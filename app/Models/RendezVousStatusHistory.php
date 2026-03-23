<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RendezVousStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'rendez_vous_status_histories';

    protected $fillable = [
        'rendez_vous_id',
        'old_status',
        'new_status',
        'changed_by',
        'source',
        'notes',
        'transitioned_at',
    ];

    protected $casts = [
        'transitioned_at' => 'datetime',
    ];

    public function rendezVous()
    {
        return $this->belongsTo(RendezVous::class, 'rendez_vous_id');
    }

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
