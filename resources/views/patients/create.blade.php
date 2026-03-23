@extends('layouts.app')

@section('title', 'Ajouter un Patient')

@section('content')
<style>
.gradient-blue { background: linear-gradient(135deg, #2c7be5 0%, #1f6fa3 100%); }
.gradient-green { background: linear-gradient(135deg, #00a389 0%, #00806c 100%); }
.gradient-red { background: linear-gradient(135deg, #e5533d 0%, #c53f2d 100%); }
.patient-create-page {
    background:
        radial-gradient(circle at top left, rgba(44, 123, 229, 0.08) 0%, rgba(44, 123, 229, 0) 24%),
        radial-gradient(circle at 88% 10%, rgba(0, 163, 137, 0.08) 0%, rgba(0, 163, 137, 0) 18%),
        linear-gradient(180deg, #f4f7fb 0%, #eef4f9 100%);
}
.patient-create-shell {
    width: 100%;
    max-width: none;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
}
.patient-create-header-block {
    padding: 24px 26px;
    border-radius: 24px;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(248, 251, 255, 0.92) 100%);
    border: 1px solid rgba(203, 213, 225, 0.78);
    box-shadow: 0 20px 40px -32px rgba(15, 23, 42, 0.22);
    margin-bottom: 22px;
    backdrop-filter: blur(8px);
}
.patient-create-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 999px;
    background: rgba(44, 123, 229, 0.09);
    color: #1f6fa3;
    font-size: 0.8rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin-bottom: 14px;
}
.page-title {
    font-size: clamp(2rem, 3vw, 2.7rem);
    font-weight: 800;
    letter-spacing: -0.03em;
    color: #16324d;
}
.page-subtitle {
    color: #64748b;
    margin: 10px 0 0;
    font-size: 1rem;
    max-width: 60ch;
}
.breadcrumb-current {
    color: #16324d;
}
.breadcrumb-bar {
    display: inline-flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    padding: 10px 14px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(203, 213, 225, 0.78);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.84);
}
.header-return-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    min-height: 48px;
    padding: 0 20px 0 16px;
    border-radius: 16px;
    border: 1px solid rgba(191, 207, 223, 0.95);
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(245, 249, 253, 0.92) 100%);
    color: #385674;
    font-weight: 700;
    letter-spacing: -0.01em;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.92), 0 16px 28px -26px rgba(15, 23, 42, 0.28);
    transition: all 0.2s ease;
}
.header-return-btn:hover,
.header-return-btn:focus {
    color: #1f6fa3;
    border-color: rgba(44, 123, 229, 0.3);
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(236, 244, 251, 0.98) 100%);
    transform: translateY(-1px);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.96), 0 18px 32px -24px rgba(31, 111, 163, 0.22);
}
.header-return-btn-icon {
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
.photo-circle {
    width: 152px;
    height: 152px;
    border-radius: 50%;
    border: 2px dashed rgba(44, 123, 229, 0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(180deg, rgba(244, 248, 253, 0.96) 0%, rgba(234, 242, 249, 0.92) 100%);
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.9), 0 16px 34px -28px rgba(15, 23, 42, 0.32);
    overflow: hidden;
}
.photo-circle:hover {
    border-color: #1f6fa3;
    background: linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(234, 242, 249, 1) 100%);
    transform: translateY(-1px);
}
.photo-placeholder-icon {
    font-size: 3.8rem;
    line-height: 1;
    color: #1f6fa3;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 84px;
    height: 84px;
    border-radius: 50%;
    background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.98) 0%, rgba(219, 234, 247, 0.92) 100%);
    box-shadow: 0 14px 28px -22px rgba(31, 111, 163, 0.4);
}
.photo-circle img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}
.section-header {
    color: white;
    padding: 18px 22px;
    border-radius: 20px 20px 0 0;
    font-weight: 700;
    font-size: 1rem;
    letter-spacing: 0.01em;
    display: flex;
    align-items: center;
    gap: 10px;
}
.form-card {
    border-radius: 20px;
    box-shadow: 0 20px 40px -32px rgba(15, 23, 42, 0.2);
    border: 1px solid rgba(203, 213, 225, 0.78);
    margin-bottom: 25px;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.96);
    backdrop-filter: blur(8px);
}
.form-card-body {
    padding: 28px;
}
.form-label {
    font-weight: 700;
    color: #16324d;
    margin-bottom: 10px;
    font-size: 14px;
    letter-spacing: 0.01em;
}
.form-control, .form-select {
    min-height: 52px;
    border: 1px solid #d8e2ee;
    border-radius: 14px;
    padding: 0 16px;
    font-size: 0.96rem;
    font-weight: 600;
    background: rgba(255, 255, 255, 0.92);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.84), 0 10px 22px -24px rgba(15, 23, 42, 0.26);
    transition: all 0.2s ease;
}
textarea.form-control {
    min-height: 112px;
    padding: 14px 16px;
    resize: vertical;
}
.form-control:focus, .form-select:focus {
    border-color: rgba(44, 123, 229, 0.42);
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(44, 123, 229, 0.12), 0 14px 28px -26px rgba(31, 111, 163, 0.35);
    transform: translateY(-1px);
}
.form-control::placeholder,
.form-select::placeholder {
    color: #8aa1b7;
    font-weight: 500;
}
.required::after { content: " *"; color: #ef4444; font-weight: bold; }
.sticky-sidebar {
    position: sticky;
    top: 24px;
}
.sidebar-card {
    background: linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(248, 251, 255, 0.94) 100%);
}
.sidebar-title {
    color: #16324d;
    font-size: 1.02rem;
    font-weight: 800;
}
.sidebar-divider {
    margin: 22px 0;
    border-color: rgba(203, 213, 225, 0.72);
}
.photo-meta {
    margin-top: 14px;
    padding: 12px 14px;
    border-radius: 14px;
    background: rgba(44, 123, 229, 0.06);
    color: #48627d;
    font-size: 0.86rem;
}
.btn-gradient-blue {
    background: linear-gradient(135deg, #2c7be5 0%, #1f6fa3 100%);
    border: none;
    color: white;
    font-weight: 700;
    min-height: 52px;
    padding: 0 24px;
    border-radius: 14px;
    box-shadow: 0 10px 22px -16px rgba(44, 123, 229, 0.52);
    transition: transform 0.2s, box-shadow 0.2s;
}
.btn-gradient-blue:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 24px -18px rgba(44, 123, 229, 0.58);
    color: white;
}
.sidebar-reset-btn {
    min-height: 52px;
    border-radius: 14px;
    font-weight: 700;
    border-color: rgba(203, 213, 225, 0.92);
    background: rgba(255,255,255,0.84);
    color: #48627d;
}
.sidebar-reset-btn:hover,
.sidebar-reset-btn:focus {
    color: #1f6fa3;
    border-color: rgba(44, 123, 229, 0.24);
    background: rgba(242, 247, 253, 0.98);
    transform: translateY(-1px);
}
.info-box {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    background: linear-gradient(180deg, rgba(239, 246, 255, 0.95) 0%, rgba(230, 241, 250, 0.92) 100%);
    border: 1px solid rgba(179, 208, 233, 0.9);
    padding: 16px 18px;
    border-radius: 18px;
    color: #0c4a6e;
    margin-bottom: 25px;
    box-shadow: 0 14px 28px -30px rgba(15, 23, 42, 0.25);
}
.info-box-icon {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: rgba(44, 123, 229, 0.14);
    color: #2c7be5;
}
.help-box {
    background: linear-gradient(180deg, rgba(240, 249, 255, 0.9) 0%, rgba(232, 245, 252, 0.88) 100%);
    padding: 16px;
    border-radius: 16px;
    font-size: 13px;
    border: 1px solid rgba(190, 222, 244, 0.92);
}
.help-box strong {
    color: #1f6fa3;
}
.section-note {
    display: block;
    margin-top: 4px;
    font-size: 0.84rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.88);
}
.form-check {
    min-height: 48px;
    margin-right: 16px;
}
.form-check-input {
    width: 18px;
    height: 18px;
    margin-top: 0.15rem;
}
.form-check-input:checked {
    background-color: #2c7be5;
    border-color: #2c7be5;
}
.form-check-label {
    font-weight: 600;
    color: #48627d;
}
.invalid-feedback.d-block,
.text-danger.small {
    font-weight: 600;
}

