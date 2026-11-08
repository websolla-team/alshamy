<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::orderBy('id', 'desc')->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::get();
        $colors = Color::get();
        $sizes = Size::get();
        return view('admin.products.create', compact('categories','sizes', 'colors'));
    }

      public function store(Request $request)
    {
        $data = $request->validate([
            'image' => 'required|image|max:1024',
            'name.*' => 'required|string',
            'description.*' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'is_featured' => 'nullable|in:1',
        ]);
        $variants = $request->validate([

            'size.*' => 'required|string',
            'quantity.*' => 'required|integer',
            'price.*' => 'required|numeric',
            'old_price.*' => 'nullable|numeric',

            'variant_images' => 'nullable|array',
            'variant_images.*' => 'nullable|array',
            'variant_images.*.*' => 'nullable|image|max:1024',
        ]);




        unset($data['imgs']);
        $fileName = Helper::storeImage($request->file('image'), 'products');
        $data['image'] = $fileName;

        try {
            DB::beginTransaction();

            $product = Product::create($data);

            // store variants
            foreach ($variants['quantity'] as $key => $quantity) {
                $product->variants()->create([
                    'size' => $variants['size'][$key],

                    'quantity' => $quantity,
                    'price' => $variants['price'][$key],
                    'old_price' => $variants['old_price'][$key],
                ]);



            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
        return redirect()->route('products.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::get();
        $colors = Color::get();
                $sizes = Size::get();

        return view('admin.products.edit', compact('product','sizes', 'categories', 'colors'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'image' => 'nullable|image',
            'name.*' => 'required|string',
            'description.*' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'is_featured' => 'nullable|in:1',
        ]);

        $variants = $request->validate([

            'size.*' => 'required|string',
            'quantity.*' => 'required|integer|min:0',
            'price.*' => 'required|numeric|min:0',
            'old_price.*' => 'nullable|numeric|min:0',
        ]);
        $data['is_featured'] = $data['is_featured'] ?? 0;



        try {
            DB::beginTransaction();

            if ($request->hasFile('image')) {
                $fileName = Helper::updateImage($request->file('image'), $product->image, 'products');
                $data['image'] = $fileName;
            }

            // Update product details
            $product->update($data);


            // Sync Variants
            $existingVariantIds = $product->variants->pluck('id')->toArray();
            $requestVariantIds = $request->input('variant_id', []);


            // Remove variants that are not in the request
            $variantsToDelete = array_diff($existingVariantIds, $requestVariantIds);
            $product->variants()->whereIn('id', $variantsToDelete)->delete();

            foreach ($variants['quantity'] as $key => $quantity) {
                $product->variants()->updateOrCreate(
                    ['id' => $requestVariantIds[$key]], // Find by ID if exists
                    [
                        'size' => $variants['size'][$key],

                        'quantity' => $quantity,
                        'price' => $variants['price'][$key],
                        'old_price' => $variants['old_price'][$key],
                    ]
                );
            }


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }


    public function destroy(Request $request, $id)
    {
        $product = Product::with('images')->findOrFail($request->id);
        $oldImage = $product->image;
        Helper::deleteImage($oldImage);

        // delete all image for this product
        foreach ($product->images as $image) {
            Helper::deleteImage($image->image);
        }
        $product->delete();
        return back();
    }
public function shoo(Request $request, $id)
{
    $product = Product::findOrFail($id);

    // Reset all to 0
    Product::query()->update(['shoo' => 0]);

    // Set the chosen one to 1
    $product->update(['shoo' => 1]);

    return redirect()->back();
}

public function getVariantPrice(Request $request)
{
    $product = Product::with('variants')
        ->find($request->product_id);

    if (!$product) {
        return response()->json([
            'price' => 0,
            'old_price' => null
        ]);
    }

    $variant = $product->variants
        ->where('size', $request->size)
        ->first();

    if (!$variant) {
        return response()->json([
            'price' => 0,
            'old_price' => null
        ]);
    }

    return response()->json([
        'price'     => $variant->price,
        'old_price' => $variant->old_price
    ]);
}

}
