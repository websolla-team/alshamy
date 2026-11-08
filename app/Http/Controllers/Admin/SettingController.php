<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $setting = Setting::first();
        return view('admin.settings.index', compact('setting'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Setting $setting)
    {
        $data = $request->validate([
            'keywords.*' => 'required|string|max:255',
            'description.*' => 'required|string',
            'main_image' => 'nullable|image',
            'top_banner' => 'nullable|image',
            'banner_image' => 'nullable|image',
            'banner_url' => 'required|string',
        ]);
        unset($data['main_image']);
        unset($data['banner_image']);
        if ($request->main_image) {
            $fileName = Helper::updateImage($request->file('main_image'), $setting->main_image, 'settings');
            $data['main_image'] = $fileName;
        }
        if ($request->banner_image) {
            $fileName = Helper::updateImage($request->file('banner_image'), $setting->banner_image, 'settings');
            $data['banner_image'] = $fileName;
        }
        if ($request->top_banner) {
            $fileName = Helper::updateImage($request->file('top_banner'), $setting->top_banner, 'settings');
            $data['top_banner'] = $fileName;
        }
        $setting->update($data);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        //
    }
}
