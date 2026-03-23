<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorie_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('categorie_documents', 'est_document_patient')) {
                $table->boolean('est_document_patient')->default(false)->after('actif');
            }
        });

        Schema::table('document_medicals', function (Blueprint $table) {
            if (!Schema::hasColumn('document_medicals', 'source_document')) {
                $table->string('source_document', 40)->default('telechargement')->after('description');
                $table->index('source_document');
            }
        });

        $defaults = [
            [
                'nom' => "Carte d'identite patient",
                'description' => 'Piece officielle d identification du patient.',
                'couleur' => '#2563eb',
                'icone' => 'fas fa-id-card',
                'duree_conservation_ans' => 10,
                'confidentiel' => true,
                'actif' => true,
                'ordre' => 10,
                'est_document_patient' => true,
            ],
            [
                'nom' => 'Carte CNSS / assurance',
                'description' => 'Carte de couverture sociale ou assurance du patient.',
                'couleur' => '#0ea5e9',
                'icone' => 'fas fa-shield-heart',
                'duree_conservation_ans' => 10,
                'confidentiel' => true,
                'actif' => true,
                'ordre' => 20,
                'est_document_patient' => true,
            ],
            [
                'nom' => 'Ordonnance medicale',
                'description' => 'Ordonnances remises ou scannees dans le cadre du suivi patient.',
                'couleur' => '#10b981',
                'icone' => 'fas fa-file-prescription',
                'duree_conservation_ans' => 20,
                'confidentiel' => true,
                'actif' => true,
                'ordre' => 30,
                'est_document_patient' => true,
            ],
            [
                'nom' => 'Resultats d analyses',
                'description' => 'Analyses biologiques, bilans ou examens de laboratoire.',
                'couleur' => '#14b8a6',
                'icone' => 'fas fa-flask',
                'duree_conservation_ans' => 20,
                'confidentiel' => true,
                'actif' => true,
                'ordre' => 40,
                'est_document_patient' => true,
            ],
            [
                'nom' => 'Radiologie / imagerie',
                'description' => 'Radiographies, echographies, scanner, IRM et autres imageries.',
                'couleur' => '#8b5cf6',
                'icone' => 'fas fa-x-ray',
                'duree_conservation_ans' => 20,
                'confidentiel' => true,
                'actif' => true,
                'ordre' => 50,
                'est_document_patient' => true,
            ],
            [
                'nom' => 'Compte rendu medical',
                'description' => 'Compte rendu de consultation, hospitalisation ou suivi.',
                'couleur' => '#6366f1',
                'icone' => 'fas fa-notes-medical',
                'duree_conservation_ans' => 20,
                'confidentiel' => true,
                'actif' => true,
                'ordre' => 60,
                'est_document_patient' => true,
            ],
            [
                'nom' => 'Certificat medical',
                'description' => 'Certificats medicaux remis au patient.',
                'couleur' => '#f59e0b',
                'icone' => 'fas fa-file-signature',
                'duree_conservation_ans' => 10,
                'confidentiel' => true,
                'actif' => true,
                'ordre' => 70,
                'est_document_patient' => true,
            ],
            [
                'nom' => 'Consentement patient',
                'description' => 'Consentements signes et documents medico-legaux associes.',
                'couleur' => '#ef4444',
                'icone' => 'fas fa-file-circle-check',
                'duree_conservation_ans' => 20,
                'confidentiel' => true,
                'actif' => true,
                'ordre' => 80,
                'est_document_patient' => true,
            ],
            [
                'nom' => 'Documents administratifs',
                'description' => 'Pieces administratives utiles au suivi patient.',
                'couleur' => '#64748b',
                'icone' => 'fas fa-folder-closed',
                'duree_conservation_ans' => 10,
                'confidentiel' => false,
                'actif' => true,
                'ordre' => 90,
                'est_document_patient' => true,
            ],
            [
                'nom' => 'Autres documents patient',
                'description' => 'Documents patients non classes dans une categorie specifique.',
                'couleur' => '#6b7280',
                'icone' => 'fas fa-file-medical',
                'duree_conservation_ans' => 10,
                'confidentiel' => false,
                'actif' => true,
                'ordre' => 100,
                'est_document_patient' => true,
            ],
        ];

        foreach ($defaults as $category) {
            DB::table('categorie_documents')->updateOrInsert(
                ['nom' => $category['nom']],
                array_merge($category, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }

    public function down(): void
    {
        DB::table('categorie_documents')
            ->whereIn('nom', [
                "Carte d'identite patient",
                'Carte CNSS / assurance',
                'Ordonnance medicale',
                'Resultats d analyses',
                'Radiologie / imagerie',
                'Compte rendu medical',
                'Certificat medical',
                'Consentement patient',
                'Documents administratifs',
                'Autres documents patient',
            ])
            ->delete();

        Schema::table('document_medicals', function (Blueprint $table) {
            if (Schema::hasColumn('document_medicals', 'source_document')) {
                $table->dropIndex(['source_document']);
                $table->dropColumn('source_document');
            }
        });

        Schema::table('categorie_documents', function (Blueprint $table) {
            if (Schema::hasColumn('categorie_documents', 'est_document_patient')) {
                $table->dropColumn('est_document_patient');
            }
        });
    }
};
