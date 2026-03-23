<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardModernController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\MedecinController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\ConsultationAiAssistantController;
use App\Http\Controllers\OrdonnanceController;
use App\Http\Controllers\MedicamentController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\DossierMedicalController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentCategoryController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\ClientTelemetryController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Security\TwoFactorController;
use App\Http\Controllers\Auth\LoginController;

require __DIR__.'/auth.php';

Route::post('/telemetry/client-errors', [ClientTelemetryController::class, 'store'])
    ->middleware('throttle:60,1')
    ->name('telemetry.client-errors');

// Page d'accueil / Dashboard (protÃ©gÃ©es par auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardModernController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardModernController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardModernController::class, 'getStats'])->name('dashboard.stats');
    Route::get('/dashboard/revenue', [DashboardModernController::class, 'getRevenueData'])->name('dashboard.revenue');
    Route::get('/dashboard/urgent-consultations', [DashboardModernController::class, 'getUrgentConsultations'])->name('dashboard.urgent-consultations');
});

// Patients
Route::get('/patients/export', [PatientController::class, 'export'])->middleware(['auth', 'module.access:patients'])->name('patients.export');
Route::resource('patients', PatientController::class)->middleware(['auth', 'module.access:patients']);

// MÃ©decins
Route::get('/medecins/export', [MedecinController::class, 'export'])->middleware(['auth', 'module.access:medecins'])->name('medecins.export');
Route::resource('medecins', MedecinController::class)->middleware(['auth', 'module.access:medecins']);

// Rendez-vous
Route::resource('rendezvous', RendezVousController::class)->middleware(['auth', 'module.access:planning']);
Route::get('/agenda', [RendezVousController::class, 'agenda'])->middleware(['auth', 'module.access:planning'])->name('agenda.index');
Route::get('/salle-attente', [RendezVousController::class, 'waitingRoomPage'])->middleware(['auth', 'module.access:planning'])->name('agenda.waiting_room');
// API endpoints pour la nouvelle interface de gestion de la salle d'attente
Route::get('/agenda/waiting-room-data', [RendezVousController::class, 'waitingRoomData'])
    ->middleware(['auth', 'module.access:planning'])
    ->name('agenda.waiting_room.data');

Route::post('/rendezvous/{id}/status', [RendezVousController::class, 'updateStatus'])
    ->middleware(['auth', 'module.access:planning'])
    ->name('rendezvous.update_status');

// Consultations
Route::get('/consultations/export', [ConsultationController::class, 'export'])->middleware(['auth', 'module.access:consultations'])->name('consultations.export');
Route::resource('consultations', ConsultationController::class)->middleware(['auth', 'module.access:consultations']);
Route::post('/consultations/{consultation}/assistant-ia/generate', [ConsultationAiAssistantController::class, 'generate'])
    ->middleware(['auth', 'module.access:consultations'])
    ->name('consultations.ai.generate');
Route::post('/consultations/{consultation}/assistant-ia/export-medical-report', [ConsultationAiAssistantController::class, 'exportMedicalReportPdf'])
    ->middleware(['auth', 'module.access:consultations'])
    ->name('consultations.ai.export-medical-report');

// Route pour les archives des dossiers mÃ©dicaux
Route::get('/dossiers/archives', [DossierMedicalController::class, 'archives'])->middleware(['auth', 'module.access:patients'])->name('dossiers.archives');

// Route pour archiver un dossier mÃ©dical
Route::match(['post', 'patch'], '/dossiers/{dossier}/archive', [DossierMedicalController::class, 'archive'])->middleware(['auth', 'module.access:patients'])->name('dossiers.archive');

// Dossiers mÃ©dicaux
Route::resource('dossiers', DossierMedicalController::class)->middleware(['auth', 'module.access:patients']);

// Ordonnances
Route::match(['post', 'put'], 'ordonnances/preview-pdf', [OrdonnanceController::class, 'previewPdf'])->middleware(['auth', 'module.access:pharmacie'])->name('ordonnances.preview-pdf');
Route::get('ordonnances/{ordonnance}/pdf', [OrdonnanceController::class, 'downloadPdf'])->middleware(['auth', 'module.access:pharmacie'])->name('ordonnances.pdf');
Route::post('ordonnances/store', [OrdonnanceController::class, 'store'])->middleware(['auth', 'module.access:pharmacie'])->name('ordonnances.store.quick');
Route::resource('ordonnances', OrdonnanceController::class)->middleware(['auth', 'module.access:pharmacie']);

// MÃ©dicaments
Route::get('medicaments/reports', [MedicamentController::class, 'reports'])->middleware(['auth', 'module.access:pharmacie'])->name('medicaments.reports');
Route::resource('medicaments', MedicamentController::class)->middleware(['auth', 'module.access:pharmacie']);

