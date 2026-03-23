<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration - MEDISY')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">

    @vite([
        'resources/css/app.css',
        'resources/css/sidebar.css',
        'resources/css/sidebar-enhanced.css',
        'resources/css/dashboard.css',
        'resources/js/app.js',
        'resources/js/sidebar.js',
    ])

    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">

        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>MEDISY</h2>
                <span>Gestion médicale du cabinet</span>
            </div>

            <nav class="main-navigation">
                <div class="sidebar-section">
                    <h4 class="section-title">TABLEAU DE BORD</h4>
                    <ul class="menu">
                        <li>
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-home"></i> Vue d'ensemble
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="sidebar-section">
                    <h4 class="section-title">GESTION PATIENTS</h4>
                    <ul class="menu">
                        <li><a href="{{ route('patients.index') }}">Patients</a></li>
                        <li><a href="{{ route('dossiers.index') }}">Dossiers M&eacute;dicaux</a></li>
                        <li><a href="{{ route('dossiers.archives') }}">Archives</a></li>
                    </ul>
                </div>

                <div class="sidebar-section">
                    <h4 class="section-title">ACTIVIT&Eacute; M&Eacute;DICALE</h4>
                    <ul class="menu">
                        <li><a href="{{ route('rendezvous.index') }}">Rendez-vous</a></li>
                        <li><a href="{{ route('consultations.index') }}">Consultations</a></li>
                        <li><a href="{{ route('ordonnances.index') }}">Ordonnances</a></li>
                        <li><a href="{{ route('urgence.index') }}">Urgences</a></li>
                        <li><a href="{{ route('salles.index') }}">Gestion Salles</a></li>
                    </ul>
                </div>

                <div class="sidebar-section">
                    <h4 class="section-title">ADMINISTRATION</h4>
                    <ul class="menu">
                        <li><a href="{{ route('medecins.index') }}">M&eacute;decins</a></li>
                        <li><a href="{{ route('infirmiers.index') }}">Infirmiers</a></li>
                        <li><a href="{{ route('specialites.index') }}">Sp&eacute;cialit&eacute;s</a></li>
                        <li><a href="{{ route('medecins.index') }}">Utilisateurs</a></li>
                        <li><a href="{{ route('parametres.index') }}">Param&egrave;tres</a></li>
                    </ul>
                </div>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col">
            @include('partials.admin.header')

            <main class="flex-1 p-6 bg-gray-50">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
