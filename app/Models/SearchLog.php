<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class SearchLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'search_logs';

    protected $fillable = [
        'query',
        'user_id',
        'session_id',
        'results_count',
        'ip',
        'created_at',
    ];

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
