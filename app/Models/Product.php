<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;
    use HasTranslations;
    public $translatable = ['name', 'description'];
    protected $guarded = [];

    public function variants()
    {
        return $this->hasMany(Variant::class)->oldest();
    }
    public function oneVariant()
    {
        return $this->hasOne(Variant::class)->oldest();
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    public function oneImage()
    {
        return $this->hasOne(ProductImage::class);
    }

    public function scopeFilter($query, $request)
    {
        return $query->when($request->filled('search'), function ($query) use ($request) {
            return $query->where(function ($q) use ($request) {
                $q->where("name->en", 'like', "%" . $request->search . "%")
                    ->orWhere("name->ar", 'like', "%" . $request->search . "%");
            });
        })
            ->when($request->has('colors'), function ($query) use ($request) {
                return $query->whereHas('variants', function ($colorQuery) use ($request) {
                    $colorQuery->whereIn('color_id', $request->colors);
                });
            })
            ->when($request->has('stock'), function ($query) {
                return $query->whereHas('variants', function ($stockQuery) {
                    $stockQuery->where('quantity', '>', 0);
                });
            })
            ->when($request->has('discount'), function ($query) {
                return $query->whereHas('variants', function ($discountQuery) {
                    $discountQuery->whereColumn('old_price', '>', 'price');
                });
            })
            ->when($request->sort_by, function ($query) use ($request) {
                // Add a subquery column for the price of the first variant
                if (in_array($request->sort_by, ['price-asc', 'price-desc'])) {
                    $query->addSelect([
                        'variant_price' => DB::table('variants')
                            ->select('price')
                            ->whereColumn('variants.product_id', 'products.id')
                            ->orderBy('id')
                            ->limit(1)
                    ]);
                }

                switch ($request->sort_by) {
                    case 'oldest':
                        return $query->orderBy('products.id', 'asc');
                    case 'newest':
                        return $query->orderBy('products.id', 'desc');
                    case 'price-asc':
                        return $query->orderBy('variant_price', 'asc');
                    case 'price-desc':
                        return $query->orderBy('variant_price', 'desc');
                }
            }, function ($query) {
                return $query->orderBy('products.id', 'desc');
            });
    }
}
