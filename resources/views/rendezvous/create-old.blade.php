<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Rendez-vous - Cabinet Médical</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-bg: #f8fafc;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .main-container {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin: 2rem auto;
            max-width: 1200px;
        }

        .header-section {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 2rem;
            position: relative;
        }

        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="50" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.1;
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .form-section {
            padding: 2rem;
        }

        .section-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .section-card:hover {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .section-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            border-radius: 16px 16px 0 0;
        }

        .section-title {
            color: #1f2937;
            font-weight: 600;
            font-size: 1.125rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-body {
            padding: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .required::after {
            content: '*';
            color: var(--danger-color);
            font-weight: bold;
        }

        .form-control, .form-select {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .select2-container--default .select2-selection--single {
            height: 42px !important;
            border: 2px solid #e5e7eb !important;
            border-radius: 8px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
            padding-left: 12px !important;
            color: #374151 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af !important;
        }

        .btn-modern {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);
            color: white;
        }

        .btn-primary-modern:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-secondary-modern {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-secondary-modern:hover {
            background: #e5e7eb;
        }

        .patient-info-card {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 1px solid #a7f3d0;
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1rem;
            display: none;
        }

        .doctor-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .doctor-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.1);
        }

        .doctor-card.selected {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.05);
        }

        .doctor-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .availability-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .available { background-color: var(--success-color); }
        .unavailable { background-color: var(--danger-color); }
        .partial { background-color: var(--warning-color); }

        .time-slots {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .time-slot {
            padding: 0.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }

        .time-slot:hover {
            border-color: var(--primary-color);
        }

        .time-slot.selected {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .time-slot.unavailable {
            background: #f3f4f6;
            color: #9ca3af;
            cursor: not-allowed;
        }

        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #e5e7eb;
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .main-container {
                margin: 1rem;
                border-radius: 12px;
            }

            .header-section, .form-section {
                padding: 1rem;
            }

            .time-slots {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="main-container">
            <!-- Header -->
            <div class="header-section">
                <div class="header-content">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h1 class="mb-2">
                                <i class="fas fa-calendar-plus me-3"></i>
                                Nouveau Rendez-vous
                            </h1>
                            <p class="mb-0 opacity-75">Planifiez un nouveau rendez-vous médical</p>
                        </div>
                        <a href="{{ route('agenda.index') }}" class="btn btn-light btn-modern">
                            <i class="fas fa-arrow-left me-2"></i>
                            Retour à l'agenda
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="form-section">
                <form id="appointmentForm" action="{{ route('rendezvous.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-lg-8">
                            <!-- Patient Selection -->
                            <div class="section-card fade-in">
                                <div class="section-header">
                                    <h3 class="section-title">
                                        <i class="fas fa-user text-primary"></i>
                                        Sélection du Patient
                                    </h3>
                                </div>
                                <div class="section-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <label class="form-label required">Patient</label>
                                            <select id="patientSelect" class="form-select" name="patient_id" required>
                                                <option value="">Rechercher un patient...</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="button" class="btn btn-primary-modern btn-modern w-100" data-bs-toggle="modal" data-bs-target="#createPatientModal">
                                                <i class="fas fa-plus me-2"></i>
                                                Nouveau Patient
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Patient Info Display -->
                                    <div id="patientInfo" class="patient-info-card">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Âge:</strong> <span id="patientAge">-</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Téléphone:</strong> <span id="patientPhone">-</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>CIN:</strong> <span id="patientCin">-</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Genre:</strong> <span id="patientGenre">-</span>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <strong>Adresse:</strong> <span id="patientAddress">-</span>
                                            </div>
                                        </div>
                                        <div id="patientAlerts" class="mt-2" style="display: none;">
                                            <div class="alert alert-warning py-2">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong>Alertes:</strong> <span id="alertsText"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Doctor Selection -->
                            <div class="section-card fade-in">
                                <div class="section-header">
                                    <h3 class="section-title">
                                        <i class="fas fa-user-md text-success"></i>
                                        Sélection du Médecin
                                    </h3>
                                </div>
                                <div class="section-body">
                                    <div class="mb-3">
                                        <label class="form-label">Filtrer par spécialité</label>
                                        <select id="specialtyFilter" class="form-select">
                                            <option value="">Toutes les spécialités</option>
                                            <option value="Médecine générale">Médecine générale</option>
                                            <option value="Cardiologie">Cardiologie</option>
                                            <option value="Dermatologie">Dermatologie</option>
                                            <option value="Ophtalmologie">Ophtalmologie</option>
                                            <option value="Pédiatrie">Pédiatrie</option>
                                        </select>
                                    </div>

                                    <div id="doctorsList">
                                        <!-- Doctors will be loaded here -->
                                    </div>
                                </div>
                            </div>

                            <!-- Date & Time -->
                            <div class="section-card fade-in">
                                <div class="section-header">
                                    <h3 class="section-title">
                                        <i class="fas fa-calendar-alt text-warning"></i>
                                        Date & Heure
                                    </h3>
                                </div>
                                <div class="section-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label required">Date</label>
                                            <input type="text" id="datePicker" class="form-control" name="date" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label required">Heure</label>
                                            <input type="text" id="timePicker" class="form-control" name="heure" required readonly>
                                        </div>
                                    </div>

                                    <div id="timeSlots" class="time-slots" style="display: none;">
                                        <!-- Time slots will be loaded here -->
                                    </div>

                                    <div class="mt-3">
                                        <button type="button" id="suggestSlotBtn" class="btn btn-secondary-modern btn-modern">
                                            <i class="fas fa-magic me-2"></i>
                                            Suggérer le prochain créneau libre
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Appointment Details -->
                            <div class="section-card fade-in">
                                <div class="section-header">
                                    <h3 class="section-title">
                                        <i class="fas fa-clipboard-list text-info"></i>
                                        Détails du Rendez-vous
                                    </h3>
                                </div>
                                <div class="section-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label required">Durée (minutes)</label>
                                            <select id="dureeSelect" class="form-select" name="duree" required>
                                                <option value="">Sélectionner...</option>
                                                <option value="15">15 minutes</option>
                                                <option value="20">20 minutes</option>
                                                <option value="30">30 minutes</option>
                                                <option value="45">45 minutes</option>
                                                <option value="60">1 heure</option>
                                                <option value="90">1h30</option>
                                                <option value="120">2 heures</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label required">Type de consultation</label>
                                            <select id="typeSelect" class="form-select" name="type" required>
                                                <option value="">Sélectionner...</option>
                                                <option value="Consultation générale">Consultation générale</option>
                                                <option value="Consultation spécialisée">Consultation spécialisée</option>
                                                <option value="Suivi">Suivi</option>
                                                <option value="Urgence">Urgence</option>
                                                <option value="Contrôle">Contrôle</option>
                                                <option value="Autre">Autre</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <label class="form-label">Motif du rendez-vous</label>
                                        <textarea id="motifTextarea" class="form-control" name="motif" rows="3"
                                                  placeholder="Décrivez brièvement le motif..."></textarea>
                                        <div class="form-text">
                                            <span id="charCount">0</span>/255 caractères
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <label class="form-label">Notes complémentaires</label>
                                        <textarea class="form-control" name="notes" rows="2"
                                                  placeholder="Informations supplémentaires..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-lg-4">
                            <!-- Actions -->
                            <div class="section-card fade-in">
                                <div class="section-header">
                                    <h3 class="section-title">
                                        <i class="fas fa-tasks text-primary"></i>
                                        Actions
                                    </h3>
                                </div>
                                <div class="section-body">
                                    <div class="d-grid gap-3">
                                        <button type="submit" id="submitBtn" class="btn btn-primary-modern btn-modern btn-lg" disabled>
                                            <i class="fas fa-calendar-check me-2"></i>
                                            Créer le Rendez-vous
                                        </button>

                                        <a href="{{ route('agenda.index') }}" class="btn btn-secondary-modern btn-modern">
                                            <i class="fas fa-times me-2"></i>
                                            Annuler
                                        </a>
                                    </div>

                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" id="notifyPatient" name="notify_patient" checked>
                                        <label class="form-check-label" for="notifyPatient">
                                            <i class="fas fa-bell me-2"></i>
                                            Notifier le patient
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Information -->
                            <div class="section-card fade-in">
                                <div class="section-header">
                                    <h3 class="section-title">
                                        <i class="fas fa-info-circle text-info"></i>
                                        Informations
                                    </h3>
                                </div>
                                <div class="section-body">
                                    <div class="alert alert-light border">
                                        <ul class="mb-0 small">
                                            <li>Les champs marqués <span class="text-danger">*</span> sont obligatoires</li>
                                            <li>Vérifiez la disponibilité du médecin avant de confirmer</li>
                                            <li>Un email de confirmation sera envoyé si activé</li>
                                            <li>Le rendez-vous sera créé avec le statut "Programmé"</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Patient Modal -->
    <div class="modal fade" id="createPatientModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus me-2"></i>
                        Nouveau Patient
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createPatientForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Nom</label>
                                <input type="text" class="form-control" name="nom" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Prénom</label>
                                <input type="text" class="form-control" name="prenom" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">CIN</label>
                                <input type="text" class="form-control" name="cin" placeholder="AA123456">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Date de naissance</label>
                                <input type="date" class="form-control" name="date_naissance" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Genre</label>
                                <select class="form-select" name="genre" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="M">Masculin</option>
                                    <option value="F">Féminin</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Téléphone</label>
                                <input type="tel" class="form-control" name="telephone" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Adresse</label>
                                <input type="text" class="form-control" name="adresse">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ville</label>
                                <input type="text" class="form-control" name="ville">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="loading" id="patientLoading" style="display: none;"></span>
                            Créer le patient
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for patient search
            $('#patientSelect').select2({
                placeholder: 'Rechercher un patient...',
                ajax: {
                    url: '{{ route("api.patients.search") }}',
                    dataType: 'json',
                    delay: 300,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                    data: item
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2
            });

            // Handle patient selection
            $('#patientSelect').on('select2:select', function (e) {
                const patient = e.params.data.data;
                displayPatientInfo(patient);
            });

            // Initialize date picker
            const datePicker = flatpickr("#datePicker", {
                dateFormat: "Y-m-d",
                minDate: "today",
                defaultDate: '{{ $date_preselectionnee }}',
                onChange: function(selectedDates, dateStr) {
                    loadTimeSlots();
                }
            });

            // Initialize time picker
            const timePicker = flatpickr("#timePicker", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultDate: '{{ $heure_preselectionnee }}'
            });

            // Load doctors
            loadDoctors();

            // Form validation
            $('#appointmentForm').on('input change', validateForm);

            // Character count for motif
            $('#motifTextarea').on('input', function() {
                const count = $(this).val().length;
                $('#charCount').text(count);
                if (count > 255) {
                    $(this).val($(this).val().substring(0, 255));
                    $('#charCount').text(255);
                }
            });

            // Suggest next slot
            $('#suggestSlotBtn').on('click', suggestNextSlot);

            // Create patient form
            $('#createPatientForm').on('submit', function(e) {
                e.preventDefault();
                createPatient();
            });
        });

        function displayPatientInfo(patient) {
            $('#patientAge').text(patient.age || '-');
            $('#patientPhone').text(patient.telephone || '-');
            $('#patientCin').text(patient.cin || '-');
            $('#patientGenre').text(patient.genre === 'M' ? 'Masculin' : 'Féminin');
            $('#patientAddress').text(`${patient.adresse || ''} ${patient.ville || ''}`.trim() || '-');

            if (patient.antecedents || patient.allergies) {
                $('#alertsText').text(`${patient.antecedents || ''} ${patient.allergies || ''}`.trim());
                $('#patientAlerts').show();
            } else {
                $('#patientAlerts').hide();
            }

            $('#patientInfo').fadeIn();
        }

        function loadDoctors() {
            // Mock data - replace with actual API call
            const doctors = @json($medecins);
            const specialty = $('#specialtyFilter').val();

            let html = '';
            doctors.forEach(doctor => {
                if (!specialty || doctor.specialite === specialty) {
                    html += `
                        <div class="doctor-card" data-id="${doctor.id}">
                            <div class="d-flex align-items-center">
                                <div class="doctor-avatar me-3">
                                    ${doctor.nom.charAt(0)}${doctor.prenom.charAt(0)}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">${doctor.nom_complet}</div>
                                    <div class="text-muted small">${doctor.specialite || 'Médecin généraliste'}</div>
                                    <div class="mt-1">
                                        <span class="availability-indicator available"></span>
                                        Disponible aujourd'hui
                                    </div>
                                </div>
                                <input type="radio" name="medecin_id" value="${doctor.id}" class="ms-3" required>
                            </div>
                        </div>
                    `;
                }
            });

            $('#doctorsList').html(html);

            // Handle doctor selection
            $('.doctor-card').on('click', function() {
                $('.doctor-card').removeClass('selected');
                $(this).addClass('selected');
                $(this).find('input[type="radio"]').prop('checked', true);
                loadTimeSlots();
            });
        }

        function loadTimeSlots() {
            const selectedDoctor = $('input[name="medecin_id"]:checked').val();
            const selectedDate = $('#datePicker').val();

            if (!selectedDoctor || !selectedDate) return;

            $('#timeSlots').show();

            // Mock time slots - replace with actual API call
            const slots = [
                '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
                '14:00', '14:30', '15:00', '15:30', '16:00', '16:30'
            ];

            let html = '';
            slots.forEach(slot => {
                const isAvailable = Math.random() > 0.3; // Mock availability
                html += `
                    <div class="time-slot ${isAvailable ? '' : 'unavailable'}" data-time="${slot}">
                        ${slot}
                    </div>
                `;
            });

            $('#timeSlots').html(html);

            // Handle time slot selection
            $('.time-slot:not(.unavailable)').on('click', function() {
                $('.time-slot').removeClass('selected');
                $(this).addClass('selected');
                $('#timePicker').val($(this).data('time'));
            });
        }

        function suggestNextSlot() {
            const selectedDoctor = $('input[name="medecin_id"]:checked').val();
            const selectedDate = $('#datePicker').val();
            const selectedType = $('#typeSelect').val();

            if (!selectedDoctor) {
                alert('Veuillez d\'abord sélectionner un médecin.');
                return;
            }

            // Mock suggestion - replace with actual API call
            const suggestedTime = '14:00';
            $('#datePicker').val(selectedDate || new Date().toISOString().split('T')[0]);
            $('#timePicker').val(suggestedTime);
            $('#dureeSelect').val('30');
        }

        function createPatient() {
            const formData = new FormData(document.getElementById('createPatientForm'));
            $('#patientLoading').show();

            fetch('{{ route("patients.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add to select and select it
                    const newOption = new Option(data.patient.nom + ' ' + data.patient.prenom, data.patient.id, true, true);
                    $('#patientSelect').append(newOption).trigger('change');

                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('createPatientModal')).hide();

                    // Reset form
                    document.getElementById('createPatientForm').reset();
                } else {
                    alert('Erreur lors de la création du patient');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de la création du patient');
            })
            .finally(() => {
                $('#patientLoading').hide();
            });
        }

        function validateForm() {
            const patientId = $('#patientSelect').val();
            const medecinId = $('input[name="medecin_id"]:checked').val();
            const date = $('#datePicker').val();
            const time = $('#timePicker').val();
            const duree = $('#dureeSelect').val();
            const type = $('#typeSelect').val();

            const isValid = patientId && medecinId && date && time && duree && type;
            $('#submitBtn').prop('disabled', !isValid);
        }

        // Specialty filter
        $('#specialtyFilter').on('change', loadDoctors);
    </script>
</body>
</html>



