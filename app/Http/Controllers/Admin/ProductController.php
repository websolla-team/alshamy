<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'variants'])->orderBy('id', 'desc')->get();
        $importedProductIds = Schema::hasTable('menu_import_links')
            ? DB::table('menu_import_links')->pluck('product_id')->flip()
            : collect();

        return view('admin.products.index', compact('products', 'importedProductIds'));
    }

    public function create()
    {
        $categories = Category::get();
        $sizes = Size::get();

        return view('admin.products.create', compact('categories', 'sizes'));
    }

    public function store(Request $request)
    {
        [$data, $variants] = $this->validatedProduct($request);
        $newImage = null;

        try {
            if ($request->hasFile('image')) {
                $newImage = Helper::storeImage($request->file('image'), 'products');
            }
            $data['image'] = $newImage ?? '/logo/logo.png';

            DB::transaction(function () use ($data, $variants) {
                $product = Product::create($data);
                $this->saveVariants($product, $variants);
            });
        } catch (\Throwable $exception) {
            if ($newImage !== null) {
                Helper::deleteImage($newImage);
            }
            throw $exception;
        }

        return redirect()->route('products.index')->with('success', 'تمت إضافة الصنف.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $product = Product::with('variants')->findOrFail($id);
        $categories = Category::get();
        $sizes = Size::get();

        return view('admin.products.edit', compact('product', 'sizes', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        [$data, $variants] = $this->validatedProduct($request, $product);
        $oldImage = $product->image;
        $newImage = null;

        try {
            if ($request->hasFile('image')) {
                $newImage = Helper::storeImage($request->file('image'), 'products');
                $data['image'] = $newImage;
            }

            DB::transaction(function () use ($product, $data, $variants) {
                $product->update($data);
                $this->saveVariants($product, $variants);
            });
        } catch (\Throwable $exception) {
            if ($newImage !== null) {
                Helper::deleteImage($newImage);
            }
            throw $exception;
        }

        if ($newImage !== null && str_starts_with($oldImage, '/storage/')) {
            Helper::deleteImage($oldImage);
        }

        return redirect()->route('products.index')->with('success', 'تم تحديث الصنف.');
    }

    private function validatedProduct(Request $request, ?Product $product = null): array
    {
        $idRule = $product
            ? ['nullable', 'integer', 'distinct', Rule::exists('variants', 'id')->where('product_id', $product->id)]
            : ['prohibited'];

        $validator = Validator::make($request->all(), [
            'image' => ['nullable', 'image', 'max:1024'],
            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.ar' => ['nullable', 'string'],
            'description.en' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'is_featured' => ['nullable', 'in:1'],
            'variants' => ['required', 'array', 'min:1'],
            'variants.*' => ['required', 'array'],
            'variants.*.id' => $idRule,
            'variants.*.size' => ['required', 'string', 'max:100'],
            'variants.*.quantity' => ['required', 'integer', 'min:0'],
            'variants.*.price' => ['required', 'numeric', 'min:0.01', 'decimal:0,2'],
            'variants.*.old_price' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
        ]);

        $existing = $product ? $product->variants()->get()->keyBy('id') : collect();
        $validator->after(function ($validator) use ($request, $existing) {
            $seen = [];
            $submittedIds = [];
            $rows = $request->input('variants', []);

            foreach (is_array($rows) ? $rows : [] as $index => $variant) {
                if (! is_array($variant)) {
                    continue;
                }
                $id = $variant['id'] ?? null;
                $saved = $id ? $existing->get((int) $id) : null;
                if ($saved) {
                    $submittedIds[] = $saved->id;
                }
                $size = $variant['size'] ?? null;
                if (is_string($size) && trim($size) !== '') {
                    $key = mb_strtolower(trim($size)).':'.($saved?->color_id ?? 'none');
                    if (isset($seen[$key])) {
                        $validator->errors()->add("variants.$index.size", 'الحجم مكرر لنفس الصنف.');
                    }
                    $seen[$key] = true;
                }
                $oldPrice = $variant['old_price'] ?? null;
                if (is_numeric($oldPrice) && (float) $oldPrice > 0
                    && is_numeric($variant['price'] ?? null)
                    && (float) $oldPrice <= (float) $variant['price']) {
                    $validator->errors()->add("variants.$index.old_price", 'السعر قبل الخصم لازم يكون أكبر من السعر الحالي.');
                }
            }

            // Missing variants stay in the database. Prevent new rows from colliding with them.
            foreach ($existing as $saved) {
                if (in_array($saved->id, $submittedIds, true)) {
                    continue;
                }
                $key = mb_strtolower(trim($saved->size)).':'.($saved->color_id ?? 'none');
                if (isset($seen[$key])) {
                    $validator->errors()->add('variants', 'فيه حجم بنفس الاسم محفوظ بالفعل؛ عدّله بدل إضافة واحد مكرر.');
                }
            }
        });

        $validated = $validator->validate();
        $name = $validated['name'];
        // Checkout expects an English key. Keep the source Arabic name when no translation exists.
        $name['en'] = $name['en'] ?? $name['ar'];
        $description = $validated['description'] ?? [];
        $data = [
            'name' => $name,
            'description' => [
                'ar' => $description['ar'] ?? '',
                'en' => $description['en'] ?? '',
            ],
            'category_id' => $validated['category_id'],
            'is_featured' => $validated['is_featured'] ?? 0,
        ];

        return [$data, $validated['variants']];
    }

    private function saveVariants(Product $product, array $variants): void
    {
        foreach ($variants as $variant) {
            $values = [
                'size' => trim($variant['size']),
                'quantity' => (int) $variant['quantity'],
                'price' => $variant['price'],
                'old_price' => isset($variant['old_price']) && (float) $variant['old_price'] > 0
                    ? $variant['old_price'] : null,
            ];

            if (! empty($variant['id'])) {
                $product->variants()->whereKey($variant['id'])->firstOrFail()->update($values);
            } else {
                $product->variants()->create($values + ['color_id' => null]);
            }
        }
    }

    public function destroy(Request $request, $id)
    {
        $imagesToDelete = DB::transaction(function () use ($request) {
            $product = Product::with('images')->lockForUpdate()->findOrFail($request->id);

            if (
                DB::table('menu_import_links')->where('product_id', $product->id)->exists()
                || DB::table('order_details')->where('product_id', $product->id)->exists()
            ) {
                return null;
            }

            $images = $product->images->pluck('image')->prepend($product->image)->all();
            $product->delete();

            return $images;
        });

        if ($imagesToDelete === null) {
            return back()->withErrors([
                'product' => 'لا يمكن حذف الصنف لأنه مرتبط بالمنيو المستورد أو بطلبات محفوظة.',
            ]);
        }

        foreach ($imagesToDelete as $image) {
            Helper::deleteImage($image);
        }

        return back();
    }

    public function shoo(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        Product::query()->update(['shoo' => 0]);
        $product->update(['shoo' => 1]);

        return redirect()->back();
    }

    public function getVariantPrice(Request $request)
    {
        $product = Product::with('variants')->find($request->product_id);
        if (! $product) {
            return response()->json(['price' => 0, 'old_price' => null]);
        }
        $variant = $product->variants->where('size', $request->size)->first();
        if (! $variant) {
            return response()->json(['price' => 0, 'old_price' => null]);
        }

        return response()->json([
            'price' => $variant->price,
            'old_price' => $variant->old_price,
        ]);
    }
}
