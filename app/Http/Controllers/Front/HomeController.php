<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Page;
use App\Models\Review;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Slider;

class HomeController extends Controller
{
    public function index()
    {
              $offers = Offer::get();


        $sliders = Slider::get(['title', 'image','text']);
             $single_product = Product::with([
            'category.categoryType',
            'images',
            'variants.color'
        ])->where('is_featured',1)->first();
        $reviews = Review::get();
    $features = Product::query()
    ->with([
        'oneVariant',
        'oneImage',
        'variants',
        'images' => function ($query) {
            $query->select('id', 'product_id', 'color_id', 'image')
                  ->with('color')
                  ->distinct('color_id');
        }
    ])
    ->where('is_featured', 1)
    ->latest()
    ->limit(12)
    ->get(['id', 'name', 'image']);

$recentProducts = Product::query()
    ->with([
        'oneVariant',
        'oneImage',
        'variants',
        'images' => function ($query) {
            $query->select('id', 'product_id', 'color_id', 'image')
                  ->with('color')
                  ->distinct('color_id');
        }
    ])
    ->where('is_featured', 0)
    ->latest()
    ->get(['id', 'name', 'image']);
$Products = Product::query()
    ->with([
        'oneVariant',
        'oneImage',
        'variants',
        'images' => function ($query) {
            $query->select('id', 'product_id', 'color_id', 'image')
                  ->with('color')
                  ->distinct('color_id');
        }
    ])

    ->latest()
    ->get(['id', 'name', 'image']);

        $categories  = Category::orderBy('order_num','asc')->withCount(['products'])->get(['id', 'title', 'image']);
        return view('website.index', compact(
            'sliders',
            'reviews',
            'features',
            'recentProducts',
            'categories',
            'single_product','offers','Products'
        ));
    }
    public function page($id = 1)
    {
        $page = Page::findOrFail($id);
         $categories  = Category::withCount(['products'])->latest()->get(['id', 'title', 'image']);
        return view('website.about', compact('page','categories'));
    }
    public function contacts()
    {
         $categories  = Category::withCount(['products'])->latest()->get(['id', 'title', 'image']);
        return view('website.contact', compact('categories'));
    }
    public function review()
    {
           $categories  = Category::withCount(['products'])->latest()->get(['id', 'title', 'image']);
           $reviews = Review::get();
        return view('website.review', compact('reviews','categories'));
    }
}
