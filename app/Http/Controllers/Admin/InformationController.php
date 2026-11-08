<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Information;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    public function index()
    {
        $informations = Information::latest()->get();
        return view('admin.informations.index', compact('informations'));
    }

    public function create()
    {
        return view('admin.informations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title.*' => 'required|string',
            'url' => 'nullable|url',
        ]);
        Information::create($data);
        return redirect()->route('informations.index');

    }

    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $information = Information::findOrFail($id);
        return view('admin.informations.edit', compact('information'));
    }

    public function update(Request $request, Information $information)
    {
        $data = $request->validate([
            'title.*' => 'required|string',
            'url' => 'nullable|url',
        ]);
        $information->update($data);
        return redirect()->route('informations.index');
    }

    public function destroy(Request $request)
    {
        $information = Information::findOrFail($request->id);
        $information->delete();
        return back();
    }
}