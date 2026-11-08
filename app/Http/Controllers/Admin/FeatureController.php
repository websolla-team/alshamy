<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $features = Feature::orderBy('id', 'desc')->get();
        return view('admin.features.index', compact('features'));
    }

    public function create()
    {
        return view('admin.features.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'image' => 'required|image',
            'text.*' => 'required|string|min:3',
            'title.*' => 'required|string|min:3',
        ]);
        $fileName = Helper::storeImage($request->file('image'), 'features');
        $data['image'] = $fileName;
        Feature::create($data);
        return redirect()->route('features.index');

    }

    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $feature = Feature::findOrFail($id);
        return view('admin.features.edit', compact('feature'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'image' => 'nullable|image',
            'text.*' => 'required|string|min:3',
            'title.*' => 'required|string|min:3',
        ]);
        unset($data['image']);
        $feature = Feature::findOrFail($id);
        if ($request->image) {
            $fileName = Helper::updateImage($request->file('image'), $feature->image, 'features');
            $data['image'] = $fileName;
        }
        $feature->update($data);
        return redirect()->route('features.index');
    }

    public function destroy(Request $request, $id)
    {
        $feature = Feature::findOrFail($request->id);
        $oldImage = $feature->image ?? '0';
        Helper::deleteImage($oldImage);
        $feature->delete();
        return back();
    }
}