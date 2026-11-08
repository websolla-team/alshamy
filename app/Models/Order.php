<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function products()
    {
        return $this->hasMany(OrderDetail::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // protected static function boot()
    // {
    //     parent::boot();
    //     static::creating(function ($order) {
    //         // if (Auth::check()) {
    //         //     $order->user_id = Auth::id();
    //         //     $order->user_name = Auth::user()->first_name . ' ' . Auth::user()->last_name;
    //         // }
    //         $request = Request::capture();
    //         $order->user_name = $request->input('first_name') . ' ' . $request->input('last_name');
    //     });
    // }
}