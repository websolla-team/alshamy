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

    /**
     * Category name without the restaurant name, for the menu navigation.
     * "شاورما الشامي" reads as "شاورما" next to the الشامي wordmark.
     */
    public function getMenuTitleAttribute(): string
    {
        $title = trim((string) $this->title);

        return trim(preg_replace('/\s+الشامي\s*$/u', '', $title)) ?: $title;
    }

    public function categoryType()
    {
        return $this->belongsTo(CategoryType::class, 'category_type_id');
    }
}