<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource',
        'action',
        'code',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions')->withTimestamps();
    }

    public function usersWithOverrides(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_permission_overrides')
            ->withPivot(['effect', 'created_by'])
            ->withTimestamps();
    }
}
