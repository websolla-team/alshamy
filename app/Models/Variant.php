<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Variant extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected function price(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return intval($value) == $value ? intval($value) : $value;
            }
        );
    }
    protected function oldPrice(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return intval($value) == $value ? intval($value) : $value;
            }
        );
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
