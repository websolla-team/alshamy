<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Brand extends Model
{
    use HasFactory;
    use HasTranslations;
    public $translatable = ['title'];
    protected $guarded = [];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function scopeSavebrand($query)
    {
        return $query->where('id', '!=', 0);
    }
}