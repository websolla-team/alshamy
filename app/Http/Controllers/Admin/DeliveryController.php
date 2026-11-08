<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deliveries = Delivery::get();
        return view('admin.deliveries.index', compact('deliveries'));
    }

    public function create()
    {
        return view('admin.deliveries.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name.*' => 'required|string',
            'cost' => 'required|numeric',
        ]);
        Delivery::create($data);
        return redirect()->route('deliveries.index');

    }

    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $delivery = Delivery::findOrFail($id);
        return view('admin.deliveries.edit', compact('delivery'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name.*' => 'required|string',
            'cost' => 'required|numeric',
        ]);
        $delivery = Delivery::findOrFail($id);
        $delivery->update($data);
        return redirect()->route('deliveries.index');
    }

    public function destroy(Request $request, $id)
    {
        $delivery = Delivery::findOrFail($request->id);
        $delivery->delete();
        return back();
    }
}