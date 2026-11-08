<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasFactory;
    use HasTranslations;
    public $translatable = ['title'];
    protected $guarded = [];
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function categoryType()
    {
        return $this->belongsTo(CategoryType::class, 'category_type_id');
    }
}