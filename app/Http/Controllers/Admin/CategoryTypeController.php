<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Models\CategoryType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = CategoryType::latest()->get();
        return view('admin.category-types.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.category-types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title.*' => 'required|string',
            // 'image' => 'required|image',
            // 'show' => 'required|integer',
        ]);
        // $fileName = Helper::storeImage($request->file('image'), 'category_types');
        // $data['image'] = $fileName;
        CategoryType::create($data);
        return redirect()->route('category-types.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $category = CategoryType::findOrFail($id);
        return view('admin.category-types.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title.*' => 'required|string',
            // 'image' => 'nullable|image',
            // 'show' => 'required|integer',
        ]);
        unset($data['image']);
        $category = CategoryType::findOrFail($id);
        if ($request->image) {
            $fileName = Helper::updateImage($request->file('image'), $category->image, 'category_types');
            $data['image'] = $fileName;
        }
        $category->update($data);
        return redirect()->route('category-types.index');
    }

    public function destroy(Request $request, $id)
    {
        $category = CategoryType::findOrFail($request->id);
        $category->delete();
        return back();
    }
}
