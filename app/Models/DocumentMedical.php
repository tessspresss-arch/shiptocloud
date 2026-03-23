<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentMedical extends Model
{
    use HasFactory;

    protected $table = 'document_medicals';

    protected $fillable = [
        'patient_archive_id',
        'categorie_document_id',
        'nom_fichier',
        'nom_original',
        'chemin_fichier',
        'mime_type',
        'taille_fichier',
        'extension',
        'description',
        'date_document',
        'auteur',
        'tags',
        'chiffre',
        'hash_fichier',
        'version',
        'document_parent_id',
        'supprime',
        'date_suppression',
        'source_document',
    ];

    protected $casts = [
        'date_document' => 'datetime',
        'chiffre' => 'boolean',
        'tags' => 'array',
    ];

    /**
     * Relation avec la catégorie
     */
    public function categorie()
    {
        return $this->belongsTo(CategorieDocument::class, 'categorie_document_id');
    }

    /**
     * Relation avec l'archive patient
     */
    public function archive(): BelongsTo
    {
        return $this->belongsTo(PatientArchive::class, 'patient_archive_id');
    }

    /**
     * Relation parent-enfant pour les versions
     */
    public function parent()
    {
        return $this->belongsTo(DocumentMedical::class, 'document_parent_id');
    }

    public function versions()
    {
        return $this->hasMany(DocumentMedical::class, 'document_parent_id');
    }

    public function getPatientRecordAttribute(): ?Patient
    {
        return $this->archive?->patient;
    }
}