body.dark-mode .patient-create-page {
    background:
        radial-gradient(circle at top left, rgba(44, 123, 229, 0.12) 0%, rgba(44, 123, 229, 0) 24%),
        linear-gradient(180deg, #0f172a 0%, #111827 100%);
}
body.dark-mode .patient-create-page .page-title,
body.dark-mode .patient-create-page .breadcrumb-current,
body.dark-mode .patient-create-page .form-label,
body.dark-mode .patient-create-page h6,
body.dark-mode .patient-create-page .form-check-label {
    color: #e5e7eb !important;
}
body.dark-mode .patient-create-page .page-subtitle,
body.dark-mode .patient-create-page .patient-create-eyebrow,
body.dark-mode .patient-create-page .photo-meta,
body.dark-mode .patient-create-page .sidebar-title {
    color: #dbeafe !important;
}
body.dark-mode .patient-create-page .text-muted,
body.dark-mode .patient-create-page nav,
body.dark-mode .patient-create-page nav a {
    color: #9ca3af !important;
}
body.dark-mode .patient-create-header-block {
    background: linear-gradient(180deg, rgba(17, 24, 39, 0.98) 0%, rgba(15, 23, 42, 0.94) 100%);
    border-color: #334155;
    box-shadow: 0 24px 44px -30px rgba(2, 6, 23, 0.65);
}
body.dark-mode .patient-create-page .breadcrumb-bar,
body.dark-mode .patient-create-page .filters-summary,
body.dark-mode .patient-create-page .header-return-btn,
body.dark-mode .patient-create-page .sidebar-reset-btn {
    background: rgba(15, 23, 42, 0.9);
    border-color: #334155;
    color: #dbeafe;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.03);
}
body.dark-mode .patient-create-page .header-return-btn-icon {
    background: rgba(93, 165, 255, 0.14);
    color: #9ecbff;
}
body.dark-mode .patient-create-page .form-card {
    background: #1f2937;
    border: 1px solid #334155;
    box-shadow: 0 8px 24px rgba(2, 6, 23, 0.45);
}
body.dark-mode .patient-create-page .form-control,
body.dark-mode .patient-create-page .form-select,
body.dark-mode .patient-create-page textarea {
    background-color: #0f172a;
    border-color: #334155;
    color: #e5e7eb;
}
body.dark-mode .patient-create-page .form-control::placeholder,
body.dark-mode .patient-create-page .form-select::placeholder,
body.dark-mode .patient-create-page textarea::placeholder {
    color: #94a3b8;
}
body.dark-mode .patient-create-page .form-control:focus,
body.dark-mode .patient-create-page .form-select:focus,
body.dark-mode .patient-create-page textarea:focus {
    background-color: #0f172a;
    border-color: #0ea5e9;
    color: #f8fafc;
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.2);
}
body.dark-mode .patient-create-page .info-box {
    background: #0b2942;
    border-color: #1d4f75;
    color: #dbeafe;
}
body.dark-mode .patient-create-page .info-box-icon {
    background: rgba(56, 189, 248, 0.16);
    color: #7dd3fc;
}
body.dark-mode .patient-create-page .photo-circle {
    background: #1e293b;
    border-color: #38bdf8;
}
body.dark-mode .patient-create-page .photo-circle:hover {
    background: #0f172a;
    border-color: #7dd3fc;
}
body.dark-mode .patient-create-page .photo-placeholder-icon {
    color: #7dd3fc;
    background: radial-gradient(circle at 30% 30%, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.92) 100%);
    box-shadow: 0 14px 28px -22px rgba(2, 6, 23, 0.72);
}
body.dark-mode .patient-create-page .help-box {
    background: #0f172a;
    border: 1px solid #334155;
}
body.dark-mode .patient-create-page .help-box strong {
    color: #7dd3fc;
}
body.dark-mode .patient-create-page .sidebar-divider {
    border-color: #334155;
}
body.dark-mode .patient-create-page hr {
    border-color: #334155;
}
body.dark-mode .patient-create-page .btn-outline-secondary {
    color: #cbd5e1;
    border-color: #475569;
}
body.dark-mode .patient-create-page .btn-outline-secondary:hover {
    background: #334155;
    color: #f8fafc;
}

