@extends('layouts.app')

@section('title', 'Archives Patients')
@section('topbar_subtitle', 'Consultation des dossiers archives avec filtres compacts et lecture plus nette.')

@section('content')
<div class="container-fluid archives-page">
    <section class="archives-shell" aria-labelledby="archives-title">
        <header class="archives-header">
            <div class="archives-header-main">
                <span class="archives-eyebrow">Archives patients</span>
                <h1 id="archives-title" class="archives-title">Archives du cabinet</h1>
                <p class="archives-subtitle">Consultez les dossiers archives, filtrez rapidement les resultats et accedez au dossier patient quand il est disponible.</p>
            </div>
            <div class="archives-header-actions">
                <a href="{{ route('archives.index') }}" class="btn btn-outline-primary archives-action-btn focus-ring">
                    <i class="fas fa-rotate-right me-2" aria-hidden="true"></i>Reinitialiser
                </a>
            </div>
        </header>

        <p class="archives-note" role="status">
            Historique des dossiers archives du cabinet, avec recherche rapide, filtres cibles et acces au dossier medical lorsqu'il existe encore dans le flux actif.
        </p>

        <form method="GET" action="{{ route('archives.index') }}" class="archives-filterbar" aria-label="Filtres des archives">
            <div class="archives-filter archives-filter-search">
                <label for="archiveSearch" class="visually-hidden">Recherche</label>
                <input
                    id="archiveSearch"
                    name="q"
                    type="text"
                    class="form-control focus-ring"
                    value="{{ request('q') }}"
                    placeholder="Rechercher un patient, un dossier ou un motif..."
                    aria-label="Recherche dans les archives"
                >
            </div>

            <div class="archives-filter archives-filter-status">
                <label for="archiveStatus" class="visually-hidden">Statut</label>
                <select id="archiveStatus" name="status" class="form-select focus-ring" aria-label="Filtrer par statut">
                    <option value="" @selected(request('status') === null || request('status') === '')>Tous les statuts</option>
                    <option value="archive" @selected(request('status') === 'archive')>Archive</option>
                    <option value="restaure" @selected(request('status') === 'restaure')>Restaure</option>
                    <option value="historique" @selected(request('status') === 'historique')>Historique</option>
                </select>
            </div>

            <div class="archives-filter archives-filter-date">
                <label for="archiveDate" class="visually-hidden">Date</label>
                <input
                    id="archiveDate"
                    name="date"
                    type="date"
                    class="form-control focus-ring"
                    value="{{ request('date') }}"
                    aria-label="Filtrer par date d'archivage"
                >
            </div>

            <div class="archives-filter archives-filter-actions">
                <button type="submit" class="btn btn-primary archives-search-btn focus-ring">
                    <i class="fas fa-search me-2" aria-hidden="true"></i>Rechercher
                </button>
            </div>
        </form>

        <div class="archives-table-wrap" role="region" aria-label="Table des archives" tabindex="0">
            <table class="table archives-table align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col" class="col-patient">Patient</th>
                        <th scope="col" class="col-date">Date d'archivage</th>
                        <th scope="col" class="col-reason">Motif</th>
                        <th scope="col" class="col-status">Statut</th>
                        <th scope="col" class="col-actions text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($archives as $archive)
                        <tr>
                            <td class="fw-semibold archives-patient-cell">{{ $archive->display_patient_name }}</td>
                            <td class="archives-date-cell">{{ $archive->display_archived_at }}</td>
                            <td class="text-muted archives-reason-cell">{{ $archive->display_reason }}</td>
                            <td>
                                <span class="{{ $archive->display_status_class }}">{{ $archive->display_status_label }}</span>
                            </td>
                            <td class="text-end">
                                <div class="doc-actions archives-actions">
                                    @if ($archive->display_view_url)
                                        <a href="{{ $archive->display_view_url }}" class="focus-ring" title="Voir le dossier" aria-label="Voir le dossier">
                                            <i class="fas fa-eye" aria-hidden="true"></i>
                                        </a>
                                    @else
                                        <button type="button" class="focus-ring" title="Aucun dossier disponible" aria-label="Aucun dossier disponible" disabled>
                                            <i class="fas fa-eye-slash" aria-hidden="true"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="archives-empty-cell">
                                <div class="archives-empty-state">
                                    <i class="fas fa-box-open archives-empty-icon" aria-hidden="true"></i>
                                    <p class="archives-empty-title">Aucune archive trouvee</p>
                                    <p class="archives-empty-text">Ajustez vos criteres ou reinitialisez les filtres pour afficher des resultats.</p>
                                    <a href="{{ route('archives.index') }}" class="btn btn-outline-primary archives-reset-btn focus-ring">
                                        <i class="fas fa-filter-circle-xmark me-2" aria-hidden="true"></i>Reinitialiser les filtres
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($archives instanceof \Illuminate\Contracts\Pagination\Paginator && method_exists($archives, 'links'))
            <div class="archives-pagination">
                {{ $archives->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
