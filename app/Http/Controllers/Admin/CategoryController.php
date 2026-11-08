<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryType;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('order_num','asc')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $categoryTypes = CategoryType::get();
        return view('admin.categories.create', compact('categoryTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
             'image' => 'image',
            'title.*' => 'required|string',
            'order_num' => 'nullable|numeric',
        ]);
        $fileName = Helper::storeImage($request->file('image'), 'categories');
        $data['image'] = $fileName;
        Category::create($data);
        return redirect()->route('categories.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $categoryTypes = CategoryType::get();
        return view('admin.categories.edit', compact('category', 'categoryTypes'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
              'image' => 'image',
            'title.*' => 'required|string',
                  'order_num' => 'nullable|numeric',
        ]);
        unset($data['image']);
        $category = Category::findOrFail($id);
        if ($request->image) {
            $fileName = Helper::updateImage($request->file('image'), $category->image, 'categories');
            $data['image'] = $fileName;
        }
        $category->update($data);
        return redirect()->route('categories.index');
    }

    public function destroy(Request $request, $id)
    {
        $category = Category::findOrFail($request->id);
        Helper::deleteImage($category->image);
        $category->delete();
        return back();
    }
}