@media (max-width: 991.98px) {
    .sticky-sidebar {
        position: static;
        top: auto;
    }
}

@media (max-width: 767.98px) {
    .patient-create-page {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }
    .patient-create-header {
        flex-direction: column;
        align-items: stretch !important;
        gap: 12px;
    }
    .patient-create-header .btn {
        width: 100%;
    }
    .page-title {
        font-size: 26px;
    }
    .patient-create-header-block {
        padding: 18px;
        border-radius: 20px;
    }
    .breadcrumb-bar {
        display: flex;
    }
    .section-header {
        padding: 14px 16px;
        font-size: 16px;
        border-radius: 18px 18px 0 0;
    }
    .form-card-body {
        padding: 16px;
    }
    .photo-circle {
        width: 110px;
        height: 110px;
    }
    .form-card {
        border-radius: 18px;
    }
    .form-control,
    .form-select,
    .btn-gradient-blue,
    .sidebar-reset-btn,
    .header-return-btn {
        min-height: 50px;
    }
}
</style>

<div class="container-fluid py-5 patient-create-page">
    <div class="patient-create-shell">
        
        <!-- Header -->
        <div class="patient-create-header-block">
            <div class="d-flex justify-content-between align-items-start mb-3 patient-create-header">
                <div>
                    <span class="patient-create-eyebrow"><i class="fas fa-id-card-clip"></i>Nouveau dossier patient</span>
                    <h1 class="mb-2 page-title">
                        <i class="fas fa-user-plus text-primary me-2"></i> Ajouter un Patient
                    </h1>
                    <p class="page-subtitle">Enregistrez un nouveau patient dans le syst&egrave;me avec une pr&eacute;sentation plus claire, mieux hi&eacute;rarchis&eacute;e et coh&eacute;rente avec l exp&eacute;rience MEDISYS Pro.</p>
                </div>
                <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary header-return-btn">
                    <span class="header-return-btn-icon"><i class="fas fa-arrow-left"></i></span>
                    <span>Retour</span>
                </a>
            </div>
            <!-- Breadcrumb -->
            <nav style="font-size: 13px;" class="text-muted breadcrumb-bar">
                <a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
                <span class="mx-2">/</span>
                <a href="{{ route('patients.index') }}" class="text-decoration-none text-muted">Patients</a>
                <span class="mx-2">/</span>
                <span class="fw-bold breadcrumb-current">Nouveau</span>
            </nav>
        </div>

        <!-- Info Alert -->
        <div class="info-box mb-4">
            <span class="info-box-icon"><i class="fas fa-info-circle"></i></span>
            <div>
                <strong>Information importante</strong><br>
                Les champs marqu&eacute;s avec <span style="color: #ef4444;">*</span> sont obligatoires.
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('patients.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                
                <!-- Left Sidebar - Photo -->
                <div class="col-lg-3 mb-4">
                    <div class="form-card sticky-sidebar sidebar-card">
                        <div class="form-card-body text-center">
                            <h5 class="mb-4 sidebar-title"><i class="fas fa-camera me-2"></i> Photo</h5>
                            
                            <label class="photo-circle mx-auto mb-3 d-block" onclick="document.getElementById('photoInput').click()">
                                <i id="photoIcon" class="fas fa-user photo-placeholder-icon"></i>
                                <img id="photoPreview" src="" alt="Preview" style="display: none;">
                            </label>
                            
                            <input type="file" id="photoInput" name="photo" accept="image/*" class="d-none" onchange="previewPhoto(event)">
                            
                            <div class="photo-meta">JPG, PNG, GIF &bull; Taille maximale 2MB</div>
                            @error('photo')
                                <div class="text-danger small mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror

                            <hr class="sidebar-divider">

                            <h6 class="mb-3 sidebar-title">Actions</h6>
                            <button type="submit" class="btn btn-gradient-blue w-100 mb-2">
                                <i class="fas fa-save me-2"></i> Enregistrer
                            </button>
                            <button type="reset" class="btn btn-outline-secondary sidebar-reset-btn w-100 mb-3">
                                <i class="fas fa-redo me-2"></i> R&eacute;initialiser
                            </button>

                            <hr class="sidebar-divider">

                            <div class="help-box">
                                <strong><i class="fas fa-life-ring me-2"></i>Besoin d'aide ?</strong>
                                <p class="mb-0 mt-2">Consultez notre <a href="#" class="text-decoration-none">guide d'utilisation</a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-lg-9">

                    <!-- Section 1: Informations Personnelles -->
                    <div class="form-card">
                        <div class="section-header gradient-blue">
                            <i class="fas fa-user-circle"></i>
                            <div>
                                Informations Personnelles
                                <span class="section-note">Identite et informations de base du patient</span>
                            </div>
                        </div>
                        <div class="form-card-body">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label required">Nom</label>
                                    <input type="text" name="nom" value="{{ old('nom') }}" required
                                        class="form-control @error('nom') is-invalid @enderror" placeholder="Ex: Dupont">
                                    @error('nom')
                                        <div class="invalid-feedback d-block mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label required">Pr&eacute;nom</label>
                                    <input type="text" name="prenom" value="{{ old('prenom') }}" required
                                        class="form-control @error('prenom') is-invalid @enderror" placeholder="Ex: Jean">
                                    @error('prenom')
                                        <div class="invalid-feedback d-block mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">CIN</label>
                                    <input type="text" name="cin" value="{{ old('cin') }}"
                                        class="form-control @error('cin') is-invalid @enderror" placeholder="AA123456">
                                    @error('cin')
                                        <div class="invalid-feedback d-block mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label required">Date de Naissance</label>
                                    <input type="date" name="date_naissance" value="{{ old('date_naissance') }}" required
                                        class="form-control @error('date_naissance') is-invalid @enderror">
                                    @error('date_naissance')
                                        <div class="invalid-feedback d-block mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label required">Genre</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="genre" id="genre_m" value="M" {{ old('genre', 'M') == 'M' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="genre_m">
                                                <i class="fas fa-mars text-primary me-1"></i> Masculin
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="genre" id="genre_f" value="F" {{ old('genre') == 'F' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="genre_f">
                                                <i class="fas fa-venus text-danger me-1"></i> F&eacute;minin
                                            </label>
                                        </div>
                                    </div>
                                    @error('genre')
                                        <div class="text-danger small mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Contact & Adresse -->
                    <div class="form-card">
                        <div class="section-header gradient-green">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                Contact & Adresse
                                <span class="section-note">Coordonnees et localisation du patient</span>
                            </div>
                        </div>
                        <div class="form-card-body">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label required">T&eacute;l&eacute;phone</label>
                                    <input type="tel" name="telephone" value="{{ old('telephone') }}" required
                                        class="form-control @error('telephone') is-invalid @enderror" placeholder="+212 6XX XXX XXX">
                                    @error('telephone')
                                        <div class="invalid-feedback d-block mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                        class="form-control @error('email') is-invalid @enderror" placeholder="patient@example.com">
                                    @error('email')
                                        <div class="invalid-feedback d-block mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-4">
                                    <label class="form-label">Adresse</label>
                                    <input type="text" name="adresse" value="{{ old('adresse') }}"
                                        class="form-control @error('adresse') is-invalid @enderror" placeholder="123 Rue de la Paix">
                                    @error('adresse')
                                        <div class="invalid-feedback d-block mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Ville</label>
                                    <input type="text" name="ville" value="{{ old('ville') }}"
                                        class="form-control @error('ville') is-invalid @enderror" placeholder="Casablanca">
                                    @error('ville')
                                        <div class="invalid-feedback d-block mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Code Postal</label>
                                    <input type="text" name="code_postal" value="{{ old('code_postal') }}"
                                        class="form-control @error('code_postal') is-invalid @enderror" placeholder="20000">
                                    @error('code_postal')
                                        <div class="invalid-feedback d-block mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Informations M&eacute;dicales -->
                    <div class="form-card">
                        <div class="section-header gradient-red">
                            <i class="fas fa-heartbeat"></i>
                            <div>
                                Informations M&eacute;dicales
                                <span class="section-note">Contexte clinique et couverture du patient</span>
                            </div>
                        </div>
                        <div class="form-card-body">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Groupe Sanguin</label>
                                    <select name="groupe_sanguin" class="form-select @error('groupe_sanguin') is-invalid @enderror">
                                        <option value="">-- S&eacute;lectionner --</option>
                                        <option value="O+" {{ old('groupe_sanguin') == 'O+' ? 'selected' : '' }}>O+</option>
                                        <option value="O-" {{ old('groupe_sanguin') == 'O-' ? 'selected' : '' }}>O-</option>
                                        <option value="A+" {{ old('groupe_sanguin') == 'A+' ? 'selected' : '' }}>A+</option>
                                        <option value="A-" {{ old('groupe_sanguin') == 'A-' ? 'selected' : '' }}>A-</option>
                                        <option value="B+" {{ old('groupe_sanguin') == 'B+' ? 'selected' : '' }}>B+</option>
                                        <option value="B-" {{ old('groupe_sanguin') == 'B-' ? 'selected' : '' }}>B-</option>
                                        <option value="AB+" {{ old('groupe_sanguin') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                        <option value="AB-" {{ old('groupe_sanguin') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                    </select>
                                    @error('groupe_sanguin')
                                        <div class="invalid-feedback d-block mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Couverture sant&eacute;</label>
                                    @php
                                        $assuranceOptions = [
                                            'CNSS', 'CNOPS', 'AMO', 'AMO TADAMON', 'CIMR',
                                            'MGPAP', 'MGEN', 'OMFAM', 'CNMSS', 'Mutuelle des Forces Auxiliaires',
                                            'Mutuelle de l\'Education / Enseignement', 'Mutuelle de la Poste / Telecom',
                                            'Wafa Assurance', 'RMA', 'AXA Maroc', 'Allianz Maroc', 'Sanlam Maroc',
                                            'AtlantaSanad', 'Saham', 'Zurich',
                                        ];

                                        $selectedAssurance = old('assurance_medicale');
                                        $isKnownAssurance = in_array((string) $selectedAssurance, $assuranceOptions, true);
                                        $assuranceSelectValue = $isKnownAssurance ? $selectedAssurance : ($selectedAssurance ? 'Autre' : '');
                                        $assuranceAutreValue = old('assurance_autre', $isKnownAssurance ? '' : $selectedAssurance);
                                    @endphp

                                    <select id="assurance_medicale" name="assurance_medicale"
                                        class="form-select @error('assurance_medicale') is-invalid @enderror @error('assurance_autre') is-invalid @enderror"
                                        aria-label="Choisir une couverture sant&eacute;">
                                        <option value="">-- Choisir une couverture sant&eacute; --</option>

                                        <optgroup label="R&eacute;gimes de base">
                                            <option value="CNSS" @selected($assuranceSelectValue === 'CNSS')>CNSS</option>
                                            <option value="CNOPS" @selected($assuranceSelectValue === 'CNOPS')>CNOPS</option>
                                            <option value="AMO" @selected($assuranceSelectValue === 'AMO')>AMO</option>
                                            <option value="AMO TADAMON" @selected($assuranceSelectValue === 'AMO TADAMON')>AMO TADAMON (ex-RAMED)</option>
                                            <option value="CIMR" @selected($assuranceSelectValue === 'CIMR')>CIMR</option>
                                        </optgroup>

                                        <optgroup label="Mutuelles">
                                            <option value="MGPAP" @selected($assuranceSelectValue === 'MGPAP')>MGPAP</option>
                                            <option value="MGEN" @selected($assuranceSelectValue === 'MGEN')>MGEN</option>
                                            <option value="OMFAM" @selected($assuranceSelectValue === 'OMFAM')>OMFAM</option>
                                            <option value="CNMSS" @selected($assuranceSelectValue === 'CNMSS')>CNMSS</option>
                                            <option value="Mutuelle des Forces Auxiliaires" @selected($assuranceSelectValue === 'Mutuelle des Forces Auxiliaires')>Mutuelle des Forces Auxiliaires</option>
                                            <option value="Mutuelle de l'&Eacute;ducation / Enseignement" @selected($assuranceSelectValue === "Mutuelle de l'&Eacute;ducation / Enseignement")>Mutuelle de l'&Eacute;ducation / Enseignement</option>
                                            <option value="Mutuelle de la Poste / Telecom" @selected($assuranceSelectValue === 'Mutuelle de la Poste / Telecom')>Mutuelle de la Poste / Telecom</option>
                                        </optgroup>

                                        <optgroup label="Assurances priv&eacute;es">
                                            <option value="Wafa Assurance" @selected($assuranceSelectValue === 'Wafa Assurance')>Wafa Assurance</option>
                                            <option value="RMA" @selected($assuranceSelectValue === 'RMA')>RMA</option>
                                            <option value="AXA Maroc" @selected($assuranceSelectValue === 'AXA Maroc')>AXA Maroc</option>
                                            <option value="Allianz Maroc" @selected($assuranceSelectValue === 'Allianz Maroc')>Allianz Maroc</option>
                                            <option value="Sanlam Maroc" @selected($assuranceSelectValue === 'Sanlam Maroc')>Sanlam Maroc</option>
                                            <option value="AtlantaSanad" @selected($assuranceSelectValue === 'AtlantaSanad')>AtlantaSanad</option>
                                            <option value="Saham" @selected($assuranceSelectValue === 'Saham')>Saham</option>
                                            <option value="Zurich" @selected($assuranceSelectValue === 'Zurich')>Zurich</option>
                                        </optgroup>

                                        <optgroup label="Autre organisme">
                                            <option value="Autre" @selected($assuranceSelectValue === 'Autre')>Autre (&agrave; pr&eacute;ciser)</option>
                                        </optgroup>
                                    </select>
                                    @error('assurance_medicale')
                                        <div class="invalid-feedback d-block mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                    @enderror

                                    <div id="assuranceAutreWrap" class="mt-3 {{ $assuranceSelectValue === 'Autre' ? '' : 'd-none' }}">
                                        <label for="assurance_autre" class="form-label">Pr&eacute;ciser</label>
                                        <input type="text" id="assurance_autre" name="assurance_autre" value="{{ $assuranceAutreValue }}"
                                            class="form-control @error('assurance_autre') is-invalid @enderror"
                                            placeholder="Nom de l'organisme">
                                        @error('assurance_autre')
                                            <div class="invalid-feedback d-block mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 mb-4">
                                    <label class="form-label">Antecedents medicaux</label>
                                    <textarea name="antecedents_medicaux" rows="4"
                                        class="form-control @error('antecedents_medicaux') is-invalid @enderror" placeholder="Decrivez les antecedents medicaux...">{{ old('antecedents_medicaux') }}</textarea>
                                    @error('antecedents_medicaux')
                                        <div class="invalid-feedback d-block mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label">Allergies</label>
                                    <textarea name="allergies" rows="3"
                                        class="form-control @error('allergies') is-invalid @enderror" placeholder="Listez les allergies connues...">{{ old('allergies') }}</textarea>
                                    @error('allergies')
                                        <div class="invalid-feedback d-block mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </form>

    </div>
</div>

<script>
function previewPhoto(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('photoPreview');
            const icon = document.getElementById('photoIcon');
            preview.src = e.target.result;
            preview.style.display = 'block';
            icon.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}

function toggleAssuranceAutre() {
    const assuranceSelect = document.getElementById('assurance_medicale');
    const assuranceAutreWrap = document.getElementById('assuranceAutreWrap');
    const assuranceAutreInput = document.getElementById('assurance_autre');

    if (!assuranceSelect || !assuranceAutreWrap || !assuranceAutreInput) {
        return;
    }

    const isAutre = assuranceSelect.value === 'Autre';
    assuranceAutreWrap.classList.toggle('d-none', !isAutre);
    assuranceAutreInput.required = isAutre;

    if (!isAutre) {
        assuranceAutreInput.value = '';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const assuranceSelect = document.getElementById('assurance_medicale');
    if (assuranceSelect) {
        assuranceSelect.addEventListener('change', toggleAssuranceAutre);
        toggleAssuranceAutre();
    }
});
</script>
@endsection


