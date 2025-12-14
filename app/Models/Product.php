<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Route model binding by slug
    public function getRouteKeyName()
    {
        return 'slug';
    }

   
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
