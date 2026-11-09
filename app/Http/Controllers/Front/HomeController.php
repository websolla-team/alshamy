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
        $categories = Category::query()
            ->whereHas('products')
            ->with(['products' => function ($query) {
                $query->with('variants')->orderBy('id');
            }])
            ->orderBy('order_num', 'asc')
            ->get(['id', 'title', 'image']);

        return view('website.index', compact('categories'));
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
