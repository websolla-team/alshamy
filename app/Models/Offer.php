<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    use HasTranslations;
    public $translatable = ['name'];
    protected $guarded = [];
}
