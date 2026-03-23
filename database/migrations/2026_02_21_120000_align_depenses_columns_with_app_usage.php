<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('depenses')) {
            return;
        }

        Schema::table('depenses', function (Blueprint $table) {
            if (!Schema::hasColumn('depenses', 'details')) {
                $table->text('details')->nullable()->after('description');
            }

            if (!Schema::hasColumn('depenses', 'categorie')) {
                $table->string('categorie', 50)->default('autre')->after('date_depense');
            }

            if (!Schema::hasColumn('depenses', 'beneficiaire')) {
                $table->string('beneficiaire')->nullable()->after('categorie');
            }

            if (!Schema::hasColumn('depenses', 'statut')) {
                $table->string('statut', 30)->default('enregistre')->after('beneficiaire');
            }

            if (!Schema::hasColumn('depenses', 'facture_numero')) {
                $table->string('facture_numero')->nullable()->after('statut');
            }

            if (!Schema::hasColumn('depenses', 'mode_paiement')) {
                $table->string('mode_paiement')->nullable()->after('facture_numero');
            }

            if (!Schema::hasColumn('depenses', 'date_paiement')) {
                $table->dateTime('date_paiement')->nullable()->after('mode_paiement');
            }

            if (!Schema::hasColumn('depenses', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('date_paiement')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        if (Schema::hasColumn('depenses', 'notes') && Schema::hasColumn('depenses', 'details')) {
            DB::table('depenses')
                ->whereNull('details')
                ->update(['details' => DB::raw('notes')]);
        }

        if (Schema::hasColumn('depenses', 'created_by') && Schema::hasColumn('depenses', 'user_id')) {
            DB::table('depenses')
                ->whereNull('user_id')
                ->update(['user_id' => DB::raw('created_by')]);
        }

        if (Schema::hasColumn('depenses', 'categorie') && Schema::hasColumn('depenses', 'categorie_id') && Schema::hasTable('categories_depenses')) {
            $mapping = [
                'fourniture' => 'fournitures',
                'medicament' => 'medicaments',
                'médicament' => 'medicaments',
                'loyer' => 'loyer',
                'personnel' => 'personnel',
                'utilite' => 'utilites',
                'utilité' => 'utilites',
                'maintenance' => 'maintenance',
                'formation' => 'formation',
            ];

            foreach ($mapping as $needle => $value) {
                $ids = DB::table('categories_depenses')
                    ->whereRaw('LOWER(nom) LIKE ?', ['%' . strtolower($needle) . '%'])
                    ->pluck('id');

                if ($ids->isNotEmpty()) {
                    DB::table('depenses')
                        ->whereIn('categorie_id', $ids->all())
                        ->update(['categorie' => $value]);
                }
            }
        }

        if (Schema::hasColumn('depenses', 'categorie')) {
            DB::table('depenses')
                ->whereNull('categorie')
                ->update(['categorie' => 'autre']);
        }

        if (Schema::hasColumn('depenses', 'statut')) {
            DB::table('depenses')
                ->whereNull('statut')
                ->update(['statut' => 'enregistre']);
        }

        Schema::table('depenses', function (Blueprint $table) {
            if (!Schema::hasColumn('depenses', 'categorie')) {
                return;
            }

            try {
                $table->index('categorie');
            } catch (\Throwable $e) {
                // Index already exists.
            }

            try {
                $table->index('statut');
            } catch (\Throwable $e) {
                // Index already exists.
            }

            try {
                $table->index('user_id');
            } catch (\Throwable $e) {
                // Index already exists.
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left empty to avoid destructive rollback on production data.
    }
};

