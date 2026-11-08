<?php

namespace App\Http\Controllers\Admin;

use App\Models\Color;
use App\Helpers\Helper;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $products = ProductImage::where('product_id', $id)->orderBy('color_id', 'desc')->get();
        $mainProduct = Product::findOrFail($id);
        $colors = Color::get();
        return view('admin.products.product_images.index', compact('products', 'mainProduct', 'id', 'colors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
              'imgs.*' => 'required|image|max:1024',
            'id' => 'required|exists:products,id',
            'color_id' => 'required|exists:colors,id',
        ]);
        if ($request->has('imgs')) {
            foreach ($request->file('imgs') as $img) {
                $imgName = Helper::storeImage($img, 'product_images');
                ProductImage::create([
                    'image' => $imgName,
                    'product_id' => $request->id,
                    'color_id' => $request->color_id,
                ]);
            }
        }
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductImage $productImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductImage $productImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
          'image' => 'image|max:1024',
            'color_id' => 'required|exists:colors,id',
        ]);
        unset($data['image']);
        $productImage = ProductImage::findOrFail($request->id);
        if ($request->image) {
            $fileName = Helper::updateImage($request->file('image'), $productImage->image, 'product_images');
            $data['image'] = $fileName;
        }
        $productImage->update($data);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        $productImage = ProductImage::findOrFail($request->id);
        $oldImage = $productImage->image;
        Helper::deleteImage($oldImage);
        $productImage->delete();
        return back();
    }
}
