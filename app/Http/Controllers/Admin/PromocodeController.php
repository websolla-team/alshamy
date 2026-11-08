<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromocodeController extends Controller
{
    public function index()
    {
        $promocodes = PromoCode::latest()->get();
        return view('admin.promocodes.index', compact('promocodes'));
    }

    public function create()
    {
        return view('admin.promocodes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:promo_codes,code',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'max_uses' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]);
        // Ensure 'is_active' is always set
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        PromoCode::create($data);
        return redirect()->route('promocodes.index');

    }

    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $promocode = PromoCode::findOrFail($id);
        return view('admin.promocodes.edit', compact('promocode'));
    }

    public function update(Request $request, PromoCode $promocode)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:promo_codes,code,' . $promocode->id,
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'max_uses' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $promocode->update($data);
        return redirect()->route('promocodes.index');
    }

    public function destroy(Request $request)
    {
        $promocode = PromoCode::findOrFail($request->id);
        $promocode->delete();
        return back();
    }
}