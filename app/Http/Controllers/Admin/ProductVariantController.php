<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Size;
use App\Models\Variant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $mainProduct = Product::findOrFail($id);
        $sizes = Size::all();
        $variants = Variant::where('product_id', $id)->get();
        return view('admin.products.product_variants.index', compact('variants', 'mainProduct', 'sizes', 'id'));
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
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
            'size_id' => 'required|string',
            'price' => 'required|numeric',
            'old_price' => 'nullable|numeric',
        ]);
        Variant::create($data);
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Variant $productVariant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Variant $productVariant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'quantity' => 'required|integer',
            'size_id' => 'required|string',
            'price' => 'required|numeric',
            'old_price' => 'nullable|numeric',
        ]);
        $productVariant = Variant::findOrFail($request->id);
        $productVariant->update($data);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        $productVariant = Variant::findOrFail($request->id);
        $productVariant->delete();
        return back();
    }
}
