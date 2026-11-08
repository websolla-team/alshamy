<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::orderBy('id', 'desc')->get();
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'image' => 'required|image',
            'title.*' => 'required|string',
        ]);
        $fileName = Helper::storeImage($request->file('image'), 'brands');
        $data['image'] = $fileName;
        Brand::create($data);
        return redirect()->route('brands.index');

    }

    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'image' => 'nullable|image',
            'title.*' => 'required|string',
        ]);
        unset($data['image']);
        $brand = Brand::findOrFail($id);
        if ($request->image) {
            $fileName = Helper::updateImage($request->file('image'), $brand->image, 'brands');
            $data['image'] = $fileName;
        }
        $brand->update($data);
        return redirect()->route('brands.index');
    }

    public function destroy(Request $request, $id)
    {
        $brand = Brand::findOrFail($request->id);
        $oldImage = $brand->image ?? '0';
        Helper::deleteImage($oldImage);
        $brand->delete();
        return back();
    }
}