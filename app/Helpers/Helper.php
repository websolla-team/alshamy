<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class Helper
{
     public static function storeImage($image, $path)
    {

        $fileName = uniqid() . '.webp';
        $path = 'images/' . trim($path, '/');
        $fullPath = storage_path('app/public/' . $path);


        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true);
        }


        $manager = new ImageManager(new Driver());


        $img = $manager->read($image->getRealPath());


        $img->toWebp(75)->save($fullPath . '/' . $fileName);


        return Storage::url($path . '/' . $fileName);
    }

    public static function updateImage($image, $oldImage, $path)
    {
        self::deleteImage($oldImage);
        return self::storeImage($image, $path);
    }

    public static function deleteImage($path)
    {
        $relativePath = str_replace('/storage/', '', $path);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    public static function link($route, $id, $title)
    {
        $slug = preg_replace('/[^\p{Arabic}A-Za-z0-9-]+/u', '-', $title);
        return route($route, ['id' => $id, 'slug' => $slug]);
    }

    public static function limit($text, $num = 200)
    {
        $text = strip_tags($text); // Remove all HTML tags
        return Str::limit($text, $num);
        // return Str::limit(preg_replace('/<[^>]+>/', ' ', $text), $num);
    }
}
