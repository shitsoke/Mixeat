<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Promotion;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    private const CATEGORIES = [
        'Food Trays',
        'Food Bowls',
        'Drinks',
        'Desserts',
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

    public function commissaryIndex()
    {
        $branch = $this->ensureCommissarySupervisor();

        return view('admin.products.index', [
            'products' => Product::whereHas('branches', fn ($query) => $query->whereKey($branch->id))
                ->with(['branches' => fn ($query) => $query->whereKey($branch->id)])
                ->latest()
                ->get(),
            'branches' => collect([$branch]),
            'manageRoute' => 'admin.supervisors.foods',
            'branchScoped' => true,
        ]);
    }

    public function commissaryCreate()
    {
        $branch = $this->ensureCommissarySupervisor();

        return view('admin.products.form', [
            'product' => new Product(),
            'branches' => collect([$branch]),
            'categories' => self::CATEGORIES,
            'manageRoute' => 'admin.supervisors.foods',
            'branchScoped' => true,
        ]);
    }

    public function commissaryStore(Request $request)
    {
        $branch = $this->ensureCommissarySupervisor();
        $data = $this->validatedProduct($request);
        $product = Product::create($data);
        $product->branches()->syncWithoutDetaching([
            $branch->id => ['available' => true, 'price' => $data['price']],
        ]);

        return redirect()->route('admin.supervisors.foods.index')->with('admin_status', 'Food added successfully for MixEat Commissary.');
    }

    public function commissaryEdit(Product $product)
    {
        $branch = $this->ensureCommissarySupervisor();
        abort_unless($product->branches()->whereKey($branch->id)->exists(), 404);

        return view('admin.products.form', [
            'product' => $product->load(['branches' => fn ($query) => $query->whereKey($branch->id)]),
            'branches' => collect([$branch]),
            'categories' => self::CATEGORIES,
            'manageRoute' => 'admin.supervisors.foods',
            'branchScoped' => true,
        ]);
    }

    public function commissaryUpdate(Request $request, Product $product)
    {
        $branch = $this->ensureCommissarySupervisor();
        abort_unless($product->branches()->whereKey($branch->id)->exists(), 404);
        $data = $this->validatedProduct($request);
        $price = $data['price'];
        unset($data['price']);
        $product->update($data);
        $product->branches()->updateExistingPivot($branch->id, [
            'price' => $price,
        ]);

        return redirect()->route('admin.supervisors.foods.index')->with('admin_status', 'Food updated successfully for MixEat Commissary.');
    }

    public function commissaryDestroy(Product $product)
    {
        $branch = $this->ensureCommissarySupervisor();
        abort_unless($product->branches()->whereKey($branch->id)->exists(), 404);

        $otherBranches = $product->branches()->where('branches.id', '!=', $branch->id)->exists();
        if ($otherBranches) {
            $product->branches()->detach($branch->id);

            return back()->with('admin_status', 'Shared food was archived from MixEat Commissary without affecting other branches.');
        }

        $product->delete();

        return back()->with('admin_status', 'Food deleted successfully.');
    }

    public function commissaryToggleAvailability(Request $request, Product $product)
    {
        $branch = $this->ensureCommissarySupervisor();
        abort_unless($product->branches()->whereKey($branch->id)->exists(), 404);
        $available = $request->boolean('available');
        $product->branches()->updateExistingPivot($branch->id, ['available' => $available]);

        return back()->with('admin_status', $product->name.' is now '.($available ? 'available' : 'sold out').' at MixEat Commissary.');
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

    public function promotions()
    {
        $this->ensureMarketing();

        return view('admin.promotions.index', [
            'promotions' => Promotion::query()->latest()->get(),
        ]);
    }

    public function createPromotion()
    {
        $this->ensureMarketing();

        return view('admin.promotions.form', ['promotion' => new Promotion()]);
    }

    public function storePromotion(Request $request)
    {
        $this->ensureMarketing();
        $data = $this->validatedPromotion($request, true);
        $data['image'] = $request->file('image')->store('promotions', 'public');
        if ($data['image'] === false) {
            throw new \RuntimeException('The promotion image could not be saved.');
        }

        Promotion::create($data);

        return redirect()->route('admin.promotions.index')->with('admin_status', 'Promotion posted successfully.');
    }

    public function editPromotion(Promotion $promotion)
    {
        $this->ensureMarketing();

        return view('admin.promotions.form', ['promotion' => $promotion]);
    }

    public function updatePromotionPost(Request $request, Promotion $promotion)
    {
        $this->ensureMarketing();
        $data = $this->validatedPromotion($request, false);
        $oldImage = $promotion->image;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('promotions', 'public');
            if ($data['image'] === false) {
                throw new \RuntimeException('The promotion image could not be saved.');
            }
        }

        $promotion->update($data);

        if ($request->hasFile('image') && $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('admin.promotions.index')->with('admin_status', 'Promotion updated successfully.');
    }

    public function destroyPromotion(Promotion $promotion)
    {
        $this->ensureMarketing();
        $image = $promotion->image;
        $promotion->delete();

        if ($image) {
            Storage::disk('public')->delete($image);
        }

        return back()->with('admin_status', 'Promotion deleted successfully.');
    }

    public function updatePromotion(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'], // Optional price validation
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:40960'],
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
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:40960'],
            'badge' => ['nullable', 'string', 'max:100'],
            'subcategory' => ['nullable', 'string', 'max:100'],
            'is_best_seller' => ['nullable', 'boolean'],
        ]), array_merge($request->hasFile('image')
            ? ['image' => $request->file('image')->store('products', 'public')]
            : [], ['is_best_seller' => $request->boolean('is_best_seller')]));
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
            'telephone_number' => ['nullable', 'string', 'max:30'],
        ]);
    }

    private function validatedPromotion(Request $request, bool $requireImage): array
    {
        $data = $request->validate([
            'label' => ['nullable', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'details' => ['required', 'string', 'max:10000'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'button_text' => ['nullable', 'string', 'max:60', 'required_with:button_url'],
            'button_url' => ['nullable', 'url:http,https', 'max:2048', 'required_with:button_text'],
            'image' => [$requireImage ? 'required' : 'nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:40960'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function ensureMarketing(): void
    {
        abort_unless(auth()->check() && (auth()->user()->isMarketing() || auth()->user()->is_admin), 403);
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->is_admin, 403);
    }

    private function ensureCommissarySupervisor(): Branch
    {
        $user = auth()->user();
        abort_unless($user && $user->isSupervisor() && ($user->is_active ?? true) && $user->branch_id, 403);

        $branch = Branch::query()
            ->whereKey($user->branch_id)
            ->where('name', 'MixEat Commissary')
            ->first();

        abort_unless($branch, 403, 'Only the MixEat Commissary supervisor can manage foods.');

        return $branch;
    }
}
