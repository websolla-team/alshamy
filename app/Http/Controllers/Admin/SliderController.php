<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{

    public function index()
    {
        $sliders = Slider::latest()->get();
        return view('admin.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'image' => 'required|image',
            'title.*' => 'nullable|string',
            'text.*' => 'nullable|string',
            'url' => 'nullable|url',
        ]);
        $fileName = Helper::storeImage($request->file('image'), 'sliders');
        $data['image'] = $fileName;
        Slider::create($data);
        return redirect()->route('sliders.index');
    }

    public function show(Slider $slider)
    {
        //
    }

    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {

        $data = $request->validate([
            'image' => 'nullable|image',
            'title.*' => 'nullable|string',
            'text.*' => 'nullable|string',
            'url' => 'nullable|url',
        ]);

        unset($data['image']);
        if ($request->image) {
            $fileName = Helper::updateImage($request->file('image'), $slider->image, 'sliders');
            $data['image'] = $fileName;
        }
        $slider->update($data);
        return redirect()->route('sliders.index');
    }

    public function destroy(Request $request)
    {
        $slider = Slider::findOrFail($request->id);
        Helper::deleteImage($slider->image);
        $slider->delete();
        return back();
    }
}
