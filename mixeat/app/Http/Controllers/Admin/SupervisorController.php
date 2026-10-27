<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupervisorController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        abort_unless($user && $user->isSupervisor() && $user->branch_id, 403, 'Unauthorized access or no branch assigned.');

        $branch = $user->branch;

        // Fetch ALL products and eager-load branch pivot data for this branch
        $products = Product::with(['branches' => function ($query) use ($branch) {
            $query->where('branches.id', $branch->id);
        }])->orderBy('name')->get();

        // Fetch orders for this branch
        $orders = Order::where('branch_id', $branch->id)
            ->latest()
            ->get();

        // Calculate Daily Summary Metrics using direct SQL date filtering (Asia/Manila)
        $today = Carbon::today('Asia/Manila');
        $todayQuery = Order::where('branch_id', $branch->id)->whereDate('created_at', $today);

        $dailySummary = [
            'total_orders'    => (clone $todayQuery)->count(),
            'preparing_count' => (clone $todayQuery)->where('status', 'Preparing')->count(),
            'ready_count'     => (clone $todayQuery)->where('status', 'Ready for Pickup')->count(),
            'completed_count' => (clone $todayQuery)->where('status', 'Completed')->count(),
            'declined_count'  => (clone $todayQuery)->where('status', 'Declined')->count(),
            'total_revenue'   => (clone $todayQuery)->whereIn('status', ['Preparing', 'Ready for Pickup', 'Completed'])->sum('total'),
        ];

        return view('admin.supervisors.index', compact('branch', 'products', 'orders', 'dailySummary'));
    }

    public function liveOrders(Request $request)
    {
        $user = $request->user();
        abort_unless($user && $user->isSupervisor() && $user->branch_id, 403, 'Unauthorized access or no branch assigned.');

        $branch = $user->branch;
        $orders = Order::where('branch_id', $branch->id)->latest()->get();
        $todayQuery = Order::where('branch_id', $branch->id)
            ->whereDate('created_at', Carbon::today('Asia/Manila'));

        $dailySummary = [
            'total_orders' => (clone $todayQuery)->count(),
            'preparing_count' => (clone $todayQuery)->where('status', 'Preparing')->count(),
            'ready_count' => (clone $todayQuery)->where('status', 'Ready for Pickup')->count(),
            'total_revenue' => (clone $todayQuery)->whereIn('status', ['Preparing', 'Ready for Pickup', 'Completed'])->sum('total'),
        ];

        return response()->json([
            'orders_html' => view('admin.supervisors.partials.order-rows', compact('orders'))->render(),
            'daily_summary' => $dailySummary,
        ]);
    }

    public function exportDailySales(Request $request): StreamedResponse
    {
        $user = $request->user();

        abort_unless($user && $user->isSupervisor() && $user->branch_id, 403, 'Unauthorized access or no branch assigned.');

        $branch = $user->branch;
        $today = Carbon::today('Asia/Manila');

        // Fetch today's orders along with their saved items from order_items table
        $orders = Order::where('branch_id', $branch->id)
            ->whereDate('created_at', $today)
            ->with('items')
            ->latest()
            ->get();

        $fileName = 'Daily_Sales_Report_' . Str::slug($branch->name) . '_' . $today->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($orders, $branch, $today) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for Microsoft Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Report Header (Explicit Asia/Manila Timezone)
            fputcsv($file, ['MIXEAT DAILY SALES REPORT']);
            fputcsv($file, ['Branch Location:', $branch->name]);
            fputcsv($file, ['Report Date:', $today->format('F d, Y')]);
            fputcsv($file, ['Generated At:', now('Asia/Manila')->format('h:i A')]);
            fputcsv($file, []); // Blank Row

            // Column Titles
            fputcsv($file, [
                'Order Number',
                'Time Placed',
                'Customer ID',
                'Order Type',
                'Ordered Items',
                'Status',
                'Total Amount (PHP)',
            ]);

            $totalRevenue = 0;

            foreach ($orders as $order) {
                $timePlaced = $order->created_at 
                    ? $order->created_at->setTimezone('Asia/Manila')->format('h:i A') 
                    : 'N/A';

                // Format item names, quantities, and add-ons: "2x Pork Binagoongan (+ Soft Drink), 1x Extra Rice"
                $itemsList = $order->items->map(function ($item) {
                    $details = "{$item->quantity}x {$item->product_name}";
                    if (!empty($item->addons) && is_array($item->addons)) {
                        $details .= " (+ " . implode(', ', $item->addons) . ")";
                    }
                    return $details;
                })->implode('; ');

                fputcsv($file, [
                    $order->order_number,
                    $timePlaced,
                    $order->user_id ?? 'Guest',
                    $order->type ?? 'Pickup',
                    $itemsList ?: 'N/A',
                    $order->status,
                    number_format($order->total, 2, '.', ''),
                ]);

                if ($order->status !== 'Declined') {
                    $totalRevenue += $order->total;
                }
            }

            // Report Summary Footer
            fputcsv($file, []); // Blank Row
            fputcsv($file, ['SUMMARY METRICS']);
            fputcsv($file, ['Total Orders Placed Today:', $orders->count()]);
            fputcsv($file, ['Completed Orders:', $orders->where('status', 'Completed')->count()]);
            fputcsv($file, ['Declined Orders:', $orders->where('status', 'Declined')->count()]);
            fputcsv($file, ['Total Estimated Revenue (PHP):', number_format($totalRevenue, 2, '.', '')]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function acceptOrder(Order $order)
    {
        $user = auth()->user();
        abort_unless($user && $user->isSupervisor() && $user->branch_id === $order->branch_id, 403);

        $order->update(['status' => 'Preparing']);

        return back()->with('admin_status', "Order {$order->order_number} accepted and set to Preparing.");
    }

        public function markReadyForPickup(Order $order)
    {
        $user = auth()->user();
        abort_unless($user && $user->isSupervisor() && $user->branch_id === $order->branch_id, 403);

        $previousStatus = $order->status;
        $order->update(['status' => 'Ready for Pickup']);
        $order->loadMissing('user', 'branch');
        if ($order->user && $order->status !== $previousStatus) {
            $order->user->notify(new OrderStatusUpdated($order, $previousStatus));
        }

        return back()->with('admin_status', "Order {$order->order_number} marked as Ready for Pickup.");
    }

        public function markCompleted(Order $order)
    {
        $user = auth()->user();
        abort_unless($user && $user->isSupervisor() && $user->branch_id === $order->branch_id, 403);

        $previousStatus = $order->status;
        $order->update(['status' => 'Completed']);
        $order->loadMissing('user', 'branch');
        if ($order->user && $order->status !== $previousStatus) {
            $order->user->notify(new OrderStatusUpdated($order, $previousStatus));
        }

        return back()->with('admin_status', "Order {$order->order_number} marked as Completed.");
    }

    public function declineOrder(Order $order)
    {
        $user = auth()->user();
        abort_unless($user && $user->isSupervisor() && $user->branch_id === $order->branch_id, 403);

        $order->update(['status' => 'Declined']);

        return back()->with('admin_status', "Order {$order->order_number} has been declined.");
    }

    public function toggleStatus(Request $request, Product $product)
    {
        $user = $request->user();
        abort_unless($user && $user->isSupervisor() && $user->branch_id, 403);

        $isAvailable = $request->boolean('available');

        $user->branch->products()->syncWithoutDetaching([
            $product->id => [
                'available' => $isAvailable,
            ]
        ]);

        return back()->with('admin_status', $product->name . ' status updated.');
    }

    public function notifications(Request $request)
    {
        $user = $request->user();
        abort_unless($user && $user->isSupervisor() && $user->branch_id, 403);

        $notifications = $user->unreadNotifications()
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'data' => $notification->data,
                    'created_at' => $notification->created_at ? $notification->created_at->diffForHumans() : null,
                    'read_at' => $notification->read_at,
                ];
            });

        return response()->json([
            'count' => $user->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }

    public function markNotificationRead(Request $request, $id)
    {
        $user = $request->user();
        abort_unless($user && $user->isSupervisor() && $user->branch_id, 403);

        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function viewNotificationOrder(Request $request, $id)
    {
        $user = $request->user();
        abort_unless($user && $user->isSupervisor() && $user->branch_id, 403);

        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        $orderId = $notification->data['order_id'] ?? null;
        if (!$orderId) {
            abort(404);
        }

        $order = Order::with('items.product', 'user')->findOrFail($orderId);
        abort_unless($order->branch_id === $user->branch_id, 403);

        return redirect()->route('supervisors.index')->with('admin_status', "Viewing order {$order->order_number}");
    }
}