// Factures
Route::resource('factures', FactureController::class)->middleware(['auth', 'module.access:facturation']);
Route::get('/factures/{facture}/pdf', [FactureController::class, 'generatePdf'])->middleware(['auth', 'module.access:facturation'])->name('factures.pdf');
Route::post('/factures/{facture}/envoyer', [FactureController::class, 'envoyer'])->middleware(['auth', 'module.access:facturation'])->name('factures.envoyer');
Route::patch('/factures/{facture}/update-statut', [FactureController::class, 'updateStatut'])->middleware(['auth', 'module.access:facturation'])->name('factures.update-statut');
Route::get('/paiements', [PaiementController::class, 'index'])->middleware(['auth', 'module.access:facturation'])->name('paiements.index');
Route::get('/paiements/export', [PaiementController::class, 'export'])->middleware(['auth', 'module.access:facturation'])->name('paiements.export');
Route::get('/paiements/export/pdf', [PaiementController::class, 'exportPdf'])->middleware(['auth', 'module.access:facturation'])->name('paiements.export-pdf');
Route::get('/paiements/{source}/{id}', [PaiementController::class, 'show'])->middleware(['auth', 'module.access:facturation'])->whereIn('source', ['factures', 'depenses', 'examens'])->whereNumber('id')->name('paiements.show');

// Statistiques
Route::get('/statistiques', [StatistiqueController::class, 'index'])->middleware(['auth', 'module.access:statistiques'])->name('statistiques');
Route::get('/statistiques/rapport', [StatistiqueController::class, 'rapport'])->middleware(['auth', 'module.access:statistiques'])->name('statistiques.rapport');
Route::get('/statistiques/export', [StatistiqueController::class, 'export'])->middleware(['auth', 'module.access:statistiques'])->name('statistiques.export');

// ===== NOUVEAUX MODULES =====

// Bilans Complementaires (vue resultats legacy)
Route::get('/examens/results', [\App\Http\Controllers\ExamenController::class, 'results'])
    ->middleware(['auth', 'module.access:examens'])
    ->name('examens.results');

// Rappels SMS
Route::group(['prefix' => 'sms', 'middleware' => ['auth', 'module.access:sms']], function () {
    Route::get('/', [\App\Http\Controllers\SMSReminderController::class, 'index'])->name('sms.index');
    Route::get('/create', [\App\Http\Controllers\SMSReminderController::class, 'create'])->name('sms.create');
    Route::get('/logs', [\App\Http\Controllers\SMSReminderController::class, 'logs'])->name('sms.logs');
    Route::get('/{reminder}', [\App\Http\Controllers\SMSReminderController::class, 'show'])->whereNumber('reminder')->name('sms.show');
    Route::get('/{reminder}/edit', [\App\Http\Controllers\SMSReminderController::class, 'edit'])->whereNumber('reminder')->name('sms.edit');
    Route::put('/{reminder}', [\App\Http\Controllers\SMSReminderController::class, 'update'])->whereNumber('reminder')->name('sms.update');
    Route::post('/{reminder}/resend', [\App\Http\Controllers\SMSReminderController::class, 'resend'])->whereNumber('reminder')->name('sms.resend');
});

