@php
    $medecin = $medecin ?? null;
    $specialites = $specialites ?? [];
    $statusOptions = $statusOptions ?? [
        'actif' => 'Actif',
        'inactif' => 'Inactif',
        'en_conge' => 'En conge',
        'retraite' => 'Retraite',
    ];
    $selectedSpecialite = old('specialite', $medecin->specialite ?? '');
    $currentPhotoUrl = $medecin?->photo_path ? asset('storage/' . $medecin->photo_path) : null;
    $currentSignatureUrl = $medecin?->signature_path ? asset('storage/' . $medecin->signature_path) : null;
@endphp

<section class="medecin-form-section">
    <h6 class="medecin-section-title">Identite</h6>
    <div class="row g-3">
        <div class="col-md-3">
            <label for="civilite" class="form-label">Civilite <span class="text-danger">*</span></label>
            <select name="civilite" id="civilite" class="form-select @error('civilite') is-invalid @enderror" required>
                <option value="">Choisir...</option>
                <option value="Dr." {{ old('civilite', $medecin->civilite ?? '') === 'Dr.' ? 'selected' : '' }}>Dr.</option>
                <option value="Pr." {{ old('civilite', $medecin->civilite ?? '') === 'Pr.' ? 'selected' : '' }}>Pr.</option>
                <option value="M." {{ old('civilite', $medecin->civilite ?? '') === 'M.' ? 'selected' : '' }}>M.</option>
                <option value="Mme" {{ old('civilite', $medecin->civilite ?? '') === 'Mme' ? 'selected' : '' }}>Mme</option>
            </select>
            @error('civilite')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
            <input type="text" name="nom" id="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom', $medecin->nom ?? '') }}" required>
            @error('nom')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-5">
            <label for="prenom" class="form-label">Prenom <span class="text-danger">*</span></label>
            <input type="text" name="prenom" id="prenom" class="form-control @error('prenom') is-invalid @enderror" value="{{ old('prenom', $medecin->prenom ?? '') }}" required>
            @error('prenom')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</section>

<section class="medecin-form-section">
    <h6 class="medecin-section-title">Profil professionnel</h6>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="specialite" class="form-label">Specialite</label>
            <select name="specialite" id="specialite" class="form-select @error('specialite') is-invalid @enderror">
                <option value="">Choisir une specialite...</option>
                @foreach($specialites as $specialite)
                    <option value="{{ $specialite }}" {{ $selectedSpecialite === $specialite ? 'selected' : '' }}>
                        {{ $specialite }}
                    </option>
                @endforeach
                @if($selectedSpecialite && !in_array($selectedSpecialite, $specialites, true))
                    <option value="{{ $selectedSpecialite }}" selected>{{ $selectedSpecialite }}</option>
                @endif
            </select>
            @error('specialite')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="numero_ordre" class="form-label">Numero d'ordre</label>
            <input type="text" name="numero_ordre" id="numero_ordre" class="form-control @error('numero_ordre') is-invalid @enderror" value="{{ old('numero_ordre', $medecin->numero_ordre ?? '') }}" placeholder="Numero d'ordre des medecins">
            @error('numero_ordre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="telephone" class="form-label">Telephone</label>
            <input type="tel" name="telephone" id="telephone" class="form-control @error('telephone') is-invalid @enderror" value="{{ old('telephone', $medecin->telephone ?? '') }}">
            @error('telephone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $medecin->email ?? '') }}">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</section>

<section class="medecin-form-section">
    <h6 class="medecin-section-title">Cabinet et facturation</h6>
    <div class="row g-3">
        <div class="col-md-8">
            <label for="adresse_cabinet" class="form-label">Adresse du cabinet</label>
            <textarea name="adresse_cabinet" id="adresse_cabinet" class="form-control @error('adresse_cabinet') is-invalid @enderror" rows="2">{{ old('adresse_cabinet', $medecin->adresse_cabinet ?? '') }}</textarea>
            @error('adresse_cabinet')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label for="ville" class="form-label">Ville</label>
            <input type="text" name="ville" id="ville" class="form-control @error('ville') is-invalid @enderror" value="{{ old('ville', $medecin->ville ?? '') }}">
            @error('ville')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label for="code_postal" class="form-label">Code postal</label>
            <input type="text" name="code_postal" id="code_postal" class="form-control @error('code_postal') is-invalid @enderror" value="{{ old('code_postal', $medecin->code_postal ?? '') }}">
            @error('code_postal')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
            <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                @foreach($statusOptions as $statusValue => $statusLabel)
                    <option value="{{ $statusValue }}" {{ old('statut', $medecin->statut ?? 'actif') === $statusValue ? 'selected' : '' }}>
                        {{ $statusLabel }}
                    </option>
                @endforeach
            </select>
            @error('statut')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label for="tarif_consultation" class="form-label">Tarif consultation (DH)</label>
            <input type="number" name="tarif_consultation" id="tarif_consultation" class="form-control @error('tarif_consultation') is-invalid @enderror" value="{{ old('tarif_consultation', $medecin->tarif_consultation ?? '') }}" step="0.01" min="0">
            @error('tarif_consultation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="date_embauche" class="form-label">Date d'embauche</label>
            <input type="date" name="date_embauche" id="date_embauche" class="form-control @error('date_embauche') is-invalid @enderror" value="{{ old('date_embauche', isset($medecin) && $medecin->date_embauche ? $medecin->date_embauche->format('Y-m-d') : '') }}">
            @error('date_embauche')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="date_depart" class="form-label">Date de depart</label>
            <input type="date" name="date_depart" id="date_depart" class="form-control @error('date_depart') is-invalid @enderror" value="{{ old('date_depart', isset($medecin) && $medecin->date_depart ? $medecin->date_depart->format('Y-m-d') : '') }}">
            @error('date_depart')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</section>

<section class="medecin-form-section">
    <h6 class="medecin-section-title">Documents et notes</h6>
    <div class="row g-3">
        <div class="col-12">
            <label for="notes" class="form-label">Notes internes</label>
            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="4">{{ old('notes', $medecin->notes ?? '') }}</textarea>
            @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="photo" class="form-label">Photo du medecin</label>
            <input type="file" name="photo" id="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*" data-preview-target="photoPreview">
            <small class="form-text">Formats acceptes: JPEG, PNG, JPG. Taille max: 5MB</small>
            @error('photo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="medecin-file-preview mt-2" id="photoPreview">
                @if($currentPhotoUrl)
                    <img src="{{ $currentPhotoUrl }}" alt="Photo du medecin">
                @else
                    <i class="fas fa-image"></i>
                    <span>Apercu photo</span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <label for="signature" class="form-label">Signature numerique</label>
            <input type="file" name="signature" id="signature" class="form-control @error('signature') is-invalid @enderror" accept="image/png" data-preview-target="signaturePreview">
            <small class="form-text">Format PNG uniquement. Taille max: 2MB</small>
            @error('signature')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="medecin-file-preview signature mt-2" id="signaturePreview">
                @if($currentSignatureUrl)
                    <img src="{{ $currentSignatureUrl }}" alt="Signature du medecin">
                @else
                    <i class="fas fa-signature"></i>
                    <span>Apercu signature</span>
                @endif
            </div>
        </div>
    </div>
</section>
