<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::orderBy('id', 'desc')->get();
        return view('admin.reviews.index', compact('reviews'));
    }

    public function create()
    {
        return view('admin.reviews.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'text.*' => 'required|string|min:3',
            'name.*' => 'required|string|min:3',
        ]);
        Review::create($data);
        return redirect()->route('reviews.index');

    }

    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $review = Review::findOrFail($id);
        return view('admin.reviews.edit', compact('review'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'text.*' => 'required|string|min:3',
            'name.*' => 'required|string|min:3',
        ]);
        $review = Review::findOrFail($id);
        $review->update($data);
        return redirect()->route('reviews.index');
    }

    public function destroy(Request $request, $id)
    {
        $blog = Review::findOrFail($request->id);
        $oldImage = $blog->image ?? '0';
        Helper::deleteImage($oldImage);
        $blog->delete();
        return back();
    }
}