// Documents
Route::group(['prefix' => 'documents', 'middleware' => ['auth', 'module.access:documents']], function () {
    Route::get('/', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/categories', [DocumentCategoryController::class, 'index'])->middleware('role:admin')->name('documents.categories');
    Route::post('/categories', [DocumentCategoryController::class, 'store'])->middleware('role:admin')->name('documents.categories.store');
    Route::put('/categories/{category}', [DocumentCategoryController::class, 'update'])->middleware('role:admin')->whereNumber('category')->name('documents.categories.update');
    Route::get('/upload', [DocumentController::class, 'create'])->name('documents.upload');
    Route::post('/upload', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/{document}', [DocumentController::class, 'show'])->whereNumber('document')->name('documents.show');
    Route::delete('/{document}', [DocumentController::class, 'destroy'])->whereNumber('document')->name('documents.destroy');
});

// ParamÃ¨tres
use App\Http\Controllers\ParametresController;
use App\Http\Controllers\Admin\Settings\GovernanceCenterController;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/parametres', [ParametresController::class, 'index'])->name('parametres.index');
    Route::put('/parametres', [ParametresController::class, 'update'])->name('parametres.update');
    Route::post('/parametres/ordonnances/templates', [ParametresController::class, 'storeOrdonnanceTemplate'])->name('parametres.ordonnances.templates.store');
    Route::put('/parametres/ordonnances/templates/{template}', [ParametresController::class, 'updateOrdonnanceTemplate'])->whereNumber('template')->name('parametres.ordonnances.templates.update');
    Route::patch('/parametres/ordonnances/templates/{template}/toggle', [ParametresController::class, 'toggleOrdonnanceTemplate'])->whereNumber('template')->name('parametres.ordonnances.templates.toggle');
    Route::delete('/parametres/ordonnances/templates/{template}', [ParametresController::class, 'destroyOrdonnanceTemplate'])->whereNumber('template')->name('parametres.ordonnances.templates.destroy');
    Route::post('/parametres/reset', [ParametresController::class, 'reset'])->name('parametres.reset');
    Route::get('/parametres/export', [ParametresController::class, 'export'])->name('parametres.export');
    Route::post('/parametres/smtp/test', [ParametresController::class, 'testSmtp'])->name('parametres.test-smtp');
    Route::post('/parametres/backup', [ParametresController::class, 'backup'])->name('parametres.backup');
    Route::get('/parametres/backup/download', [ParametresController::class, 'downloadBackup'])->name('parametres.backup-download');
    Route::post('/parametres/backup/restore', [ParametresController::class, 'restoreBackup'])->name('parametres.backup-restore');
    Route::post('/parametres/system/clear-cache', [ParametresController::class, 'clearSystemCaches'])->name('parametres.clear-cache');
    Route::get('/parametres/system/stats', [ParametresController::class, 'systemStats'])->name('parametres.system-stats');

    Route::prefix('admin/settings')->name('admin.settings.')->group(function () {
        Route::get('/', [GovernanceCenterController::class, 'index'])->name('index');
        Route::get('/general', [GovernanceCenterController::class, 'general'])->name('general');
        Route::get('/rbac', [GovernanceCenterController::class, 'rbac'])->name('rbac');
        Route::post('/rbac/users/{user}/roles', [GovernanceCenterController::class, 'updateRbacRoles'])->name('rbac.update-roles');
        Route::post('/rbac/users/{user}/overrides', [GovernanceCenterController::class, 'updateRbacOverrides'])->name('rbac.update-overrides');
        Route::get('/security', [GovernanceCenterController::class, 'security'])->name('security');
        Route::get('/audit', [GovernanceCenterController::class, 'audit'])->name('audit');
        Route::get('/audit/export', [GovernanceCenterController::class, 'exportAudit'])->name('audit.export');
        Route::post('/audit/retention', [GovernanceCenterController::class, 'updateAuditRetention'])->name('audit.retention');
        Route::get('/notifications', [GovernanceCenterController::class, 'notifications'])->name('notifications');
        Route::get('/performance', [GovernanceCenterController::class, 'performance'])->name('performance');
        Route::get('/integrations', [GovernanceCenterController::class, 'integrations'])->name('integrations');
    });
});

// Support & Aide
Route::view('/urgence', 'urgence.index')->middleware(['auth'])->name('urgence.index');

Route::view('/aide', 'aide.index')->name('aide.index');

Route::view('/contact', 'contact.index')->name('contact.index');

// Ã‰quipe mÃ©dicale supplÃ©mentaire
Route::view('/infirmiers', 'infirmiers.index')->middleware(['auth', 'role:admin'])->name('infirmiers.index');

Route::view('/specialites', 'specialites.index')->middleware(['auth', 'role:admin'])->name('specialites.index');

Route::view('/gardes', 'gardes.index')->middleware(['auth', 'role:admin'])->name('gardes.index');

// Planning & Ã‰quipements
Route::get('/planning', function (\Illuminate\Http\Request $request) {
    return redirect()->route('agenda.index', $request->query());
})->middleware(['auth', 'module.access:planning'])->name('planning.index');

Route::view('/salles', 'salles.index')->middleware(['auth', 'module.access:planning'])->name('salles.index');

// Examens & Tests
Route::view('/examen', 'examen.index')->middleware(['auth', 'module.access:examens'])->name('examen.index');

// Rapports
use App\Http\Controllers\RapportController;
use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ExamenController;
use App\Http\Controllers\CertificatMedicalController;
use App\Http\Controllers\SMSReminderController;

// DÃ©penses
Route::get('/depenses/export', [DepenseController::class, 'export'])->middleware(['auth', 'module.access:depenses'])->name('depenses.export');
Route::get('/depenses/statistiques', [DepenseController::class, 'statistiques'])->middleware(['auth', 'module.access:depenses'])->name('depenses.statistiques');
Route::resource('depenses', DepenseController::class)->middleware(['auth', 'module.access:depenses']);

// Contacts
Route::post('/contacts/{contact}/toggle-favorite', [ContactController::class, 'toggleFavorite'])->middleware(['auth', 'module.access:contacts'])->name('contacts.toggle-favorite');
Route::post('/contacts/{contact}/toggle-active', [ContactController::class, 'toggleActive'])->middleware(['auth', 'module.access:contacts'])->name('contacts.toggle-active');
Route::get('/contacts/export', [ContactController::class, 'export'])->middleware(['auth', 'module.access:contacts'])->name('contacts.export');
Route::resource('contacts', ContactController::class)->middleware(['auth', 'module.access:contacts']);

