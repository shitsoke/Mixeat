<?php

namespace App\Http\Controllers;

use App\Models\Branch;
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

    protected array $featuredProducts = [
        [
            'id' => 1,
            'name' => 'Chicken Meal',
            'category' => 'Meals',
            'description' => 'Delicious and satisfying chicken meal.',
            'price' => 149.00,
            'image' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&w=900&q=80',
            'badge' => 'Featured',
            'available' => true,
        ],
        [
            'id' => 2,
            'name' => 'Crispy Chicken',
            'category' => 'Chicken',
            'description' => 'Crunchy, juicy, and packed with flavor.',
            'price' => 129.00,
            'image' => 'https://images.unsplash.com/photo-1562967916-eb82221dfb92?auto=format&fit=crop&w=900&q=80',
            'badge' => 'Best Seller',
            'available' => true,
        ],
        [
            'id' => 3,
            'name' => 'Classic Burger',
            'category' => 'Burgers',
            'description' => 'A savory burger stacked with classic toppings.',
            'price' => 135.00,
            'image' => 'https://images.unsplash.com/photo-1550547660-d9450f859349?auto=format&fit=crop&w=900&q=80',
            'badge' => 'Popular',
            'available' => true,
        ],
        [
            'id' => 4,
            'name' => 'Cheesy Burger',
            'category' => 'Burgers',
            'description' => 'Loaded with melted cheese and rich sauce.',
            'price' => 155.00,
            'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=900&q=80',
            'badge' => 'Hot Pick',
            'available' => false,
        ],
        [
            'id' => 5,
            'name' => 'Carbonara',
            'category' => 'Pasta',
            'description' => 'Creamy pasta with savory and comforting flavors.',
            'price' => 169.00,
            'image' => 'https://images.unsplash.com/photo-1555949258-eb67b1ef0ceb?auto=format&fit=crop&w=900&q=80',
            'badge' => 'Featured',
            'available' => true,
        ],
        [
            'id' => 6,
            'name' => 'French Fries',
            'category' => 'Snacks',
            'description' => 'Golden and crispy side that completes any meal.',
            'price' => 79.00,
            'image' => 'https://images.unsplash.com/photo-1576107232684-1279f390859f?auto=format&fit=crop&w=900&q=80',
            'badge' => null,
            'available' => true,
        ],
        [
            'id' => 7,
            'name' => 'Iced Tea',
            'category' => 'Drinks',
            'description' => 'Refreshing iced tea served chilled.',
            'price' => 45.00,
            'image' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=900&q=80',
            'badge' => null,
            'available' => true,
        ],
        [
            'id' => 8,
            'name' => 'Soft Drink',
            'category' => 'Drinks',
            'description' => 'Classic fizzy drink with a cool finish.',
            'price' => 35.00,
            'image' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=900&q=80',
            'badge' => null,
            'available' => true,
        ],
        [
            'id' => 9,
            'name' => 'Chocolate Cake',
            'category' => 'Desserts',
            'description' => 'Rich chocolate cake for a sweet ending.',
            'price' => 120.00,
            'image' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&w=900&q=80',
            'badge' => 'Sweet Treat',
            'available' => true,
        ],
    ];

    protected array $branches = [
        [
            'name' => 'MixEat Banilad',
            'address' => 'Banilad, Cebu City',
            'opening_hours' => '8:00 AM - 9:00 PM',
            'distance' => '1.2 km away',
        ],
        [
            'name' => 'MixEat Mandaue',
            'address' => 'M. C. Briones St., Mandaue City',
            'opening_hours' => '9:00 AM - 10:00 PM',
            'distance' => '3.4 km away',
        ],
        [
            'name' => 'MixEat Talamban',
            'address' => 'Talamban, Cebu City',
            'opening_hours' => '7:30 AM - 9:30 PM',
            'distance' => '5.1 km away',
        ],
    ];

    protected array $cartItems = [
        [
            'id' => 1,
            'name' => 'Chicken Meal',
            'price' => 149.00,
            'quantity' => 2,
            'image' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&w=900&q=80',
        ],
        [
            'id' => 6,
            'name' => 'French Fries',
            'price' => 79.00,
            'quantity' => 1,
            'image' => 'https://images.unsplash.com/photo-1576107232684-1279f390859f?auto=format&fit=crop&w=900&q=80',
        ],
    ];

    protected array $orders = [
        [
            'id' => 'MX-000001',
            'date' => 'Sep 15, 2026',
            'branch' => 'MixEat Banilad',
            'total' => 427.00,
            'status' => 'Preparing',
        ],
        [
            'id' => 'MX-000002',
            'date' => 'Sep 12, 2026',
            'branch' => 'MixEat Mandaue',
            'total' => 320.00,
            'status' => 'Ready',
        ],
        [
            'id' => 'MX-000003',
            'date' => 'Sep 05, 2026',
            'branch' => 'MixEat Talamban',
            'total' => 185.00,
            'status' => 'Completed',
        ],
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

    protected function branchOptions(): array
    {
        return Branch::orderBy('id')->get()->map(fn (Branch $branch) => $branch->toArray())->all();
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
            'price' => $settings['promotion_price'] ?? '149',
        ];
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

    public function addToCart(string $id)
    {
        $product = collect($this->catalogProducts())->firstWhere('id', (int) $id);

        abort_unless($product && $product['available'], 404);

        $cart = session('cart', []);
        $cartItemKey = (string) $product['id'];

        if (isset($cart[$cartItemKey])) {
            $cart[$cartItemKey]['quantity']++;
        } else {
            $cart[$cartItemKey] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1,
                'image' => $product['image'],
            ];
        }

        session(['cart' => $cart]);

        return back()->with('cart_status', $product['name'].' added to your cart.');
    }

    public function removeFromCart(string $id)
    {
        $cart = session('cart', []);
        $removedItem = $cart[(string) $id] ?? null;

        unset($cart[(string) $id]);
        session(['cart' => $cart]);

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

        if (! isset($cart[(string) $id])) {
            return back();
        }

        if ($quantity === 0) {
            unset($cart[(string) $id]);
        } else {
            $cart[(string) $id]['quantity'] = $quantity;
        }

        session(['cart' => $cart]);

        return back();
    }

    protected function catalogProducts(): array
    {
        $branchId = (int) session('selected_branch', 0) + 1;

        return Product::with('branches')->get()->map(function (Product $product) use ($branchId) {
            $branchProduct = $product->branches->firstWhere('id', $branchId);

            return [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category,
                'description' => $product->description,
                'price' => (float) $product->price,
                'image' => $product->imageUrl(),
                'badge' => $product->badge,
                'available' => (bool) ($branchProduct?->pivot->available ?? false),
            ];
        })->all();
    }

    public function cart()
    {
        $items = array_values(session('cart', []));
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
        $items = array_values(session('cart', []));
        $subtotal = array_reduce($items, fn ($carry, $item) => $carry + ($item['price'] * $item['quantity']), 0);
        $deliveryFee = 0;
        $branches = $this->branchOptions();
        $selectedBranch = $branches[(int) session('selected_branch', 0)] ?? $branches[0];

        return view('storefront.checkout', [
            'items' => $items,
            'selectedBranch' => $selectedBranch,
            'subtotal' => $subtotal,
            'deliveryFee' => $deliveryFee,
            'total' => $subtotal + $deliveryFee,
        ]);
    }

    public function orderConfirmation()
    {
        return view('storefront.order-confirmation', [
            'order' => [
                'id' => 'MX-000001',
                'branch' => 'MixEat Banilad',
                'type' => 'Pickup',
                'time' => '25-30 minutes',
                'total' => 427.00,
            ],
        ]);
    }

    public function orders()
    {
        return view('storefront.orders', [
            'orders' => $this->orders,
        ]);
    }

    public function orderDetail(string $id)
    {
        $order = collect($this->orders)->firstWhere('id', 'MX-'.str_pad((int) $id, 6, '0', STR_PAD_LEFT)) ?? $this->orders[0];

        return view('storefront.order-detail', [
            'order' => $order,
            'statuses' => [
                'Pending',
                'Confirmed',
                'Preparing',
                'Ready',
                'Out for Delivery',
                'Completed',
                'Cancelled',
            ],
        ]);
    }

    public function login()
    {
        return view('storefront.login');
    }

    public function register()
    {
        return view('storefront.register');
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
}
