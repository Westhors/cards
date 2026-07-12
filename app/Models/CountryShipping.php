<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryShipping extends Model
{
    protected $fillable = [
        'name',
        'iso_code',
        'shipping_price',
        'currency',
    ];

    protected $casts = [
        'shipping_price' => 'decimal:2',
    ];
}
