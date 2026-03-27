@extends('layouts.app')

@section('title', 'Modifier Patient')
@section('topbar_subtitle', 'Edition structuree du dossier patient avec actions rapides et mise en page harmonisee.')

@section('content')
<div class="container-fluid patient-edit-page">
    <div class="card shadow-lg patient-edit-shell">
                <div class="card-header patient-edit-hero">
                    <div class="patient-edit-page-header">
                        <div class="patient-edit-header-main">
                            <a href="{{ route('patients.index') }}" class="header-back-btn patient-module-btn patient-module-btn--surface">
                                <span class="header-back-btn-icon patient-module-btn__icon"><i class="fas fa-arrow-left"></i></span>
                                <span class="d-none d-sm-inline">Retour</span>
                            </a>

                            <div class="patient-edit-title-card">
                                <i class="fas fa-user-injured"></i>
                                <div class="patient-edit-title-copy">
                                    <span class="patient-edit-eyebrow">Dossiers patients</span>
                                    <h1>Modifier Patient</h1>
                                    <p>Mettez a jour la fiche de {{ $patient->prenom }} {{ $patient->nom }} avec une edition plus nette et plus coherente.</p>
                                </div>
                                <span class="patient-edit-dossier-badge">{{ $patient->numero_dossier }}</span>
                            </div>
                        </div>

                        <div class="patient-edit-header-actions">
                            <a href="{{ route('patients.show', $patient->id) }}" class="btn-custom btn-secondary-custom patient-module-btn patient-module-btn--surface">
                                <span class="patient-module-btn__icon"><i class="fas fa-eye"></i></span>
                                <span>Voir fiche</span>
                            </a>
                            <button type="submit" form="editPatientForm" class="btn-custom btn-success-custom patient-module-btn patient-module-btn--primary">
                                <span class="patient-module-btn__icon"><i class="fas fa-save"></i></span>
                                <span>Enregistrer</span>
                            </button>
                        </div>
                    </div>

                    <div class="hero-meta-chips">
                        <span class="hero-chip"><i class="fas fa-id-card"></i>ID {{ $patient->id }}</span>
                        <span class="hero-chip"><i class="fas fa-calendar-plus"></i>Cree le {{ $patient->created_at ? $patient->created_at->format('d/m/Y') : 'N/A' }}</span>
                        <span class="hero-chip"><i class="fas fa-user-pen"></i>Edition du dossier patient</span>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('patients.update', $patient->id) }}" method="POST" id="editPatientForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Informations Personnelles -->
                            <div class="col-md-6">
                                <div class="card mb-4 edit-section-card">
                                    <div class="card-header bg-light section-head">
                                        <div>
                                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informations Personnelles</h5>
                                            <p>Identite, naissance et coordonnees principales du patient.</p>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Nom *</label>
                                                <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                                                       value="{{ old('nom', $patient->nom) }}" required>
                                                @error('nom')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Pr&eacute;nom *</label>
                                                <input type="text" name="prenom" class="form-control @error('prenom') is-invalid @enderror"
                                                       value="{{ old('prenom', $patient->prenom) }}" required>
                                                @error('prenom')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">CIN</label>
                                                <input type="text" name="cin" class="form-control @error('cin') is-invalid @enderror"
                                                       value="{{ old('cin', $patient->cin) }}" placeholder="Ex: AB123456">
                                                @error('cin')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Date de naissance *</label>
                                                <input type="date" name="date_naissance" class="form-control @error('date_naissance') is-invalid @enderror"
                                                       value="{{ old('date_naissance', $patient->date_naissance ? \Carbon\Carbon::parse($patient->date_naissance)->format('Y-m-d') : '') }}" required>
                                                @error('date_naissance')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Genre *</label>
                                                <select name="genre" class="form-select @error('genre') is-invalid @enderror" required>
                                                    <option value="">S&eacute;lectionner</option>
                                                    <option value="M" {{ old('genre', $patient->genre) == 'M' ? 'selected' : '' }}>Masculin</option>
                                                    <option value="F" {{ old('genre', $patient->genre) == 'F' ? 'selected' : '' }}>F&eacute;minin</option>
                                                </select>
                                                @error('genre')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">&Eacute;tat civil</label>
                                                <select name="etat_civil" class="form-select @error('etat_civil') is-invalid @enderror">
                                                    <option value="">S&eacute;lectionner</option>
                                                    <option value="celibataire" {{ old('etat_civil', $patient->etat_civil) == 'celibataire' ? 'selected' : '' }}>C&eacute;libataire</option>
                                                    <option value="marie" {{ old('etat_civil', $patient->etat_civil) == 'marie' ? 'selected' : '' }}>Mari&eacute;(e)</option>
                                                    <option value="divorce" {{ old('etat_civil', $patient->etat_civil) == 'divorce' ? 'selected' : '' }}>Divorc&eacute;(e)</option>
                                                    <option value="veuf" {{ old('etat_civil', $patient->etat_civil) == 'veuf' ? 'selected' : '' }}>Veuf/Veuve</option>
                                                </select>
                                                @error('etat_civil')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Adresse</label>
                                            <textarea name="adresse" class="form-control @error('adresse') is-invalid @enderror"
                                                      rows="2">{{ old('adresse', $patient->adresse) }}</textarea>
                                            @error('adresse')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                @include('patients.partials.city-field', [
                                                    'selectedCity' => old('ville', $patient->ville),
                                                    'selectId' => 'patientEditVilleSelection',
                                                    'otherId' => 'patientEditVilleAutre',
                                                    'selectClass' => 'form-select',
                                                    'otherInputClass' => 'form-control',
                                                    'feedbackClass' => 'invalid-feedback d-block',
                                                    'helperClass' => 'form-text text-muted mt-2',
                                                ])
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Code postal</label>
                                                <input type="text" name="code_postal" class="form-control @error('code_postal') is-invalid @enderror"
                                                       value="{{ old('code_postal', $patient->code_postal) }}" placeholder="20000">
                                                @error('code_postal')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact & Informations M&eacute;dicales -->
                            <div class="col-md-6">
                                <div class="card mb-4 edit-section-card">
                                    <div class="card-header bg-light section-head">
                                        <div>
                                            <h5 class="mb-0"><i class="fas fa-phone me-2"></i>Contact</h5>
                                            <p>Informations de communication et contact d urgence.</p>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">T&eacute;l&eacute;phone *</label>
                                                <input type="tel" name="telephone" class="form-control @error('telephone') is-invalid @enderror"
                                                       value="{{ old('telephone', $patient->telephone) }}"
                                                       placeholder="61234567"
                                                       inputmode="numeric"
                                                       pattern="[0-9]{8}"
                                                       minlength="8"
                                                       maxlength="8"
                                                       title="Saisir exactement 8 chiffres"
                                                       autocomplete="off"
                                                       required>
                                                @error('telephone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                                       value="{{ old('email', $patient->email) }}">
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Personne &agrave; contacter (urgence)</label>
                                            <input type="text" name="contact_urgence" class="form-control @error('contact_urgence') is-invalid @enderror"
                                                   value="{{ old('contact_urgence', $patient->contact_urgence) }}" placeholder="Nom & Pr&eacute;nom">
                                            @error('contact_urgence')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">T&eacute;l&eacute;phone urgence</label>
                                            <input type="tel" name="telephone_urgence" class="form-control @error('telephone_urgence') is-invalid @enderror"
                                                   value="{{ old('telephone_urgence', $patient->telephone_urgence) }}">
                                            @error('telephone_urgence')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Informations M&eacute;dicales -->
                                <div class="card mb-4 edit-section-card">
                                    <div class="card-header bg-light section-head">
                                        <div>
                                            <h5 class="mb-0"><i class="fas fa-heartbeat me-2"></i>Informations M&eacute;dicales</h5>
                                            <p>Contexte clinique, traitements et donnees de sante du patient.</p>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Groupe sanguin</label>
                                            <select name="groupe_sanguin" class="form-select @error('groupe_sanguin') is-invalid @enderror">
                                                <option value="">Non renseign&eacute;</option>
                                                <option value="A+" {{ old('groupe_sanguin', $patient->groupe_sanguin) == 'A+' ? 'selected' : '' }}>A+</option>
                                                <option value="A-" {{ old('groupe_sanguin', $patient->groupe_sanguin) == 'A-' ? 'selected' : '' }}>A-</option>
                                                <option value="B+" {{ old('groupe_sanguin', $patient->groupe_sanguin) == 'B+' ? 'selected' : '' }}>B+</option>
                                                <option value="B-" {{ old('groupe_sanguin', $patient->groupe_sanguin) == 'B-' ? 'selected' : '' }}>B-</option>
                                                <option value="AB+" {{ old('groupe_sanguin', $patient->groupe_sanguin) == 'AB+' ? 'selected' : '' }}>AB+</option>
                                                <option value="AB-" {{ old('groupe_sanguin', $patient->groupe_sanguin) == 'AB-' ? 'selected' : '' }}>AB-</option>
                                                <option value="O+" {{ old('groupe_sanguin', $patient->groupe_sanguin) == 'O+' ? 'selected' : '' }}>O+</option>
                                                <option value="O-" {{ old('groupe_sanguin', $patient->groupe_sanguin) == 'O-' ? 'selected' : '' }}>O-</option>
                                            </select>
                                            @error('groupe_sanguin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Allergies</label>
                                            <textarea name="allergies" class="form-control @error('allergies') is-invalid @enderror"
                                                      rows="2" placeholder="Liste des allergies...">{{ old('allergies', $patient->allergies) }}</textarea>
                                            @error('allergies')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Ant&eacute;c&eacute;dents m&eacute;dicaux</label>
                                            <textarea name="antecedents" class="form-control @error('antecedents') is-invalid @enderror"
                                                      rows="2" placeholder="Ant&eacute;c&eacute;dents m&eacute;dicaux...">{{ old('antecedents', $patient->antecedents) }}</textarea>
                                            @error('antecedents')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Traitements en cours</label>
                                            <textarea name="traitements" class="form-control @error('traitements') is-invalid @enderror"
                                                      rows="2" placeholder="Traitements actuels...">{{ old('traitements', $patient->traitements) }}</textarea>
                                            @error('traitements')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes & Observations -->
                        <div class="card mb-4 edit-section-card">
                            <div class="card-header bg-light section-head">
                                <div>
                                    <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notes & Observations</h5>
                                    <p>Commentaires libres et observations utiles pour le suivi du patient.</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                          rows="3" placeholder="Notes additionnelles sur le patient...">{{ old('notes', $patient->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
</form>
                </div>

                <!-- Pied de page avec statistiques -->
                        <div class="card-footer bg-light patient-footer">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <small class="text-muted">
                                <i class="fas fa-calendar-plus me-1"></i>
                                Cr&eacute;&eacute; le: {{ $patient->created_at ? $patient->created_at->format('d/m/Y &agrave; H:i') : 'N/A' }}
                            </small>
                        </div>
                        <div class="col-md-4 text-center">
                            <small class="text-muted">
                                <i class="fas fa-edit me-1"></i>
                                Derni&egrave;re modification: {{ $patient->updated_at ? $patient->updated_at->format('d/m/Y &agrave; H:i') : 'N/A' }}
                            </small>
                        </div>
                        <div class="col-md-4 text-center">
                            <small class="text-muted">
                                <i class="fas fa-file-medical me-1"></i>
                                Dossiers m&eacute;dicaux: {{ $patient->dossiers_count ?? 0 }}
                            </small>
                        </div>
                    </div>
                </div>

            </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation du formulaire
    const form = document.getElementById('editPatientForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            let valid = true;

            // Validation basique
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.classList.add('is-invalid');
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires (*)');
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
.patient-edit-page {
    width: 100%;
    max-width: none;
    padding: 0.55rem 0.4rem 1rem;
    --edit-bg:
        radial-gradient(circle at top left, rgba(44, 123, 229, 0.08) 0%, rgba(44, 123, 229, 0) 22%),
        radial-gradient(circle at 88% 10%, rgba(0, 163, 137, 0.08) 0%, rgba(0, 163, 137, 0) 16%),
        linear-gradient(180deg, #f4f7fb 0%, #eef4f9 100%);
    --edit-card: rgba(255, 255, 255, 0.96);
    --edit-border: rgba(203, 213, 225, 0.84);
    --edit-title: #16324d;
    --edit-text: #4a617a;
    --edit-primary: #2c7be5;
    --edit-primary-2: #1f6fa3;
    --edit-soft: #f7fbff;
    --edit-focus: rgba(44, 123, 229, 0.14);
    background: var(--edit-bg);
}

.patient-edit-page .patient-edit-shell {
    width: 100%;
    max-width: none;
    margin: 0;
    border: 1px solid var(--edit-border);
    border-radius: 24px;
    overflow: hidden;
    background: var(--edit-card);
    box-shadow: 0 26px 44px -34px rgba(15, 23, 42, 0.22);
    backdrop-filter: blur(8px);
}

.patient-edit-page .patient-edit-hero {
    background:
        radial-gradient(circle at top right, rgba(44, 123, 229, 0.12) 0%, rgba(44, 123, 229, 0) 34%),
        linear-gradient(180deg, #fbfdff 0%, #f4f8fd 100%) !important;
    border-bottom: 1px solid var(--edit-border);
    padding: 1rem 1.05rem 0.95rem;
    color: var(--edit-title) !important;
}

.patient-edit-page .patient-edit-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.95rem;
    flex-wrap: wrap;
}

.patient-edit-page .patient-edit-header-main {
    display: flex;
    align-items: center;
    gap: 0.7rem;
    min-width: 0;
    flex: 1 1 700px;
}

.patient-edit-page .patient-edit-title-card {
    display: flex;
    align-items: center;
    gap: 0.78rem;
    min-width: 0;
    flex: 1 1 auto;
}

.patient-edit-page .patient-edit-title-card > i {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: linear-gradient(135deg, #edf4ff 0%, #dce9ff 100%);
    color: #2d66d8;
    border: 1px solid #d6e2f5;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.72);
    font-size: 1.15rem;
}

.patient-edit-page .patient-edit-title-copy {
    min-width: 0;
}

.patient-edit-page .patient-edit-eyebrow {
    display: inline-flex;
    align-items: center;
    padding: 0.22rem 0.58rem;
    border-radius: 999px;
    border: 1px solid #c5dbf1;
    background: #eef5ff;
    color: #2f6399;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    margin-bottom: 0.36rem;
}

.patient-edit-page .patient-edit-title-copy h1 {
    margin: 0;
    color: #17365d;
    font-size: clamp(1.3rem, 2vw, 1.85rem);
    line-height: 1.08;
    font-weight: 800;
    letter-spacing: -0.03em;
}

.patient-edit-page .patient-edit-title-copy p {
    margin: 0.25rem 0 0;
    color: #6281a2;
    font-size: 0.88rem;
    font-weight: 600;
    line-height: 1.5;
}

.patient-edit-page .patient-edit-dossier-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 34px;
    padding: 0.35rem 0.8rem;
    border-radius: 999px;
    border: 1px solid #d4e2ef;
    background: linear-gradient(180deg, #ffffff 0%, #f5f9fd 100%);
    color: #1f6fa3;
    font-size: 0.79rem;
    font-weight: 800;
    white-space: nowrap;
    box-shadow: 0 10px 16px -20px rgba(15, 23, 42, 0.28);
}

.patient-edit-page .patient-edit-header-actions {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.patient-edit-page .hero-meta-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    margin-top: 0.88rem;
}

.patient-edit-page .hero-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.36rem;
    min-height: 32px;
    padding: 0.32rem 0.68rem;
    border-radius: 999px;
    border: 1px solid #d8e4ee;
    background: #f8fafc;
    color: #6b8298;
    font-size: 0.76rem;
    font-weight: 700;
    letter-spacing: 0.01em;
}

.patient-edit-page .header-back-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    min-height: 48px;
    padding: 0 18px 0 14px;
    border-radius: 16px;
    border: 1px solid rgba(191, 207, 223, 0.95);
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(245, 249, 253, 0.92) 100%);
    color: #385674;
    font-weight: 700;
    letter-spacing: -0.01em;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.92), 0 16px 28px -26px rgba(15, 23, 42, 0.28);
    transition: all 0.2s ease;
    white-space: nowrap;
    text-decoration: none;
}

.patient-edit-page .header-back-btn:hover,
.patient-edit-page .header-back-btn:focus {
    color: #1f6fa3;
    border-color: rgba(44, 123, 229, 0.3);
    background: linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(236,244,251,0.96) 100%);
    transform: translateY(-1px);
    box-shadow: 0 14px 24px -20px rgba(15, 23, 42, 0.3);
    text-decoration: none;
}

.patient-edit-page .header-back-btn-icon {
    width: 28px;
    height: 28px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(44, 123, 229, 0.1);
    color: #2c7be5;
    flex-shrink: 0;
}

.patient-edit-page .btn-custom {
    padding: 11px 18px;
    border: 1px solid transparent;
    border-radius: 14px;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s ease;
    font-size: 0.95rem;
    white-space: nowrap;
    min-height: 48px;
    box-shadow: 0 16px 26px -24px rgba(15, 23, 42, 0.22);
    text-decoration: none;
}

.patient-edit-page .btn-secondary-custom {
    background: linear-gradient(180deg, #ffffff 0%, #f4f8fc 100%);
    color: #475569;
    border-color: #d5e0ec;
}

.patient-edit-page .btn-secondary-custom:hover,
.patient-edit-page .btn-secondary-custom:focus {
    background-color: #e2e8f0;
    color: #475569;
    text-decoration: none;
}

.patient-edit-page .btn-success-custom {
    background: linear-gradient(135deg, #2c7be5 0%, #1f6fa3 100%);
    color: #ffffff;
    box-shadow: 0 16px 24px -18px rgba(44, 123, 229, 0.5);
}

.patient-edit-page .btn-success-custom:hover,
.patient-edit-page .btn-success-custom:focus {
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 20px 30px -20px rgba(31, 111, 163, 0.48);
    text-decoration: none;
}

.patient-edit-page .card-body {
    background: transparent;
    padding: 1.15rem;
}

.patient-edit-page .edit-section-card {
    border: 1px solid var(--edit-border);
    border-radius: 20px;
    background: var(--edit-card);
    box-shadow: 0 20px 36px -32px rgba(15, 23, 42, 0.2);
    overflow: hidden;
}

.patient-edit-page .section-head {
    background: linear-gradient(180deg, #fbfdff 0%, #f4f8fd 100%) !important;
    border-bottom: 1px solid var(--edit-border);
    border-top: none;
    padding: 1rem 1.1rem;
}

.patient-edit-page .section-head h5 {
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--edit-title);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.patient-edit-page .section-head p {
    margin: 0.3rem 0 0;
    color: #64748b;
    font-size: 0.84rem;
    font-weight: 600;
}

.patient-edit-page .form-label {
    font-size: 0.93rem;
    font-weight: 700;
    color: var(--edit-text);
    margin-bottom: 0.42rem;
    letter-spacing: 0.1px;
}

.patient-edit-page .form-control,
.patient-edit-page .form-select {
    border-radius: 14px;
    border: 1px solid #d8e2ee;
    min-height: 52px;
    color: #1f3553;
    background: rgba(255, 255, 255, 0.96);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.84), 0 10px 22px -24px rgba(15, 23, 42, 0.26);
    padding: 0 0.95rem;
    font-weight: 600;
    transition: all 0.2s ease;
}

.patient-edit-page textarea.form-control {
    min-height: 108px;
    padding-top: 0.8rem;
    padding-bottom: 0.8rem;
}

.patient-edit-page .form-control:focus,
.patient-edit-page .form-select:focus {
    border-color: rgba(44, 123, 229, 0.42);
    box-shadow: 0 0 0 4px var(--edit-focus), 0 14px 26px -24px rgba(31, 111, 163, 0.32);
    transform: translateY(-1px);
}

.patient-edit-page .invalid-feedback {
    font-weight: 600;
}

.patient-edit-page .patient-footer {
    border-top: 1px solid var(--edit-border);
    background: linear-gradient(180deg, #fbfdff 0%, #f4f8fd 100%) !important;
    padding-top: 0.9rem;
    padding-bottom: 0.9rem;
}

.patient-edit-page .patient-footer small {
    display: inline-block;
    font-weight: 600;
}

@media (max-width: 1200px) {
    .patient-edit-page .patient-edit-page-header {
        align-items: flex-start;
    }
}

@media (max-width: 992px) {
    .patient-edit-page .card-body {
        padding: 0.9rem;
    }
}

@media (max-width: 768px) {
    .patient-edit-page {
        padding: 0.35rem 0.15rem 0.75rem;
    }

    .patient-edit-page .patient-edit-hero {
        padding: 0.92rem 0.82rem;
    }

    .patient-edit-page .patient-edit-page-header,
    .patient-edit-page .patient-edit-header-main {
        gap: 0.6rem;
    }

    .patient-edit-page .patient-edit-title-card {
        align-items: flex-start;
    }

    .patient-edit-page .patient-edit-title-card > i {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        font-size: 1rem;
    }

    .patient-edit-page .patient-edit-title-copy h1 {
        font-size: 1.18rem;
    }

    .patient-edit-page .patient-edit-title-copy p {
        font-size: 0.84rem;
    }

    .patient-edit-page .patient-edit-header-actions {
        width: 100%;
        justify-content: stretch;
    }

    .patient-edit-page .patient-edit-header-actions .btn-custom,
    .patient-edit-page .header-back-btn {
        width: 100%;
        justify-content: center;
    }

    .patient-edit-page .patient-footer .col-md-4 {
        margin-bottom: 0.45rem;
    }

    .patient-edit-page .patient-edit-shell,
    .patient-edit-page .edit-section-card {
        border-radius: 18px;
    }
}

body.dark-mode .patient-edit-page,
body.theme-dark .patient-edit-page {
    --edit-bg:
        radial-gradient(circle at top left, rgba(44, 123, 229, 0.12) 0%, rgba(44, 123, 229, 0) 22%),
        linear-gradient(180deg, #0f172a 0%, #111827 100%);
    --edit-card: rgba(18, 36, 59, 0.96);
    --edit-border: #2a4a6f;
    --edit-title: #d4e7ff;
    --edit-text: #aac3df;
    --edit-soft: #142a45;
    --edit-focus: rgba(88, 161, 230, 0.28);
}

body.dark-mode .patient-edit-page .header-back-btn,
body.theme-dark .patient-edit-page .header-back-btn {
    background: linear-gradient(150deg, #183552 0%, #14304b 100%) !important;
    border-color: #365b7d !important;
    color: #d2e6fb !important;
}

body.dark-mode .patient-edit-page .patient-edit-title-card > i,
body.theme-dark .patient-edit-page .patient-edit-title-card > i {
    background: linear-gradient(150deg, #17314f 0%, #1a3859 100%);
    border-color: #35506a;
    color: #9fcaff;
}

body.dark-mode .patient-edit-page .patient-edit-eyebrow,
body.theme-dark .patient-edit-page .patient-edit-eyebrow {
    background: #173858;
    border-color: #3a638d;
    color: #d3e8ff;
}

body.dark-mode .patient-edit-page .patient-edit-title-copy h1,
body.theme-dark .patient-edit-page .patient-edit-title-copy h1 {
    color: #e3f0ff;
}

body.dark-mode .patient-edit-page .patient-edit-title-copy p,
body.theme-dark .patient-edit-page .patient-edit-title-copy p,
body.dark-mode .patient-edit-page .patient-edit-dossier-badge,
body.theme-dark .patient-edit-page .patient-edit-dossier-badge {
    color: #a9c0da;
}

body.dark-mode .patient-edit-page .patient-edit-dossier-badge,
body.theme-dark .patient-edit-page .patient-edit-dossier-badge {
    background: #132b46;
    border-color: #355b84;
}

body.dark-mode .patient-edit-page .header-back-btn-icon,
body.theme-dark .patient-edit-page .header-back-btn-icon {
    background: rgba(119, 183, 255, 0.16);
    color: #9fd0ff;
}

body.dark-mode .patient-edit-page .btn-secondary-custom,
body.theme-dark .patient-edit-page .btn-secondary-custom {
    border-color: #365b7d !important;
    background: linear-gradient(150deg, #183552 0%, #14304b 100%) !important;
    color: #d2e6fb !important;
}

body.dark-mode .patient-edit-page .btn-success-custom,
body.theme-dark .patient-edit-page .btn-success-custom {
    color: #ffffff !important;
}

body.dark-mode .patient-edit-page .hero-chip,
body.theme-dark .patient-edit-page .hero-chip {
    background: rgba(17, 34, 54, 0.9);
    border-color: #35506a;
    color: #a5c1db;
}

body.dark-mode .patient-edit-page .form-control,
body.dark-mode .patient-edit-page .form-select,
body.theme-dark .patient-edit-page .form-control,
body.theme-dark .patient-edit-page .form-select {
    border-color: #355a83;
    background: #11263e;
    color: #d5e7fb;
}

body.dark-mode .patient-edit-page .form-control::placeholder,
body.theme-dark .patient-edit-page .form-control::placeholder {
    color: #8fb0d1;
}

body.dark-mode .patient-edit-page .patient-footer,
body.theme-dark .patient-edit-page .patient-footer {
    background: #122842 !important;
}
</style>
@include('patients.partials.button-theme')
@endpush


