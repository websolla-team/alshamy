<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
              'image' => 'required|image|max:1024',
            'url' => 'required|url|max:255',
            'title' => 'required|max:255',
        ]);
        $fileName = Helper::storeImage($request->file('image'), 'banners');
        $data['image'] = $fileName;
        Banner::create($data);
        return redirect()->route('banners.index');

    }

    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
              'image' => 'required|image|max:1024',
            'url' => 'required|url|max:255',
            'title' => 'required|max:255',
        ]);
        unset($data['image']);
        $banner = Banner::findOrFail($id);
        if ($request->image) {
            $fileName = Helper::updateImage($request->file('image'), $banner->image, 'banners');
            $data['image'] = $fileName;
        }
        $banner->update($data);
        return redirect()->route('banners.index');
    }

    public function destroy(Request $request, $id)
    {
        $banner = Banner::findOrFail($request->id);
        $oldImage = $banner->image;
        Helper::deleteImage($oldImage);
        $banner->delete();
        return back();
    }
}
