<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        $addonsBySection = collect();
        if (Schema::hasTable('menu_addons')) {
            $addonsBySection = DB::table('menu_addons')
                ->orderBy('id')
                ->get(['name', 'section', 'choices'])
                ->map(fn ($addon) => [
                    'name' => $addon->name,
                    'section' => $addon->section,
                    'choices' => collect(json_decode($addon->choices, true) ?: [])
                        ->filter(fn ($choice) => is_array($choice)
                            && ! empty($choice['size'])
                            && is_numeric($choice['price'] ?? null)
                            && (float) $choice['price'] > 0)
                        ->values()
                        ->all(),
                ])
                ->groupBy('section');
        }

        return view('website.index', compact('categories', 'addonsBySection'));
    }

    public function page($id = 1)
    {
        $page = Page::findOrFail($id);
        $categories = Category::withCount(['products'])->latest()->get(['id', 'title', 'image']);

        return view('website.about', compact('page', 'categories'));
    }

    public function contacts()
    {
        $categories = Category::withCount(['products'])->latest()->get(['id', 'title', 'image']);

        return view('website.contact', compact('categories'));
    }

    public function review()
    {
        $categories = Category::withCount(['products'])->latest()->get(['id', 'title', 'image']);
        $reviews = Review::get();

        return view('website.review', compact('reviews', 'categories'));
    }
}
