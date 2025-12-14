<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    // allow setting any columns we use in the controller
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'meta'       => 'array',   // if you store cart snapshot JSON
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Cancellable logic (tweak as you like)
    public function getIsCancellableAttribute(): bool
    {
        return in_array($this->status, ['pending', 'paid', 'processing'], true);
    }

    public function markCancelled(): void
    {
        $this->update(['status' => 'cancelled']);
    }
}
