<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ApiClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'secret_hash',
        'metadata',
        'active',
        'last_used_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function tokens(): HasMany
    {
        return $this->hasMany(ApiToken::class, 'client_id');
    }

    public function rateLimit(): HasOne
    {
        return $this->hasOne(ApiRateLimit::class, 'client_id');
    }
}
