<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sizes = Size::orderBy('id', 'desc')->get();
        return view('admin.sizes.index', compact('sizes'));
    }

    public function create()
    {
        return view('admin.sizes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name.*' => 'required|string',
        ]);
        Size::create($data);
        return redirect()->route('sizes.index');

    }

    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $size = Size::findOrFail($id);
        return view('admin.sizes.edit', compact('size'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name.*' => 'required|string',
        ]);
        $size = Size::findOrFail($id);
        $size->update($data);
        return redirect()->route('sizes.index');
    }

    public function destroy(Request $request, $id)
    {
        $size = Size::findOrFail($request->id);
        $size->delete();
        return back();
    }
}
