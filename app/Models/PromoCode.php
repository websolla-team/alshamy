<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function isValid()
    {
        // return true or false
        $now = now();
        return $this->is_active &&
        $this->uses_count < $this->max_uses &&
            (!$this->start_date || $now->greaterThanOrEqualTo($this->start_date)) &&
            (!$this->end_date || $now->lessThanOrEqualTo($this->end_date));
    }
    public function calculateDiscount($totalPrice)
    {
        if ($this->discount_type == 'percentage') {
            return ($totalPrice * $this->discount_value) / 100;
        } elseif ($this->discount_type == 'fixed') {
            return min($totalPrice, $this->discount_value);
        }
        return 0; // If no valid discount type is found, return 0.
    }

}