@extends('layouts.app')

@section('title', 'Rapports')
@section('topbar_subtitle', 'Centre de rapports, analyses et exports m&eacute;dicaux')

@section('content')
<div class="container-fluid reports-page py-2 py-lg-3">
    <div class="reports-shell">
        <div class="reports-head">
            <div>
                <h2 class="reports-title">
                    <i class="fas fa-chart-line me-2"></i>Centre de Rapports & Analyses
                </h2>
                <p class="reports-subtitle mb-0">G&eacute;n&eacute;rez, exportez et suivez vos rapports en un seul espace.</p>
            </div>
            <div class="reports-head-stats">
                <div class="head-stat">
                    <span>Formats</span>
                    <strong>PDF / Excel / CSV</strong>
                </div>
                <div class="head-stat">
                    <span>Derni&egrave;re mise &agrave; jour</span>
                    <strong>Aujourd'hui</strong>
                </div>
            </div>
        </div>

        <div class="reports-toolbar">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Type de rapport</label>
                    <select class="form-select" id="reportType">
                        <option value="">Type de rapport</option>
                        <option value="monthly">Consultations</option>
                        <option value="financial">Facturation</option>
                        <option value="patient">Patients</option>
                        <option value="medicament">M&eacute;dicaments</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date d&eacute;but</label>
                    <input type="date" class="form-control" id="reportDateStart" placeholder="Date d&eacute;but">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date de fin</label>
                    <input type="date" class="form-control" id="reportDateEnd" placeholder="Date de fin">
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary w-100 reports-main-btn" id="generateMainReport">
                        <i class="fas fa-search me-2"></i>G&eacute;n&eacute;rer le rapport
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="report-tile">
                    <div class="report-tile-icon bg-primary-soft text-primary">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h6>Rapport mensuel</h6>
                    <p>Consultations du mois</p>
                    <button class="btn btn-outline-primary btn-sm generate-report" data-type="monthly">
                        <i class="fas fa-spinner fa-spin d-none"></i>
                        G&eacute;n&eacute;rer
                    </button>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="report-tile">
                    <div class="report-tile-icon bg-success-soft text-success">
                        <i class="fas fa-euro-sign"></i>
                    </div>
                    <h6>Rapport financier</h6>
                    <p>Chiffre d'affaires</p>
                    <button class="btn btn-outline-success btn-sm generate-report" data-type="financial">
                        <i class="fas fa-spinner fa-spin d-none"></i>
                        G&eacute;n&eacute;rer
                    </button>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="report-tile">
                    <div class="report-tile-icon bg-info-soft text-info">
                        <i class="fas fa-users"></i>
                    </div>
                    <h6>Rapport patients</h6>
                    <p>Statistiques patients</p>
                    <button class="btn btn-outline-info btn-sm generate-report" data-type="patient">
                        <i class="fas fa-spinner fa-spin d-none"></i>
                        G&eacute;n&eacute;rer
                    </button>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="report-tile">
                    <div class="report-tile-icon bg-warning-soft text-warning">
                        <i class="fas fa-pills"></i>
                    </div>
                    <h6>Rapport m&eacute;dicaments</h6>
                    <p>Stock et prescriptions</p>
                    <button class="btn btn-outline-warning btn-sm generate-report" data-type="medicament">
                        <i class="fas fa-spinner fa-spin d-none"></i>
                        G&eacute;n&eacute;rer
                    </button>
                </div>
            </div>
        </div>

        <div class="reports-card mb-4">
            <div class="reports-card-head">
                <h6 class="mb-0">Historique des rapports g&eacute;n&eacute;r&eacute;s</h6>
            </div>
            <div class="reports-card-body">
                <div class="table-responsive">
                    <table class="table reports-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>P&eacute;riode</th>
                                <th>G&eacute;n&eacute;r&eacute; par</th>
                                <th>Format</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td data-label="Date">15/01/2024</td>
                                <td data-label="Type">Rapport mensuel</td>
                                <td data-label="P&eacute;riode">D&eacute;cembre 2023</td>
                                <td data-label="G&eacute;n&eacute;r&eacute; par">Dr. Martin</td>
                                <td data-label="Format"><span class="badge bg-primary">PDF</span></td>
                                <td data-label="Actions">
                                    <div class="reports-row-actions">
                                        <button class="btn action-btn action-view" type="button" aria-label="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn action-btn action-download" type="button" aria-label="T&eacute;l&eacute;charger">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="btn action-btn action-delete" type="button" aria-label="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td data-label="Date">10/01/2024</td>
                                <td data-label="Type">Rapport financier</td>
                                <td data-label="P&eacute;riode">4eme trimestre 2023</td>
                                <td data-label="G&eacute;n&eacute;r&eacute; par">Admin</td>
                                <td data-label="Format"><span class="badge bg-success">Excel</span></td>
                                <td data-label="Actions">
                                    <div class="reports-row-actions">
                                        <button class="btn action-btn action-view" type="button" aria-label="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn action-btn action-download" type="button" aria-label="T&eacute;l&eacute;charger">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="btn action-btn action-delete" type="button" aria-label="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td data-label="Date">05/01/2024</td>
                                <td data-label="Type">Rapport patients</td>
                                <td data-label="P&eacute;riode">Janvier 2024</td>
                                <td data-label="G&eacute;n&eacute;r&eacute; par">Dr. Dubois</td>
                                <td data-label="Format"><span class="badge bg-primary">PDF</span></td>
                                <td data-label="Actions">
                                    <div class="reports-row-actions">
                                        <button class="btn action-btn action-view" type="button" aria-label="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn action-btn action-download" type="button" aria-label="T&eacute;l&eacute;charger">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="btn action-btn action-delete" type="button" aria-label="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="reports-card">
            <div class="reports-card-head">
                <h6 class="mb-0">Param&egrave;tres d'export</h6>
            </div>
            <div class="reports-card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="setting-panel">
                            <h6>Format d'export</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" id="pdf" value="pdf" checked>
                                <label class="form-check-label" for="pdf">
                                    <i class="fas fa-file-pdf me-2 text-danger"></i>PDF
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" id="excel" value="excel">
                                <label class="form-check-label" for="excel">
                                    <i class="fas fa-file-excel me-2 text-success"></i>Excel
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" id="csv" value="csv">
                                <label class="form-check-label" for="csv">
                                    <i class="fas fa-file-csv me-2 text-info"></i>CSV
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="setting-panel">
                            <h6>Options d'inclusion</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="graphs" checked>
                                <label class="form-check-label" for="graphs">Inclure les graphiques</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="details" checked>
                                <label class="form-check-label" for="details">D&eacute;tails complets</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="summary">
                                <label class="form-check-label" for="summary">R&eacute;sum&eacute; ex&eacute;cutif</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="setting-panel">
                            <h6>Planification</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="auto">
                                <label class="form-check-label" for="auto">G&eacute;n&eacute;ration automatique</label>
                            </div>
                            <label class="form-label mb-1" for="frequence">Fr&eacute;quence</label>
                            <select id="frequence" class="form-select" disabled>
                                <option>Hebdomadaire</option>
                                <option>Mensuel</option>
                                <option>Trimestriel</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.reports-page {
    --rp-bg: linear-gradient(180deg, #f6f9fd 0%, #f2f6fb 100%);
    --rp-card: #ffffff;
    --rp-border: #e1eaf5;
    --rp-title: #0f2746;
    --rp-muted: #5f728d;
    --rp-primary: #1f6fe5;
    background: var(--rp-bg);
    border: 1px solid #e8eff7;
    border-radius: 18px;
    width: 100%;
    max-width: 100%;
}

.reports-shell {
    width: 100%;
    max-width: none;
    margin: 0;
    padding: clamp(.75rem, 1.4vw, 1.35rem);
}

.reports-head {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.reports-title {
    font-size: 1.4rem;
    color: var(--rp-title);
    font-weight: 700;
    margin-bottom: .35rem;
}

.reports-subtitle {
    color: var(--rp-muted);
    font-size: .92rem;
}

.reports-head-stats {
    display: flex;
    gap: .65rem;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.head-stat {
    background: #f7faff;
    border: 1px solid #dbe7f8;
    border-radius: 12px;
    padding: .6rem .8rem;
    min-width: 160px;
}

.head-stat span {
    display: block;
    font-size: .75rem;
    color: #6782a7;
    margin-bottom: .15rem;
}

.head-stat strong {
    color: #193e70;
    font-size: .88rem;
    font-weight: 700;
}

.reports-toolbar,
.reports-card {
    background: var(--rp-card);
    border: 1px solid var(--rp-border);
    border-radius: 16px;
    box-shadow: 0 18px 24px -28px rgba(26, 54, 93, 0.8);
}

.reports-toolbar {
    padding: 1rem;
    margin-bottom: 1rem;
}

.reports-page .form-label {
    font-size: .84rem;
    color: #3b5a82;
    font-weight: 600;
}

.reports-page .form-control,
.reports-page .form-select {
    border-radius: 11px;
    border-color: #cfddf0;
    background: #fbfdff;
}

.reports-page .form-control:focus,
.reports-page .form-select:focus {
    border-color: #8eb8f8;
    box-shadow: 0 0 0 0.2rem rgba(31, 111, 229, 0.15);
}

.reports-main-btn {
    border-radius: 11px;
    font-weight: 600;
    height: 42px;
    background: linear-gradient(135deg, #1f6fe5 0%, #4b8cf2 100%);
    border: 0;
}

.report-tile {
    height: 100%;
    text-align: center;
    background: #fff;
    border: 1px solid var(--rp-border);
    border-radius: 16px;
    padding: 1rem .9rem;
    box-shadow: 0 15px 26px -28px rgba(26, 53, 90, 0.7);
}

.report-tile-icon {
    width: 52px;
    height: 52px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    margin-bottom: .7rem;
}

.bg-primary-soft { background: #e8f1ff; }
.bg-success-soft { background: #e9f8f0; }
.bg-info-soft { background: #e8f7fb; }
.bg-warning-soft { background: #fff5e4; }

.report-tile h6 {
    color: #12345f;
    margin-bottom: .2rem;
    font-weight: 700;
}

.report-tile p {
    color: #627999;
    font-size: .87rem;
    margin-bottom: .65rem;
}

.reports-card-head {
    border-bottom: 1px solid var(--rp-border);
    padding: .85rem 1rem;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
}

.reports-card-head h6 {
    color: #153863;
    font-weight: 700;
}

.reports-card-body {
    padding: 1rem;
}

.reports-table thead th {
    color: #254a78;
    font-weight: 700;
    border-bottom-color: #d8e4f5;
}

.reports-table tbody td {
    color: #2d486d;
    border-color: #edf2f9;
}

.reports-row-actions {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    flex-wrap: wrap;
}

.action-btn {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid transparent;
}

.action-view {
    color: #1f6fe5;
    background: #edf4ff;
    border-color: #c9ddff;
}

.action-download {
    color: #158a4e;
    background: #edfbf3;
    border-color: #c8efd9;
}

.action-delete {
    color: #d63a4a;
    background: #fff0f2;
    border-color: #ffd0d8;
}

.setting-panel {
    border: 1px solid #dce8f7;
    border-radius: 14px;
    padding: .85rem;
    background: #f9fcff;
    height: 100%;
}

.setting-panel h6 {
    color: #214772;
    font-weight: 700;
    margin-bottom: .65rem;
}

.setting-panel .form-check {
    margin-bottom: .35rem;
}

body.dark-mode .reports-page {
    --rp-bg: linear-gradient(180deg, #0f172a 0%, #111827 100%);
    --rp-card: #0b1730;
    --rp-border: #2f4b67;
    --rp-title: #f3f4f6;
    --rp-muted: #9ca3af;
    --rp-primary: #60a5fa;
    border-color: #2f4b67;
    box-shadow: 0 14px 28px -20px rgba(0, 0, 0, 0.65);
}

body.dark-mode .reports-head {
    background: linear-gradient(180deg, #102236 0%, #0f1d30 100%);
    border: 1px solid #2f4b67;
    border-radius: 14px;
    padding: .9rem 1rem;
    margin-bottom: 1rem;
}

body.dark-mode .reports-title,
body.dark-mode .reports-card-head h6,
body.dark-mode .report-tile h6,
body.dark-mode .setting-panel h6 {
    color: #e5edff;
}

body.dark-mode .reports-subtitle,
body.dark-mode .report-tile p,
body.dark-mode .head-stat span,
body.dark-mode .reports-page .form-label,
body.dark-mode .reports-table tbody td,
body.dark-mode .reports-table thead th,
body.dark-mode .setting-panel .form-check-label {
    color: #9fb3cf !important;
}

body.dark-mode .head-stat,
body.dark-mode .report-tile,
body.dark-mode .setting-panel,
body.dark-mode .reports-toolbar,
body.dark-mode .reports-card,
body.dark-mode .reports-card-head {
    background: #0b1730 !important;
    border-color: #2f4b67 !important;
}

body.dark-mode .reports-card-head {
    background: linear-gradient(180deg, #111f33 0%, #0f1d30 100%) !important;
}

body.dark-mode .head-stat strong {
    color: #dbeafe;
}

body.dark-mode .reports-table tbody tr:hover {
    background: rgba(30, 58, 138, 0.12);
}

body.dark-mode .reports-page .form-control,
body.dark-mode .reports-page .form-select {
    background: #111827;
    border-color: #374151;
    color: #f3f4f6;
}

body.dark-mode .reports-page .form-control::placeholder {
    color: #94a3b8;
}

body.dark-mode .reports-page .form-control:focus,
body.dark-mode .reports-page .form-select:focus {
    border-color: #60a5fa;
    box-shadow: 0 0 0 0.2rem rgba(96, 165, 250, 0.2);
}

body.dark-mode .reports-table tbody td,
body.dark-mode .reports-table thead th {
    border-color: #374151;
}

body.dark-mode .reports-main-btn {
    background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
}

@media (max-width: 991.98px) {
    .reports-shell {
        padding: .75rem;
        max-width: 100%;
    }

    .reports-head {
        flex-direction: column;
        gap: .75rem;
    }

    .reports-head-stats {
        width: 100%;
        justify-content: flex-start;
    }

    .head-stat {
        flex: 1;
        min-width: 0;
    }
}

@media (max-width: 767.98px) {
    .reports-page {
        border-radius: 12px;
    }

    .reports-title {
        font-size: 1.15rem;
    }

    .reports-toolbar,
    .reports-card,
    .report-tile,
    .setting-panel {
        border-radius: 12px;
    }

    .reports-card-body,
    .reports-toolbar {
        padding: .75rem;
    }

    .reports-main-btn {
        height: 40px;
    }

    .report-tile {
        padding: .85rem .75rem;
    }

    .report-tile-icon {
        width: 44px;
        height: 44px;
        font-size: 1rem;
    }

    .action-btn {
        width: 30px;
        height: 30px;
        border-radius: 8px;
    }

    .reports-card-body .table-responsive {
        overflow: visible;
    }

    .reports-table,
    .reports-table tbody,
    .reports-table tr,
    .reports-table td {
        display: block;
        width: 100%;
    }

    .reports-table {
        font-size: .88rem;
    }

    .reports-table thead {
        display: none;
    }

    .reports-table tbody {
        display: grid;
        gap: .85rem;
    }

    .reports-table tbody tr {
        background: #ffffff;
        border: 1px solid var(--rp-border);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 18px 24px -30px rgba(26, 54, 93, 0.52);
    }

    .reports-table tbody td {
        display: grid;
        grid-template-columns: minmax(92px, 108px) minmax(0, 1fr);
        gap: .75rem;
        align-items: start;
        padding: .75rem .85rem;
        border-bottom: 1px solid #edf2f9;
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .reports-table tbody td:last-child {
        border-bottom: 0;
    }

    .reports-table tbody td::before {
        content: attr(data-label);
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #6782a7;
    }

    .reports-row-actions {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 32px));
        gap: .45rem;
    }
}

@media (max-width: 575.98px) {
    .reports-shell {
        padding: .6rem;
    }

    .reports-head {
        margin-bottom: .75rem;
    }

    .reports-subtitle {
        font-size: .84rem;
    }

    .reports-head-stats {
        display: grid;
        grid-template-columns: 1fr;
        gap: .5rem;
    }

    .reports-card-head,
    .reports-card-body {
        padding: .65rem;
    }

    .reports-table tbody td {
        grid-template-columns: 1fr;
        gap: .45rem;
        white-space: normal;
    }

    .reports-row-actions {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .action-btn {
        width: 100%;
    }
}

@media (max-width: 767.98px) {
    body.dark-mode .reports-table tbody tr {
        background: #0b1730;
        border-color: #374151;
        box-shadow: none;
    }

    body.dark-mode .reports-table tbody td::before {
        color: #8ea9c6;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function normalizeReportType(type) {
        var map = {
            consultations: 'monthly',
            consultation: 'monthly',
            monthly: 'monthly',
            facturation: 'financial',
            financier: 'financial',
            financial: 'financial',
            patients: 'patient',
            patient: 'patient',
            medicaments: 'medicament',
            medicament: 'medicament'
        };

        if (!type) return '';
        var normalized = String(type).trim().toLowerCase();
        return map[normalized] || normalized;
    }

    function generateReport(type, triggerButton) {
        var normalizedType = normalizeReportType(type);
        if (!normalizedType) {
            alert('Veuillez s\u00E9lectionner un type de rapport.');
            return;
        }

        var formatInput = document.querySelector('input[name="format"]:checked');
        var format = formatInput ? formatInput.value : 'pdf';
        var dateDebut = document.getElementById('reportDateStart')?.value || '';
        var dateFin = document.getElementById('reportDateEnd')?.value || '';

        var originalText = triggerButton.innerHTML;
        triggerButton.disabled = true;
        triggerButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> G\u00E9n\u00E9ration...';

        var params = new URLSearchParams();
        params.set('format', format);
        if (dateDebut) params.set('date_debut', dateDebut);
        if (dateFin) params.set('date_fin', dateFin);
        var csrfToken = document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') || '';
        if (csrfToken) {
            params.set('_token', csrfToken);
        }

        fetch('/rapports/' + normalizedType, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/pdf, application/json',
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                'X-CSRF-TOKEN': csrfToken
            },
            body: params.toString()
        })
        .then(function (response) {
            if (!response.ok) {
                return response.text().then(function (text) {
                    try {
                        var data = JSON.parse(text);
                        throw new Error(data.error || 'Erreur lors de la g\u00E9n\u00E9ration du rapport');
                    } catch (e) {
                        throw new Error('Erreur lors de la g\u00E9n\u00E9ration du rapport');
                    }
                });
            }

            var contentType = response.headers.get('content-type') || '';
            if (contentType.indexOf('application/pdf') !== -1 || contentType.indexOf('application/octet-stream') !== -1) {
                return response.blob().then(function (blob) {
                    return { mode: 'file', blob: blob };
                });
            }

            if (contentType.indexOf('application/json') !== -1) {
                return response.json().then(function (data) {
                    if (data.error) {
                        throw new Error(data.error);
                    }

                    return { mode: 'json', data: data };
                });
            }

            return response.blob().then(function (blob) {
                return { mode: 'file', blob: blob };
            });
        })
        .then(function (result) {
            if (result.mode === 'file') {
                var url = window.URL.createObjectURL(result.blob);
                var a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = 'rapport_' + normalizedType + '_' + new Date().toISOString().split('T')[0] + '.' + (format === 'pdf' ? 'pdf' : format);
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);

                setTimeout(function () {
                    location.reload();
                }, 600);
                return;
            }

            if (result.mode === 'json' && result.data && result.data.message) {
                alert(result.data.message);
            }
        })
        .catch(function (error) {
            alert('Une erreur est survenue lors de la g\u00E9n\u00E9ration du rapport: ' + error.message);
        })
        .finally(function () {
            triggerButton.disabled = false;
            triggerButton.innerHTML = originalText;
        });
    }

    document.querySelectorAll('.generate-report').forEach(function (button) {
        button.addEventListener('click', function () {
            generateReport(this.getAttribute('data-type'), this);
        });
    });

    var mainBtn = document.getElementById('generateMainReport');
    if (mainBtn) {
        mainBtn.addEventListener('click', function () {
            var selectedType = document.getElementById('reportType')?.value || '';
            generateReport(selectedType, mainBtn);
        });
    }
});
</script>
@endpush
