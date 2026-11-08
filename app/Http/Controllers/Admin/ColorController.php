<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $colors = Color::orderBy('id', 'desc')->get();
        return view('admin.colors.index', compact('colors'));
    }

    public function create()
    {
        return view('admin.colors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name.*' => 'required|string',

        ]);
        Color::create($data);
        return redirect()->route('extra.index');

    }

    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $color = Color::findOrFail($id);
        return view('admin.colors.edit', compact('color'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name.*' => 'required|string',

        ]);
        $color = Color::findOrFail($id);
        $color->update($data);
        return redirect()->route('extra.index');
    }

    public function destroy(Request $request, $id)
    {
        $color = Color::findOrFail($request->id);
        $color->delete();
        return back();
    }
}
