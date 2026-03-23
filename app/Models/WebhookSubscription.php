<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebhookSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'direction',
        'event',
        'url',
        'secret',
        'active',
        'retry_max_attempts',
        'timeout_ms',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function deliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class, 'subscription_id');
    }
}
