<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\CartItem;
use App\Models\OrderItem;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'stock',
        'price',
        'image_path',
    ];

    /**
     * A product belongs to a category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * A product can appear in many cart items
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * A product can appear in many order items
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Route model binding by slug instead of ID
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Accessor for product image URL
     */
    public function getImageUrlAttribute(): string
    {
        $path = trim((string) $this->image_path);

        // Stored file path on "public" disk
        if ($path && ! Str::startsWith($path, ['http://', 'https://'])) {
            return asset('storage/' . $path);
        }

        // Old records that still have full URLs
        if ($path) {
            return $path;
        }

        // Fallback placeholder
        return asset('images/placeholder-product.png');
    }
}
