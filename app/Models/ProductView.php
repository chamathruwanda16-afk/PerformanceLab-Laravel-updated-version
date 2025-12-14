<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ProductView extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'product_views';

    protected $fillable = [
        'product_id',
        'slug',
        'user_id',
        'session_id',
        'ip',
        'user_agent',
        'viewed_at',
        'search_query',   // 🔥 which search led to this click
    ];

    public $timestamps = false;

    protected $casts = [
        'viewed_at' => 'datetime',
    ];
}
