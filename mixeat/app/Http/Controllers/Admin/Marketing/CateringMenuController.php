<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Models\CateringMenuItem;
use App\Models\CateringPackage;
use App\Models\PackedMealPackage;
use App\Models\PartyTray;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CateringMenuController extends Controller
{
    public function index(Request $request)
    {
        $items = CateringMenuItem::query()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = (string) $request->input('search');
                $query->where(function ($nested) use ($search): void {
                    $nested->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->input('category')))
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->input('status') === 'active'))
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.marketing.catering-menu.index', [
            'items' => $items,
            'categories' => CateringMenuItem::CATEGORIES,
            'packages' => CateringPackage::withTrashed()->latest()->get(),
            'packedMeals' => PackedMealPackage::withTrashed()->latest()->get(),
            'partyTrays' => PartyTray::withTrashed()->latest()->get(),
            'tab' => $request->input('tab', 'foods'),
        ]);
    }

    public function create()
    {
        return view('admin.marketing.catering-menu.form', [
            'item' => new CateringMenuItem(['is_active' => true]),
            'categories' => CateringMenuItem::CATEGORIES,
        ]);
    }

    public function store(Request $request)
    {
        CateringMenuItem::create($this->validated($request));

        return redirect()->route('admin.marketing.catering-menu.index')->with('success', 'Catering menu item added.');
    }

    public function edit(CateringMenuItem $cateringMenuItem)
    {
        return view('admin.marketing.catering-menu.form', [
            'item' => $cateringMenuItem,
            'categories' => CateringMenuItem::CATEGORIES,
        ]);
    }

    public function update(Request $request, CateringMenuItem $cateringMenuItem)
    {
        $data = $this->validated($request, $cateringMenuItem);
        $cateringMenuItem->update($data);

        return redirect()->route('admin.marketing.catering-menu.index')->with('success', 'Catering menu item updated.');
    }

    public function destroy(CateringMenuItem $cateringMenuItem)
    {
        $cateringMenuItem->delete();

        return back()->with('success', 'Catering menu item archived.');
    }

    public function toggle(CateringMenuItem $cateringMenuItem)
    {
        $cateringMenuItem->update(['is_active' => ! $cateringMenuItem->is_active]);

        return back()->with('success', $cateringMenuItem->name.' is now '.($cateringMenuItem->is_active ? 'active' : 'inactive').'.');
    }

    public function catalogCreate(string $type)
    {
        $this->assertType($type);
        $model = $this->modelFor($type);

        return view('admin.marketing.catering-menu.catalog-form', [
            'type' => $type,
            'item' => new $model(['is_active' => true]),
            'categories' => CateringMenuItem::CATEGORIES,
        ]);
    }

    public function catalogStore(Request $request, string $type)
    {
        $this->assertType($type);
        $model = $this->modelFor($type);
        $data = $this->catalogValidated($request, $type);
        $model::create($data);

        return redirect()->route('admin.marketing.catering-menu.index', ['tab' => $type])->with('success', ucfirst($type).' saved.');
    }

    public function catalogEdit(string $type, int $id)
    {
        $this->assertType($type);
        $item = $this->modelFor($type)::withTrashed()->findOrFail($id);

        return view('admin.marketing.catering-menu.catalog-form', [
            'type' => $type,
            'item' => $item,
            'categories' => CateringMenuItem::CATEGORIES,
        ]);
    }

    public function catalogUpdate(Request $request, string $type, int $id)
    {
        $this->assertType($type);
        $item = $this->modelFor($type)::withTrashed()->findOrFail($id);
        $item->update($this->catalogValidated($request, $type, $item));

        return redirect()->route('admin.marketing.catering-menu.index', ['tab' => $type])->with('success', ucfirst($type).' updated.');
    }

    public function catalogDestroy(string $type, int $id)
    {
        $this->assertType($type);
        $this->modelFor($type)::withTrashed()->findOrFail($id)->delete();

        return back()->with('success', ucfirst($type).' archived.');
    }

    public function catalogToggle(string $type, int $id)
    {
        $this->assertType($type);
        $item = $this->modelFor($type)::withTrashed()->findOrFail($id);
        $item->update(['is_active' => ! $item->is_active]);

        return back()->with('success', 'Availability updated.');
    }

    private function assertType(string $type): void
    {
        abort_unless(in_array($type, ['packages', 'packed-meals', 'party-trays'], true), 404);
    }

    private function modelFor(string $type): string
    {
        return match ($type) {
            'packages' => CateringPackage::class,
            'packed-meals' => PackedMealPackage::class,
            'party-trays' => PartyTray::class,
        };
    }

    private function catalogValidated(Request $request, string $type, ?object $item = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ];
        if ($type === 'packages') {
            $rules += ['code' => ['required', 'string', 'max:80', Rule::unique('catering_packages', 'code')->ignore($item?->id)], 'price' => ['required', 'numeric', 'min:0'], 'min_guests' => ['nullable', 'integer', 'min:1'], 'max_guests' => ['nullable', 'integer', 'gte:min_guests'], 'meat_count' => ['nullable', 'integer', 'min:0'], 'included_categories' => ['nullable', 'string', 'max:500'], 'included_dishes' => ['nullable', 'json']];
        } elseif ($type === 'packed-meals') {
            $rules += ['code' => ['required', 'string', 'max:80', Rule::unique('packed_meal_packages', 'code')->ignore($item?->id)], 'price' => ['required', 'numeric', 'min:0'], 'side_lines' => ['nullable', 'string', 'max:1000'], 'fixed_lines' => ['nullable', 'string', 'max:1000'], 'included_dishes' => ['nullable', 'json']];
        } else {
            $rules += ['full_price' => ['required', 'numeric', 'min:0'], 'half_price' => ['required', 'numeric', 'min:0'], 'full_size' => ['nullable', 'string', 'max:80'], 'half_size' => ['nullable', 'string', 'max:80'], 'dish_lines' => ['nullable', 'string', 'max:5000'], 'sizes' => ['nullable', 'json'], 'dishes' => ['nullable', 'json']];
        }
        $data = $request->validate($rules);
        $data['is_active'] = $request->boolean('is_active');
        if (in_array($type, ['packages', 'packed-meals'], true)) {
            $legacyIncluded = isset($data['included_dishes']) ? json_decode($data['included_dishes'], true) : null;
            $data['included_dishes'] = is_array($legacyIncluded) && ! $request->hasAny(['meat_count', 'included_categories', 'side_lines', 'fixed_lines'])
                ? $legacyIncluded
                : ($type === 'packages'
                    ? ['meat_count' => (int) ($data['meat_count'] ?? 0), 'categories' => $this->lines($data['included_categories'] ?? '', ',')]
                    : ['sides' => $this->lines($data['side_lines'] ?? ''), 'fixed' => $this->lines($data['fixed_lines'] ?? '')]);
            unset($data['meat_count'], $data['included_categories'], $data['side_lines'], $data['fixed_lines']);
        } else {
            $legacySizes = isset($data['sizes']) ? json_decode($data['sizes'], true) : null;
            $legacyDishes = isset($data['dishes']) ? json_decode($data['dishes'], true) : null;
            $data['sizes'] = [
                'full' => $data['full_size'] ?? ($legacySizes['full'] ?? 'Full'),
                'half' => $data['half_size'] ?? ($legacySizes['half'] ?? 'Half'),
            ];
            $data['dishes'] = $request->filled('dish_lines')
                ? $this->lines((string) $data['dish_lines'])
                : (is_array($legacyDishes) ? array_values($legacyDishes) : []);
            unset($data['full_size'], $data['half_size'], $data['dish_lines']);
        }
        if ($request->hasFile('image')) {
            if ($item?->image) {
                Storage::disk('public')->delete($item->image);
            }
            $data['image'] = $request->file('image')->store('catering-catalog', 'public');
        } elseif ($item) {
            unset($data['image']);
        }

        return $data;
    }

    private function lines(string $value, string $separator = "\n"): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/'.preg_quote($separator, '/').'/', $value) ?: [])));
    }

    private function validated(Request $request, ?CateringMenuItem $item = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160', Rule::unique('catering_menu_items', 'name')
                ->where(fn ($query) => $query->where('category', $request->input('category')))
                ->ignore($item?->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'category' => ['required', Rule::in(array_keys(CateringMenuItem::CATEGORIES))],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('catering-menu', 'public');
        } elseif ($item) {
            unset($data['image']);
        }

        return $data;
    }
}
