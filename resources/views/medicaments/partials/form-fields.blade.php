@php
    $model = $medicament ?? null;
    $datePeremptionValue = old('date_peremption', $model && $model->date_peremption ? $model->date_peremption->format('Y-m-d') : '');
    $dateFabricationValue = old('date_fabrication', $model && $model->date_fabrication ? $model->date_fabrication->format('Y-m-d') : '');
@endphp

<section class="med-form-section">
    <div class="med-form-section-head">
        <div class="med-form-section-title">
            <span class="med-form-section-icon"><i class="fas fa-capsules"></i></span>
            <div>
                <h3>Identification du médicament</h3>
                <p class="med-form-section-help">Nom, référence, catégorie, laboratoire et type pour structurer le catalogue.</p>
            </div>
        </div>
        <span class="med-form-section-tag">Catalogue</span>
    </div>
    <div class="med-form-section-body">
        <div class="med-form-grid">
            <div class="med-field">
                <label for="nom_commercial">Nom commercial <span class="required">*</span></label>
                <input type="text" name="nom_commercial" id="nom_commercial" class="form-control @error('nom_commercial') is-invalid @enderror" value="{{ old('nom_commercial', $model->nom_commercial ?? '') }}" required>
                <p class="med-field-hint">Libellé principal affiché dans le catalogue et les recherches.</p>
                @error('nom_commercial')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field">
                <label for="dci">DCI</label>
                <input type="text" name="dci" id="dci" class="form-control @error('dci') is-invalid @enderror" value="{{ old('dci', $model->dci ?? '') }}" placeholder="Ex: Paracétamol">
                <p class="med-field-hint">Dénomination commune internationale pour la traçabilité clinique.</p>
                @error('dci')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field">
                <label for="code_cip">Code CIP</label>
                <input type="text" name="code_cip" id="code_cip" class="form-control @error('code_cip') is-invalid @enderror" value="{{ old('code_cip', $model->code_cip ?? '') }}" placeholder="34009XXXXXXXXX">
                @error('code_cip')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field">
                <label for="code_medicament">Code médicament</label>
                <input type="text" name="code_medicament" id="code_medicament" class="form-control @error('code_medicament') is-invalid @enderror" value="{{ old('code_medicament', $model->code_medicament ?? '') }}">
                @error('code_medicament')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field">
                <label for="categorie">Catégorie</label>
                <input type="text" name="categorie" id="categorie" class="form-control @error('categorie') is-invalid @enderror" value="{{ old('categorie', $model->categorie ?? '') }}" placeholder="Ex: Antalgique, Antibiotique">
                @error('categorie')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field">
                <label for="laboratoire">Laboratoire</label>
                <input type="text" name="laboratoire" id="laboratoire" class="form-control @error('laboratoire') is-invalid @enderror" value="{{ old('laboratoire', $model->laboratoire ?? '') }}" placeholder="Ex: Sanofi, Pfizer">
                @error('laboratoire')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field">
                <label for="type">Type <span class="required">*</span></label>
                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                    <option value="">Choisir...</option>
                    <option value="prescription" {{ old('type', $model->type ?? '') == 'prescription' ? 'selected' : '' }}>Prescription</option>
                    <option value="otc" {{ old('type', $model->type ?? '') == 'otc' ? 'selected' : '' }}>OTC (sans ordonnance)</option>
                    <option value="controlled" {{ old('type', $model->type ?? '') == 'controlled' ? 'selected' : '' }}>Contrôlé</option>
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field">
                <label for="classe_therapeutique">Classe thérapeutique</label>
                <input type="text" name="classe_therapeutique" id="classe_therapeutique" class="form-control @error('classe_therapeutique') is-invalid @enderror" value="{{ old('classe_therapeutique', $model->classe_therapeutique ?? '') }}" placeholder="Ex: Analgésiques, Antibiotiques">
                @error('classe_therapeutique')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</section>

