<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientArchive extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'donnees',
    ];

    protected $casts = [
        'donnees' => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function documents()
    {
        return $this->hasMany(DocumentMedical::class, 'patient_archive_id');
    }
}