// Examens & Bilans
Route::resource('examens', ExamenController::class)->middleware(['auth', 'module.access:examens']);
Route::post('/examens/{examen}/resultats', [ExamenController::class, 'addResultat'])->middleware(['auth', 'module.access:examens'])->name('examens.add-resultat');
Route::delete('/resultats-examens/{resultat}', [ExamenController::class, 'deleteResultat'])->middleware(['auth', 'module.access:examens'])->name('resultats.delete');
Route::get('/examens/export', [ExamenController::class, 'export'])->middleware(['auth', 'module.access:examens'])->name('examens.export');

// Certificats MÃ©dicaux
Route::get('/certificats/export', [CertificatMedicalController::class, 'export'])->middleware(['auth', 'module.access:examens'])->name('certificats.export');
Route::resource('certificats', CertificatMedicalController::class)->middleware(['auth', 'module.access:examens']);
Route::get('/certificats/{certificat}/pdf', [CertificatMedicalController::class, 'downloadPDF'])->middleware(['auth', 'module.access:examens'])->name('certificats.pdf');
Route::post('/certificats/{certificat}/transmis', [CertificatMedicalController::class, 'marquerTransmis'])->middleware(['auth', 'module.access:examens'])->name('certificats.transmis');

// SMS Reminders
Route::middleware(['auth', 'module.access:sms'])->group(function () {
    Route::post('/sms/store', [SMSReminderController::class, 'store'])->name('sms.store');
    Route::post('/sms/{reminder}/cancel', [SMSReminderController::class, 'cancel'])->name('sms.cancel');
    Route::post('/sms/test', [SMSReminderController::class, 'sendTest'])->name('sms.test');
});

// Rapports
Route::middleware(['auth', 'module.access:rapports'])->group(function () {
    Route::get('/rapports', [RapportController::class, 'index'])->name('rapports.index');
    Route::post('/rapports/monthly', [RapportController::class, 'generateMonthlyReport'])->name('rapports.monthly');
    Route::post('/rapports/financial', [RapportController::class, 'generateFinancialReport'])->name('rapports.financial');
    Route::post('/rapports/patient', [RapportController::class, 'generatePatientReport'])->name('rapports.patient');
    Route::post('/rapports/medicament', [RapportController::class, 'generateMedicamentReport'])->name('rapports.medicament');

});

// User Preferences
Route::middleware('auth')->group(function () {
    Route::post('/user/preferences/sidebar', [UserPreferenceController::class, 'updateSidebarPreference']);
    Route::get('/user/preferences', [UserPreferenceController::class, 'getUserPreferences']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/profile/2fa', [TwoFactorController::class, 'show'])->name('profile.2fa.show');
    Route::post('/profile/2fa/enable', [TwoFactorController::class, 'enable'])->name('profile.2fa.enable');
    Route::post('/profile/2fa/disable', [TwoFactorController::class, 'disable'])->name('profile.2fa.disable');
    Route::post('/profile/2fa/recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('profile.2fa.recovery');
});

// Alias historique vers les vraies archives des dossiers médicaux
Route::redirect('/archives', '/dossiers/archives')->middleware(['auth', 'module.access:patients'])->name('archives.index');


// API pour FullCalendar et fonctionnalitÃ©s AJAX
Route::prefix('api')->middleware(['auth', 'module.access:planning'])->group(function () {
    Route::get('/rendezvous', [RendezVousController::class, 'apiEvents']);
    Route::get('/rendezvous/statistiques', [RendezVousController::class, 'statistiques']);

    // Nouvelles routes pour le formulaire de rendez-vous
    Route::get('/patients/search', [RendezVousController::class, 'searchPatients'])->name('api.patients.search');
    Route::get('/medecins/{medecin}/availability', [RendezVousController::class, 'getDoctorAvailability']);
    Route::get('/consultation-types', [RendezVousController::class, 'getConsultationTypes']);
    Route::get('/suggest-slot', [RendezVousController::class, 'suggestNextSlot']);
});

// Routes Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::resource('/medecins', MedecinController::class)->names('admin.medecins');
    Route::resource('/patients', PatientController::class)->names('admin.patients');
});

// Gestion des utilisateurs (admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('utilisateurs/{utilisateur}/activity', [UserManagementController::class, 'activity'])->name('utilisateurs.activity');
    Route::post('utilisateurs/{utilisateur}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('utilisateurs.toggle-status');
    Route::post('utilisateurs/{utilisateur}/reset-password', [UserManagementController::class, 'resetPassword'])->name('utilisateurs.reset-password');
    Route::resource('utilisateurs', UserManagementController::class)->parameters([
        'utilisateurs' => 'utilisateur',
    ])->except(['show']);
});


