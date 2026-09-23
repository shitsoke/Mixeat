<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    private const CATEGORIES = [
        'Food Trays',
        'Food Bowls',
    ];

    public function index()
    {
        $this->ensureAdmin();

        return view('admin.products.index', [
            'products' => Product::with('branches')->latest()->get(),
            'branches' => Branch::orderBy('id')->get(),
        ]);
    }

    public function create()
    {
        $this->ensureAdmin();

        return view('admin.products.form', [
            'product' => new Product(),
            'branches' => Branch::orderBy('id')->get(), 
            'categories' => self::CATEGORIES,
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();
        $product = Product::create($this->validatedProduct($request));
        $this->syncBranchAvailability($request, $product);

        return redirect()->route('admin.products.index')->with('admin_status', 'Food added successfully.');
    }

    public function edit(Product $product)
    {
        $this->ensureAdmin();

        return view('admin.products.form', [
            'product' => $product->load('branches'),
            'branches' => Branch::orderBy('id')->get(),
            'categories' => self::CATEGORIES,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $this->ensureAdmin();
        $product->update($this->validatedProduct($request));
        $this->syncBranchAvailability($request, $product);

        return redirect()->route('admin.products.index')->with('admin_status', 'Food updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->ensureAdmin();
        $product->delete();

        return back()->with('admin_status', 'Food deleted successfully.');
    }

    public function toggleAvailability(Request $request, Product $product, Branch $branch)
    {
        $this->ensureAdmin();
        $available = $request->boolean('available');
        $product->branches()->syncWithoutDetaching([$branch->id => ['available' => $available]]);

        return back()->with('admin_status', $product->name.' is now '.($available ? 'available' : 'sold out').' at '.$branch->name.'.');
    }

    public function branches()
    {
        $this->ensureAdmin();

        return view('admin.branches.index', [
            'branches' => Branch::orderBy('id')->get(),
        ]);
    }

    public function createBranch()
    {
        $this->ensureAdmin();

        return view('admin.branches.form', ['branch' => new Branch()]);
    }

    public function storeBranch(Request $request)
    {
        $this->ensureAdmin();
        Branch::create($this->validatedBranch($request));

        return redirect()->route('admin.branches.index')->with('admin_status', 'Branch added successfully.');
    }

    public function editBranch(Branch $branch)
    {
        $this->ensureAdmin();

        return view('admin.branches.form', ['branch' => $branch]);
    }

    public function updateBranch(Request $request, Branch $branch)
    {
        $this->ensureAdmin();
        $branch->update($this->validatedBranch($request));

        return redirect()->route('admin.branches.index')->with('admin_status', 'Branch updated successfully.');
    }

    public function destroyBranch(Branch $branch)
    {
        $this->ensureAdmin();
        $branch->products()->detach();
        $branch->delete();

        return back()->with('admin_status', 'Branch deleted successfully.');
    }

    public function promotion()
    {
        $this->ensureAdmin();

        return view('admin.promotion', [
            'promotion' => $this->promotionSettings(),
        ]);
    }

    public function updatePromotion(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'], // Optional price validation
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        foreach (['label', 'title', 'price'] as $key) {
            SiteSetting::updateOrCreate(
                ['key' => 'promotion_'.$key],
                ['value' => $data[$key] ?? ''] // Safely converts null to empty string
            );
        }

        if ($request->hasFile('image')) {
            SiteSetting::updateOrCreate(
                ['key' => 'promotion_image'],
                ['value' => $request->file('image')->store('promotions', 'public')]
            );
        }

        return back()->with('admin_status', 'Promotion updated successfully.');
    }

    private function promotionSettings(): array
    {
        return SiteSetting::query()
            ->whereIn('key', ['promotion_image', 'promotion_label', 'promotion_title', 'promotion_price'])
            ->pluck('value', 'key')
            ->all();
    }

    private function validatedProduct(Request $request): array
    {
        return array_merge($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(self::CATEGORIES)],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'badge' => ['nullable', 'string', 'max:100'],
        ]), $request->hasFile('image')
            ? ['image' => $request->file('image')->store('products', 'public')]
            : []);
    }

    private function syncBranchAvailability(Request $request, Product $product): void
    {
        $availability = collect($request->input('branches', []))->mapWithKeys(fn ($value, $branchId) => [
            (int) $branchId => ['available' => (bool) $value],
        ])->all();

        $product->branches()->sync($availability);
    }

    private function validatedBranch(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'opening_hours' => ['required', 'string', 'max:255'],
            'distance' => ['required', 'string', 'max:100'],
        ]);
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->is_admin, 403);
    }
}
