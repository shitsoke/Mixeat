<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    protected array $categories = [
        'All',
        'Food Trays',
        'Food Bowls',
    ];

    public function home()
    {
        return view('storefront.home', [
            'featuredProducts' => array_slice($this->catalogProducts(), 0, 6),
            'categories' => $this->categories,
            'branches' => $this->branchOptions(),
            'selectedBranch' => (int) session('selected_branch', 0),
            'promotion' => $this->promotionSettings(),
        ]);
    }

    public function menu(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $category = (string) $request->query('category', 'All');
        $price = (string) $request->query('price', 'any');
        $availability = (string) $request->query('availability', 'all');
        $sort = (string) $request->query('sort', 'featured');

        $products = collect($this->catalogProducts())
            ->when($search !== '', fn ($products) => $products->filter(fn ($product) => str_contains(strtolower($product['name'].' '.$product['description']), strtolower($search))))
            ->when($category !== 'All' && in_array($category, $this->categories, true), fn ($products) => $products->where('category', $category))
            ->when($price === 'under-100', fn ($products) => $products->where('price', '<', 100))
            ->when($price === '100-200', fn ($products) => $products->whereBetween('price', [100, 200]))
            ->when($price === '200-300', fn ($products) => $products->whereBetween('price', [200, 300]))
            ->when($availability === 'available', fn ($products) => $products->where('available', true))
            ->when($availability === 'sold-out', fn ($products) => $products->where('available', false));

        $products = match ($sort) {
            'price-low' => $products->sortBy('price'),
            'price-high' => $products->sortByDesc('price'),
            'newest' => $products->sortByDesc('id'),
            default => $products,
        };

        return view('storefront.menu', [
            'products' => $products->values()->all(),
            'categories' => $this->categories,
            'filters' => compact('search', 'category', 'price', 'availability', 'sort'),
        ]);
    }

    public function featured()
    {
        return view('storefront.home', [
            'featuredProducts' => $this->catalogProducts(),
            'categories' => $this->categories,
            'branches' => $this->branchOptions(),
            'selectedBranch' => (int) session('selected_branch', 0),
            'promotion' => $this->promotionSettings(),
        ]);
    }

    public function orders()
    {
        // Fetch orders placed by the current authenticated user
        $userOrders = Order::with(['branch', 'items'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        // Active Orders (Real-time status tracking for customer dashboard)
        $activeOrders = $userOrders->whereIn('status', ['Pending', 'Preparing', 'Ready for Pickup']);

        // Order History (Past closed transactions)
        $orderHistory = $userOrders->whereIn('status', ['Completed', 'Declined', 'Cancelled']);

        return view('storefront.orders', compact('activeOrders', 'orderHistory'));
    }

    public function orderDetail(string $id)
    {
        $order = Order::with(['branch', 'items'])
            ->where('order_number', 'MX-'.str_pad((int) $id, 6, '0', STR_PAD_LEFT))
            ->first();

        $formattedOrder = $order ? [
            'id' => $order->order_number,
            'date' => $order->created_at ? $order->created_at->format('M d, Y') : 'Just now',
            'branch' => $order->branch?->name ?? 'MixEat',
            'total' => (float) $order->total,
            'status' => $order->status,
            'items' => $order->items,
        ] : [
            'id' => 'MX-'.str_pad((int) $id, 6, '0', STR_PAD_LEFT),
            'date' => now()->format('M d, Y'),
            'branch' => 'MixEat',
            'total' => 0.00,
            'status' => 'Pending',
            'items' => [],
        ];

        return view('storefront.order-detail', [
            'order' => $formattedOrder,
            'statuses' => [
                'Pending',
                'Preparing',
                'Ready for Pickup',
                'Completed',
                'Cancelled',
                'Declined',
            ],
        ]);
    }

    public function cart()
    {
        // Preserve string keys from session cart to allow variant key removal
        $items = session('cart', []);
        $subtotal = array_reduce($items, fn ($carry, $item) => $carry + ($item['price'] * $item['quantity']), 0);
        $deliveryFee = 0;

        return view('storefront.cart', [
            'items' => $items,
            'subtotal' => $subtotal,
            'deliveryFee' => $deliveryFee,
            'total' => $subtotal + $deliveryFee,
        ]);
    }

    public function checkout()
    {
        $items = session('cart', []);
        $subtotal = array_reduce($items, fn ($carry, $item) => $carry + ($item['price'] * $item['quantity']), 0);
        $deliveryFee = 0;
        $branches = $this->branchOptions();
        $selectedBranch = $branches[(int) session('selected_branch', 0)] ?? ($branches[0] ?? []);

        return view('storefront.checkout', [
            'items' => $items,
            'selectedBranch' => $selectedBranch,
            'subtotal' => $subtotal,
            'deliveryFee' => $deliveryFee,
            'total' => $subtotal + $deliveryFee,
        ]);
    }

    public function placeOrder()
    {
        $items = session('cart', []);
        abort_if(empty($items), 422, 'Your cart is empty.');

        $subtotal = array_reduce($items, fn ($carry, $item) => $carry + ($item['price'] * $item['quantity']), 0);
        $branchId = $this->getActiveBranchId();

        $orderCount = Order::count() + 1;
        $orderNumber = 'MX-'.str_pad($orderCount, 6, '0', STR_PAD_LEFT);

        // 1. Save main order record
        $order = Order::create([
            'order_number' => $orderNumber,
            'user_id' => auth()->id(),
            'branch_id' => $branchId,
            'status' => 'Pending',
            'type' => 'Pickup',
            'total' => $subtotal,
        ]);

        // 2. Save each ordered product and add-ons to order_items table
        foreach ($items as $item) {
            $order->items()->create([
                'product_name' => $item['name'],
                'quantity'     => $item['quantity'],
                'price'        => $item['price'],
                'addons'       => $item['addons'] ?? [],
            ]);
        }

        session()->put('latest_order', [
            'id' => $order->order_number,
            'branch' => $order->branch?->name ?? 'MixEat',
            'type' => $order->type,
            'time' => '25-30 minutes',
            'total' => $order->total,
        ]);

        session()->forget('cart');

        return redirect()->route('order-confirmation');
    }

    public function orderConfirmation()
    {
        return view('storefront.order-confirmation', [
            'order' => session('latest_order', [
                'id' => 'MX-000001',
                'branch' => 'MixEat Banilad',
                'type' => 'Pickup',
                'time' => '25-30 minutes',
                'total' => 0.00,
            ]),
        ]);
    }

    public function product(string $id)
    {
        $catalog = $this->catalogProducts();
        $product = collect($catalog)->firstWhere('id', (int) $id) ?? $catalog[0];

        return view('storefront.product', [
            'product' => $product,
            'categories' => $this->categories,
        ]);
    }

    public function branches()
    {
        return view('storefront.branches', [
            'branches' => $this->branchOptions(),
            'selectedBranch' => session('selected_branch', 0),
        ]);
    }

    public function selectBranch(string $branch)
    {
        $branchIndex = (int) $branch;
        $branches = $this->branchOptions();

        abort_unless(isset($branches[$branchIndex]), 404);

        session(['selected_branch' => $branchIndex]);

        return back()->with('branch_status', $branches[$branchIndex]['name'].' selected.');
    }

    public function selectBranchFromDropdown(Request $request)
    {
        $branchIndex = $request->validate([
            'branch' => ['required', 'integer', 'min:0'],
        ])['branch'];
        $branches = $this->branchOptions();

        abort_unless(isset($branches[$branchIndex]), 404);

        session(['selected_branch' => $branchIndex]);

        return back()->with('branch_status', $branches[$branchIndex]['name'].' selected.');
    }

    public function addToCart(Request $request, string $id)
    {
        $product = collect($this->catalogProducts())->firstWhere('id', (int) $id);

        if (! $product || ! $product['available']) {
            return back()->with('cart_status', 'Sorry, this item is sold out at the selected branch.');
        }

        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
            'addons'   => ['nullable', 'array'],
        ]);

        $quantity = (int) ($validated['quantity'] ?? 1);
        $selectedAddons = $validated['addons'] ?? [];

        // Add-on price dictionary
        $addonPrices = [
            'Extra Rice'  => 20,
            'Extra Sauce' => 10,
            'Soft Drink'  => 30,
        ];

        $unitPrice = (float) $product['price'];
        $appliedAddons = [];

        foreach ($selectedAddons as $addon) {
            if (isset($addonPrices[$addon])) {
                $unitPrice += $addonPrices[$addon];
                $appliedAddons[] = $addon;
            }
        }

        sort($appliedAddons);
        $cart = session('cart', []);

        // Unique hash key per variant item
        $cartItemKey = (string) $product['id'] . '_' . md5(implode(',', $appliedAddons));

        if (isset($cart[$cartItemKey])) {
            $cart[$cartItemKey]['quantity'] += $quantity;
        } else {
            $cart[$cartItemKey] = [
                'id'       => $product['id'],
                'name'     => $product['name'],
                'price'    => $unitPrice,
                'quantity' => $quantity,
                'image'    => $product['image'],
                'addons'   => $appliedAddons,
            ];
        }

        session(['cart' => $cart]);

        return back()->with('cart_status', $product['name'].' added to your cart.');
    }

    public function removeFromCart(string $id)
    {
        $cart = session('cart', []);
        $removedItem = $cart[$id] ?? null;

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session(['cart' => $cart]);
        }

        return back()->with('cart_status', $removedItem
            ? $removedItem['name'].' removed from your cart.'
            : 'That item was not in your cart.');
    }

    public function updateCartQuantity(Request $request, string $id)
    {
        $quantity = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ])['quantity'];

        $cart = session('cart', []);

        if (! isset($cart[$id])) {
            return back();
        }

        if ($quantity === 0) {
            unset($cart[$id]);
        } else {
            $cart[$id]['quantity'] = $quantity;
        }

        session(['cart' => $cart]);

        return back();
    }

    public function profile()
    {
        $user = auth()->user();

        return view('storefront.profile', [
            'user' => $user,
        ]);
    }

    public function about()
    {
        return view('storefront.about');
    }

    public function contact()
    {
        return view('storefront.contact');
    }

    protected function branchOptions(): array
    {
        return Branch::orderBy('id')->get()->map(function (Branch $branch) {
            return [
                'id' => $branch->id,
                'name' => $branch->name,
                'address' => $branch->address,
                'opening_hours' => $branch->opening_hours,
                'distance' => $branch->distance ?? '1.0 km away',
            ];
        })->all();
    }

    protected function getActiveBranchId(): int
    {
        $branches = $this->branchOptions();
        $selectedIndex = (int) session('selected_branch', 0);

        return $branches[$selectedIndex]['id'] ?? ($branches[0]['id'] ?? 1);
    }

    protected function promotionSettings(): array
    {
        $settings = SiteSetting::query()
            ->whereIn('key', ['promotion_image', 'promotion_label', 'promotion_title', 'promotion_price'])
            ->pluck('value', 'key')
            ->all();

        return [
            'image' => ! empty($settings['promotion_image']) ? asset('storage/'.$settings['promotion_image']) : 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=1200&q=80',
            'label' => $settings['promotion_label'] ?? "Today's favorite",
            'title' => $settings['promotion_title'] ?? 'Chicken Meal Combo',
            'price' => isset($settings['promotion_price']) && $settings['promotion_price'] !== '' ? $settings['promotion_price'] : null,
        ];
    }

    protected function catalogProducts(): array
    {
        $branchId = $this->getActiveBranchId();

        return Product::with(['branches' => function ($query) use ($branchId) {
            $query->where('branches.id', $branchId);
        }])->get()->map(function (Product $product) {
            $branchPivot = $product->branches->first()?->pivot;

            $availability = true;
            if ($branchPivot) {
                if (isset($branchPivot->available)) {
                    $availability = (bool) $branchPivot->available;
                } elseif (isset($branchPivot->is_available)) {
                    $availability = (bool) $branchPivot->is_available;
                }
            }

            return [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category,
                'description' => $product->description,
                'price' => (float) $product->price,
                'image' => method_exists($product, 'imageUrl') ? $product->imageUrl() : asset('storage/'.$product->image),
                'badge' => $product->badge,
                'available' => $availability,
            ];
        })->all();
    }
}