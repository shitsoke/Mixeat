<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Promotion;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Services\CateringContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class StorefrontController extends Controller
{
    private const IN_HOUSE_DELIVERY_MINIMUM = 3000;

    private const CASH_PAYMENT_MAXIMUM = 1500;

    protected array $categories = [
        'All',
        'Food Bowls',
        'Signatures',
        'Sizzling & BBQ',
        'Crispy Favorites',
        'Healthy & Veggie',
        'Drinks',
        'Desserts',
    ];

    /**
     * Retrieve cart items from active Session or fallback to 30-day Cookie.
     */
    private function getCart(): array
    {
        if (session()->has('cart')) {
            return session('cart', []);
        }

        $cookieCart = request()->cookie('mixeat_cart');
        if ($cookieCart) {
            $cart = json_decode($cookieCart, true) ?? [];
            session(['cart' => $cart]);
            return $cart;
        }

        return [];
    }

    public function home()
    {
        $menuProducts = collect($this->catalogProducts())
            ->reject(fn ($product) => $product['category'] === 'Food Trays')
            ->values()
            ->all();
        $trackedOrders = auth()->check()
            ? Order::with('branch')
                ->where('user_id', auth()->id())
                ->where('status', '!=', 'Completed')
                ->latest()
                ->take(10)
                ->get()
            : collect();

        return view('storefront.home', [
            'featuredProducts' => array_slice($menuProducts, 0, 6),
            'categories' => $this->categories,
            'branches' => $this->branchOptions(),
            'selectedBranch' => (int) session('selected_branch', 0),
            'promotion' => $this->promotionSettings(),
            'homePromotions' => Promotion::query()
                ->where('is_active', true)
                ->latest()
                ->get()
                ->map(fn (Promotion $promotion) => [
                    'label' => $promotion->label,
                    'title' => $promotion->title,
                    'image' => $promotion->image
                        ? asset('storage/'.$promotion->image)
                        : asset('images/mixeat.jpg'),
                    'details_url' => route('featured.show', $promotion),
                ])
                ->all(),
            'trackedOrders' => $trackedOrders,
        ]);
    }

    public function liveOrders()
    {
        $orders = Order::with('branch')
            ->where('user_id', auth()->id())
            ->where('status', '!=', 'Completed')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn (Order $order) => [
                'id' => $order->order_number,
                'status' => $order->status,
                'type' => $order->type,
                'branch' => $order->branch?->name ?? 'MixEat',
                'total' => number_format((float) $order->total, 2),
                'pickup_at' => $order->pickup_at?->format('M j, Y g:i A'),
                'url' => route('order-detail', (int) str_replace('MX-', '', $order->order_number)),
            ]);

        return response()->json(['orders' => $orders]);
    }

    public function menu(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $category = (string) $request->query('category', 'All');
        if ($category === 'All Bowls') {
            $category = 'Food Bowls';
        }
        $price = (string) $request->query('price', 'any');
        $availability = (string) $request->query('availability', 'all');
        $sort = (string) $request->query('sort', 'featured');

        $products = collect($this->catalogProducts())
            ->reject(fn ($product) => $product['category'] === 'Food Trays')
            ->when($search !== '', fn ($products) => $products->filter(fn ($product) => str_contains(strtolower($product['name'].' '.$product['description']), strtolower($search))))
            ->when($category === 'Best Seller', fn ($products) => $products->where('category', 'Food Bowls')->where('is_best_seller', true))
            ->when(in_array($category, ['Chicken','Pork','Beef','Fish'], true), fn ($products) => $products->where('category', 'Food Bowls')->filter(fn ($p) => strcasecmp($p['subcategory'] ?? '', $category) === 0))
            ->when($category === 'All Bowls' || $category === 'All', fn ($products) => $products->where('category', 'Food Bowls'))
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
        return view('storefront.featured', [
            'promotions' => Promotion::query()
                ->where('is_active', true)
                ->latest()
                ->get(),
        ]);
    }

    public function featuredPromotion(Promotion $promotion)
    {
        abort_unless($promotion->is_active, 404);

        return view('storefront.featured-promotion', [
            'promotion' => $promotion,
        ]);
    }

    public function catering()
    {
        return view('storefront.catering', [
            'cateringContent' => app(CateringContent::class)->get(),
            'partyTrays' => Product::query()
                ->where('category', 'Food Trays')
                ->orderBy('name')
                ->get(),
            'campaignSource' => (string) request()->query('campaign', ''),
        ]);
    }

    public function orders()
    {
        $userOrders = Order::with(['branch', 'items'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        $activeOrders = $userOrders->whereIn('status', ['Pending', 'Preparing', 'Ready for Pickup']);
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
            'type' => $order->type,
            'pickup_at' => $order->pickup_at?->format('M j, Y g:i A'),
            'delivery_method' => $order->delivery_method,
            'delivery_address' => $order->delivery_address,
            'payment_method' => $order->payment_method,
            'items' => $order->items,
        ] : [
            'id' => 'MX-'.str_pad((int) $id, 6, '0', STR_PAD_LEFT),
            'date' => now()->format('M d, Y'),
            'branch' => 'MixEat',
            'total' => 0.00,
            'status' => 'Pending',
            'type' => 'Pickup',
            'pickup_at' => null,
            'delivery_method' => null,
            'delivery_address' => null,
            'payment_method' => null,
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
        $items = $this->getCart();
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
        $items = $this->getCart();
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
            'inHouseDeliveryMinimum' => self::IN_HOUSE_DELIVERY_MINIMUM,
            'cashPaymentMaximum' => self::CASH_PAYMENT_MAXIMUM,
        ]);
    }

    public function placeOrder(Request $request)
    {
        $items = $this->getCart();
        abort_if(empty($items), 422, 'Your cart is empty.');

        $subtotal = array_reduce($items, fn ($carry, $item) => $carry + ($item['price'] * $item['quantity']), 0);
        $validated = $request->validate([
            'order_type' => ['required', 'in:Pickup,Delivery'],
            'pickup_at' => ['required_if:order_type,Pickup', 'nullable', 'date', 'after_or_equal:now'],
            'delivery_method' => ['required_if:order_type,Delivery', 'nullable', 'in:In-house,Maxim'],
            'delivery_address' => ['required_if:order_type,Delivery', 'nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:Cash,QR Code'],
            'gcash_reference_number' => ['required_if:payment_method,QR Code', 'nullable', 'string', 'max:100'],
        ]);

        if ($validated['order_type'] === 'Delivery'
            && $validated['delivery_method'] === 'In-house'
            && $subtotal < self::IN_HOUSE_DELIVERY_MINIMUM) {
            return back()
                ->withInput()
                ->withErrors(['delivery_method' => 'In-house delivery requires a minimum purchase of ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¦ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â±'.number_format(self::IN_HOUSE_DELIVERY_MINIMUM, 2).'.']);
        }

        if ($subtotal > self::CASH_PAYMENT_MAXIMUM && $validated['payment_method'] !== 'QR Code') {
            return back()
                ->withInput()
                ->withErrors(['payment_method' => 'Orders above ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¦ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â±'.number_format(self::CASH_PAYMENT_MAXIMUM, 2).' must be paid using QR Code.']);
        }

        $branchId = $this->getActiveBranchId();

        $order = DB::transaction(function () use ($items, $validated, $subtotal, $branchId) {
            $maxOrder = Order::lockForUpdate()->select('order_number')->orderByRaw('CAST(SUBSTRING(order_number, 4) AS UNSIGNED) DESC')->first();
            $nextNumber = $maxOrder && $maxOrder->order_number ? ((int) substr($maxOrder->order_number, 3)) + 1 : 1;
            $orderNumber = 'MX-'.str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => auth()->id(),
                'branch_id' => $branchId,
                'status' => 'Pending',
                'type' => $validated['order_type'],
                'total' => $subtotal,
                'pickup_at' => $validated['order_type'] === 'Pickup' ? $validated['pickup_at'] : null,
                'delivery_method' => $validated['order_type'] === 'Delivery' ? $validated['delivery_method'] : null,
                'delivery_address' => $validated['order_type'] === 'Delivery' ? $validated['delivery_address'] : null,
                'payment_method' => $validated['payment_method'],
                'gcash_reference_number' => $validated['payment_method'] === 'QR Code' ? trim($validated['gcash_reference_number']) : null,
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'addons' => $item['addons'] ?? [],
                ]);
            }

            return $order;
        });

        session()->put('latest_order', [
            'id' => $order->order_number,
            'branch' => $order->branch?->name ?? 'MixEat',
            'type' => $order->type,
            'time' => '25-30 minutes',
            'pickup_at' => $order->pickup_at?->format('M j, Y g:i A'),
            'delivery_method' => $order->delivery_method,
            'payment_method' => $order->payment_method,
            'gcash_reference_number' => $order->gcash_reference_number,
            'total' => $order->total,
        ]);

        // Clear both session and cookie on checkout complete
        session()->forget('cart');
        Cookie::queue(Cookie::forget('mixeat_cart'));

        return redirect()->route('order-confirmation');
    }

    public function orderConfirmation()
    {
        return view('storefront.order-confirmation', [
            'order' => session('latest_order', [
                'id' => 'MX-000001',
                'branch' => 'MixEat Banilad',
                'type' => 'Pickup',
                'pickup_at' => null,
                'delivery_method' => null,
                'payment_method' => 'Cash',
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

        if (! $product || ! ($product['available'] ?? true)) {
            return back()->with('cart_status', 'Sorry, this item is sold out at the selected branch.');
        }

        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
            'addons'   => ['nullable', 'array'],
        ]);

        $quantity = (int) ($validated['quantity'] ?? 1);
        $selectedAddons = $validated['addons'] ?? [];

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
        $cart = $this->getCart();

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

        // Queue cookie for 30 days (43,200 minutes)
        Cookie::queue('mixeat_cart', json_encode($cart), 43200);

        return back()->with('cart_status', $product['name'].' added to your cart.');
    }

    public function removeFromCart(string $id)
    {
        $cart = $this->getCart();
        $removedItem = $cart[$id] ?? null;

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session(['cart' => $cart]);

            if (empty($cart)) {
                Cookie::queue(Cookie::forget('mixeat_cart'));
            } else {
                Cookie::queue('mixeat_cart', json_encode($cart), 43200);
            }
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

        $cart = $this->getCart();

        if (! isset($cart[$id])) {
            return back();
        }

        if ($quantity === 0) {
            unset($cart[$id]);
        } else {
            $cart[$id]['quantity'] = $quantity;
        }

        session(['cart' => $cart]);

        if (empty($cart)) {
            Cookie::queue(Cookie::forget('mixeat_cart'));
        } else {
            Cookie::queue('mixeat_cart', json_encode($cart), 43200);
        }

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
                'telephone_number' => $branch->telephone_number,
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
                'subcategory' => $product->subcategory,
                'description' => $product->description,
                'price' => (float) $product->price,
                'image' => method_exists($product, 'imageUrl') ? $product->imageUrl() : asset('storage/'.$product->image),
                'badge' => $product->badge,
                'is_best_seller' => (bool) $product->is_best_seller,
            ];
        })->all();
    }
}







