<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CategoryType extends Model
{
    use HasFactory;
    use HasTranslations;
    public $translatable = ['title'];
    protected $guarded = [];
    public function categories()
    {
        return $this->hasMany(Category::class);
    }
    public function products()
    {
        return $this->hasManyThrough(Product::class, Category::class);
    }
}