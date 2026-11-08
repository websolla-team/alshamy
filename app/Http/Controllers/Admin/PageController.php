<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::get();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'image' => 'required|image',
            'text.*' => 'required|string|min:3',
            'title.*' => 'required|string|min:3',
        ]);
        $fileName = Helper::storeImage($request->file('image'), 'pages');
        $data['image'] = $fileName;
        Page::create($data);
        return redirect()->route('pages.index');

    }

    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'image' => 'nullable|image',
            'text.*' => 'required|string|min:3',
            'title.*' => 'required|string|min:3',
        ]);
        unset($data['image']);
        $page = Page::findOrFail($id);
        if ($request->image) {
            $fileName = Helper::updateImage($request->file('image'), $page->image, 'pages');
            $data['image'] = $fileName;
        }
        $page->update($data);
        return redirect()->route('pages.index');
    }

    public function destroy(Request $request, $id)
    {
        $page = Page::where('id', '!=', 1)->findOrFail($request->id);
        $oldImage = $page->image ?? '0';
        Helper::deleteImage($oldImage);
        $page->delete();
        return back();
    }
}