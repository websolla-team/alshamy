<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'paymob_order_id',
        'amount',
        'currency',
        'payment_method',
        'status',
        'transaction_id',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array', // علشان يتحول JSON تلقائيًا
    ];
}
