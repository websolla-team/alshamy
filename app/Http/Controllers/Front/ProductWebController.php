<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryType;
use App\Models\Color;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;

class ProductWebController extends Controller
{
    public $paginate = 20;
    protected function getCommonData()
    {
        return [
            'categoryTypes' => CategoryType::select('id', 'title')
                ->with(['categories' => function ($query) {
                    $query->select('id', 'category_type_id', 'title')
                        ->withCount('products');
                }])
                ->withCount('products')
                ->latest()
                ->get(),
            'colors' => Color::select('id', 'name')->get()
        ];
    }

    public function categoryType(Request $request, $id)
    {
        $categoryType = CategoryType::with([
            'categories' => function ($query) {
                $query->select('id', 'category_type_id', 'title', 'image')
                    ->withCount('products');
            }
        ])->findOrFail($id);

        // Apply filters using the single scope
        $products = $categoryType->products()
            ->with(['oneVariant', 'oneImage', 'variants' => function ($query) {
                $query->select('product_id', 'color_id')->with('color')->distinct('color_id');
            }])
            ->filter($request)
            ->paginate($this->paginate);
$categories = Category::withCount(['products'])
    ->where('category_type_id', $id) // هنا بتحط قيمة الـ category_type_id
    ->latest()
    ->get(['id', 'title', 'image']);        $data = $this->getCommonData();
        return view('website.products', array_merge(compact('categoryType', 'products','categories'), $data));
    }

    public function category(Request $request, $id)
    {
        $category = Category::with('categoryType')->findOrFail($id);
 $categories  = Category::withCount(['products'])->latest()->get(['id', 'title', 'image']);
        // Apply filters using the single scope
        $products = $category->products()
            ->with(['oneVariant', 'oneImage', 'variants' => function ($query) {
                $query->select('product_id', 'color_id')->with('color')->distinct('color_id');
            }])
            ->filter($request)
            ->paginate($this->paginate);

        $data = $this->getCommonData();
        return view('website.products', array_merge(compact('category', 'products','categories'), $data));
    }


    public function products(Request $request)
    {
        $products = Product::with(['oneVariant', 'oneImage', 'variants' => function ($query) {
            $query->select('product_id', 'color_id')->with('color')->distinct('color_id');
        }])
            ->filter($request)
            ->paginate($this->paginate);
             $categories  = Category::withCount(['products'])->latest()->get(['id', 'title', 'image']);

        $data = $this->getCommonData();
        return view('website.products', array_merge(compact('products','categories'), $data));
    }
    public function productDetails($id)
    {
           $features = Product::query()
            ->with([
                'oneVariant',
                'oneImage',
                'variants' => function ($query) {
                    $query->select('product_id', 'color_id')->with('color')->distinct('color_id');
                }
            ])
            ->where('is_featured', 1)
            ->latest()
            ->limit(12)
            ->get(['id', 'name', 'image']);
        $product = Product::with([
            'category.categoryType',
            'images',
            'variants.color'
        ])->findOrFail($id);
        $uniqueSizes = $product->variants->pluck('size')->unique();
        $uniqueColors = $product->variants->pluck('color')->unique();
 $categories  = Category::withCount(['products'])->latest()->get(['id', 'title', 'image']);
        $relatedProducts = Product::with(['oneVariant'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit(10)
            ->get();

        return view('website.product-details', compact('product','features', 'uniqueSizes', 'uniqueColors', 'relatedProducts','categories'));
    }
    public function quickView($variantId)
    {
        $product = Product::with('oneVariant.color')
            ->whereRelation('oneVariant', 'id', $variantId)
            ->firstOrFail();
        return view('website.layouts.quick-view', compact('product'));
    }

    public function getUniqueColorsByVariantSize($productId, $colorId)
    {
        $variants = Variant::query()
            ->where('product_id', $productId)
            ->where('color_id', $colorId)
            ->get();
        return response()->json($variants);
    }
}
