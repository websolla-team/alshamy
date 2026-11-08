<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Helpers\Helper;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offers = Offer::orderBy('id', 'desc')->get();
                $products = Product::orderBy('id', 'desc')->get();

        return view('admin.offers.index', compact('offers','products'));
    }

    public function create()
    {
                $products = Product::orderBy('id', 'desc')->get();

        return view('admin.offers.create',compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name.*' => 'required|string',
            'product_id' => 'required',
        ]);
        Offer::create($data);
        return redirect()->route('offers.index');

    }

    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $offer = Offer::findOrFail($id);
                $products = Product::orderBy('id', 'desc')->get();

        return view('admin.offers.edit', compact('offer','products'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name.*' => 'required|string',
            'product_id' => 'required',

        ]);
        $offer = Offer::findOrFail($id);
        $offer->update($data);
        return redirect()->route('offers.index');
    }

    public function destroy(Request $request, $id)
    {
        $offer = Offer::findOrFail($request->id);
        $offer->delete();
        return back();
    }
}