<section class="med-form-section">
    <div class="med-form-section-head">
        <div class="med-form-section-title">
            <span class="med-form-section-icon"><i class="fas fa-money-check-dollar"></i></span>
            <div>
                <h3>Tarification et statut</h3>
                <p class="med-form-section-help">Prix d’achat, prix de vente, remboursement et statut administratif du produit.</p>
            </div>
        </div>
        <span class="med-form-section-tag">Tarif</span>
    </div>
    <div class="med-form-section-body">
        <div class="med-form-grid">
            <div class="med-field">
                <label for="prix_achat">Prix d'achat (DH) <span class="required">*</span></label>
                <input type="number" name="prix_achat" id="prix_achat" class="form-control @error('prix_achat') is-invalid @enderror" value="{{ old('prix_achat', $model->prix_achat ?? '') }}" step="0.01" min="0" required>
                @error('prix_achat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field">
                <label for="prix_vente">Prix de vente (DH) <span class="required">*</span></label>
                <input type="number" name="prix_vente" id="prix_vente" class="form-control @error('prix_vente') is-invalid @enderror" value="{{ old('prix_vente', $model->prix_vente ?? '') }}" step="0.01" min="0" required>
                @error('prix_vente')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field">
                <label for="taux_remboursement">Taux de remboursement (%)</label>
                <input type="number" name="taux_remboursement" id="taux_remboursement" class="form-control @error('taux_remboursement') is-invalid @enderror" value="{{ old('taux_remboursement', $model->taux_remboursement ?? '') }}" min="0" max="100" step="0.01">
                @error('taux_remboursement')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field">
                <label for="prix_remboursement">Prix de remboursement (DH)</label>
                <input type="number" name="prix_remboursement" id="prix_remboursement" class="form-control @error('prix_remboursement') is-invalid @enderror" value="{{ old('prix_remboursement', $model->prix_remboursement ?? '') }}" step="0.01" min="0">
                @error('prix_remboursement')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field">
                <label for="statut">Statut <span class="required">*</span></label>
                <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                    <option value="actif" {{ old('statut', $model->statut ?? 'actif') == 'actif' ? 'selected' : '' }}>Actif</option>
                    <option value="inactif" {{ old('statut', $model->statut ?? '') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                </select>
                @error('statut')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field full">
                <label>Indicateurs de prise en charge</label>
                <div class="med-form-checks">
                    <label class="med-check-card" for="generique">
                        <input type="checkbox" name="generique" id="generique" class="form-check-input @error('generique') is-invalid @enderror" value="1" {{ old('generique', $model->generique ?? false) ? 'checked' : '' }}>
                        <span class="med-check-body">
                            <strong>Générique</strong>
                            <span>Identifier rapidement si le médicament appartient à une référence générique.</span>
                        </span>
                    </label>

                    <label class="med-check-card" for="remboursable">
                        <input type="checkbox" name="remboursable" id="remboursable" class="form-check-input @error('remboursable') is-invalid @enderror" value="1" {{ old('remboursable', $model->remboursable ?? false) ? 'checked' : '' }}>
                        <span class="med-check-body">
                            <strong>Remboursable</strong>
                            <span>Activer la prise en charge et les calculs associés au remboursement.</span>
                        </span>
                    </label>
                </div>
                @error('generique')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @error('remboursable')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</section>

<section class="med-form-section">
    <div class="med-form-section-head">
        <div class="med-form-section-title">
            <span class="med-form-section-icon"><i class="fas fa-boxes-stacked"></i></span>
            <div>
                <h3>Stock et traçabilité</h3>
                <p class="med-form-section-help">Quantités, seuils, dates, lot, fournisseur, présentation et voie d’administration.</p>
            </div>
        </div>
        <span class="med-form-section-tag">Stock</span>
    </div>
    <div class="med-form-section-body">
        <div class="med-form-grid">
            <div class="med-field">
                <label for="quantite_stock">Quantité en stock <span class="required">*</span></label>
                <input type="number" name="quantite_stock" id="quantite_stock" class="form-control @error('quantite_stock') is-invalid @enderror" value="{{ old('quantite_stock', $model->quantite_stock ?? 0) }}" min="0" required>
                @error('quantite_stock')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field">
                <label for="quantite_seuil">Seuil d'alerte</label>
                <input type="number" name="quantite_seuil" id="quantite_seuil" class="form-control @error('quantite_seuil') is-invalid @enderror" value="{{ old('quantite_seuil', $model->quantite_seuil ?? '') }}" min="0" placeholder="Ex: 10">
                @error('quantite_seuil')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field">
                <label for="quantite_ideale">Quantité idéale</label>
                <input type="number" name="quantite_ideale" id="quantite_ideale" class="form-control @error('quantite_ideale') is-invalid @enderror" value="{{ old('quantite_ideale', $model->quantite_ideale ?? '') }}" min="0" placeholder="Ex: 50">
                @error('quantite_ideale')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field">
                <label for="date_peremption">Date de péremption</label>
                <input type="date" name="date_peremption" id="date_peremption" class="form-control @error('date_peremption') is-invalid @enderror" value="{{ $datePeremptionValue }}">
                @error('date_peremption')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field">
                <label for="date_fabrication">Date de fabrication</label>
                <input type="date" name="date_fabrication" id="date_fabrication" class="form-control @error('date_fabrication') is-invalid @enderror" value="{{ $dateFabricationValue }}">
                @error('date_fabrication')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field">
                <label for="numero_lot">Numéro de lot</label>
                <input type="text" name="numero_lot" id="numero_lot" class="form-control @error('numero_lot') is-invalid @enderror" value="{{ old('numero_lot', $model->numero_lot ?? '') }}">
                @error('numero_lot')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field">
                <label for="fournisseur">Fournisseur</label>
                <input type="text" name="fournisseur" id="fournisseur" class="form-control @error('fournisseur') is-invalid @enderror" value="{{ old('fournisseur', $model->fournisseur ?? '') }}">
                @error('fournisseur')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field">
                <label for="presentation">Présentation</label>
                <input type="text" name="presentation" id="presentation" class="form-control @error('presentation') is-invalid @enderror" value="{{ old('presentation', $model->presentation ?? '') }}" placeholder="Ex: Comprimé, Sirop">
                @error('presentation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field">
                <label for="voie_administration">Voie d'administration</label>
                <select name="voie_administration" id="voie_administration" class="form-select @error('voie_administration') is-invalid @enderror">
                    <option value="">Choisir...</option>
                    <option value="orale" {{ old('voie_administration', $model->voie_administration ?? '') == 'orale' ? 'selected' : '' }}>Orale</option>
                    <option value="injectable" {{ old('voie_administration', $model->voie_administration ?? '') == 'injectable' ? 'selected' : '' }}>Injectable</option>
                    <option value="topique" {{ old('voie_administration', $model->voie_administration ?? '') == 'topique' ? 'selected' : '' }}>Topique</option>
                    <option value="rectale" {{ old('voie_administration', $model->voie_administration ?? '') == 'rectale' ? 'selected' : '' }}>Rectale</option>
                    <option value="inhalation" {{ old('voie_administration', $model->voie_administration ?? '') == 'inhalation' ? 'selected' : '' }}>Inhalation</option>
                </select>
                @error('voie_administration')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</section>

<section class="med-form-section">
    <div class="med-form-section-head">
        <div class="med-form-section-title">
            <span class="med-form-section-icon"><i class="fas fa-stethoscope"></i></span>
            <div>
                <h3>Informations cliniques</h3>
                <p class="med-form-section-help">Posologie, contre-indications, effets secondaires et consignes de conservation.</p>
            </div>
        </div>
        <span class="med-form-section-tag">Médical</span>
    </div>
    <div class="med-form-section-body">
        <div class="med-form-grid">
            <div class="med-field full">
                <label for="posologie">Posologie</label>
                <textarea name="posologie" id="posologie" class="form-control @error('posologie') is-invalid @enderror" rows="3">{{ old('posologie', $model->posologie ?? '') }}</textarea>
                @error('posologie')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field full">
                <label for="contre_indications">Contre-indications</label>
                <textarea name="contre_indications" id="contre_indications" class="form-control @error('contre_indications') is-invalid @enderror" rows="3">{{ old('contre_indications', $model->contre_indications ?? '') }}</textarea>
                @error('contre_indications')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field full">
                <label for="effets_secondaires">Effets secondaires</label>
                <textarea name="effets_secondaires" id="effets_secondaires" class="form-control @error('effets_secondaires') is-invalid @enderror" rows="3">{{ old('effets_secondaires', $model->effets_secondaires ?? '') }}</textarea>
                @error('effets_secondaires')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field full">
                <label for="precautions">Précautions d'emploi</label>
                <textarea name="precautions" id="precautions" class="form-control @error('precautions') is-invalid @enderror" rows="3">{{ old('precautions', $model->precautions ?? '') }}</textarea>
                @error('precautions')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="med-field full">
                <label for="conservation">Conditions de conservation</label>
                <textarea name="conservation" id="conservation" class="form-control @error('conservation') is-invalid @enderror" rows="3">{{ old('conservation', $model->conservation ?? '') }}</textarea>
                @error('conservation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</section>