<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id','product_id','name','price','qty','total'];

    public function order()   { return $this->belongsTo(\App\Models\Order::class); }
    public function product() { return $this->belongsTo(\App\Models\Product::class); }
}
