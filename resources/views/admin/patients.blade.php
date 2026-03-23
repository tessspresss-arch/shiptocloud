@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex flex-col md:flex-row md:space-x-4 mb-8">
        <!-- Stat Cards -->
        <div class="flex-1 bg-white rounded-xl shadow p-6 mb-4 md:mb-0 flex items-center">
            <div class="bg-blue-100 text-blue-600 rounded-full p-3 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20h6M3 20h5v-2a4 4 0 013-3.87M16 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <div class="text-2xl font-bold">8</div>
                <div class="text-gray-500">Patients actifs</div>
            </div>
        </div>
        <div class="flex-1 bg-white rounded-xl shadow p-6 mb-4 md:mb-0 flex items-center">
            <div class="bg-green-100 text-green-600 rounded-full p-3 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <div class="text-2xl font-bold">0</div>
                <div class="text-gray-500">Rendez-vous aujourd'hui</div>
            </div>
        </div>
        <div class="flex-1 bg-white rounded-xl shadow p-6 mb-4 md:mb-0 flex items-center">
            <div class="bg-yellow-100 text-yellow-600 rounded-full p-3 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0h6"/></svg>
            </div>
            <div>
                <div class="text-2xl font-bold">0</div>
                <div class="text-gray-500">Dossiers médicaux</div>
            </div>
        </div>
        <div class="flex-1 bg-white rounded-xl shadow p-6 flex items-center">
            <div class="bg-red-100 text-red-600 rounded-full p-3 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-1.414 1.414M6.343 17.657l-1.414-1.414M5.636 5.636l1.414 1.414M17.657 17.657l1.414-1.414M12 8v4m0 4h.01"/></svg>
            </div>
            <div>
                <div class="text-2xl font-bold">0</div>
                <div class="text-gray-500">Patients avec allergies</div>
            </div>
        </div>
    </div>

    <!-- Search & Actions -->
    <form method="GET" action="{{ route('admin.patients') }}" class="flex flex-col md:flex-row md:items-center md:space-x-4 mb-6">
        <input type="text" name="search" value="{{ request('search') }}" class="flex-1 rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-3 mb-2 md:mb-0" placeholder="Rechercher par nom...">
        <button type="submit" class="bg-blue-600 text-white rounded-lg px-6 py-3 font-semibold shadow hover:bg-blue-700 transition">Appliquer</button>
        <a href="{{ route('admin.patients') }}" class="bg-gray-200 text-gray-700 rounded-lg px-6 py-3 font-semibold ml-2 hover:bg-gray-300 transition">Réinitialiser</a>
        <a href="{{ route('patients.create') }}" class="bg-green-600 text-white rounded-lg px-6 py-3 font-semibold ml-2 shadow hover:bg-green-700 transition">+ Nouveau Patient</a>
        <a href="#" class="bg-gray-100 text-gray-700 rounded-lg px-6 py-3 font-semibold ml-2 hover:bg-gray-200 transition">Exporter CSV</a>
    </form>

    <!-- Patients Table -->
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID / Dossier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CIN</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Naissance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Genre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- Exemple de ligne -->
                    @foreach($patients as $patient)
                    <tr class="hover:bg-blue-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-blue-700">
                            PAT-{{ str_pad($patient->id, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ strtoupper($patient->nom) }} {{ ucfirst($patient->prenom) }}<br>
                            <span class="text-xs text-gray-400">ID: {{ $patient->id }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 text-blue-500 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"/></svg>
                                    {{ $patient->telephone }}
                                </span>
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 12v1m0 4v1m-4-5v1m0 4v1m-4-5v1m0 4v1"/></svg>
                                    {{ $patient->email }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $patient->cin ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $patient->date_naissance ? \Carbon\Carbon::parse($patient->date_naissance)->format('d/m/Y') : 'N/A' }}<br>
                            <span class="text-xs text-gray-400">
                                {{ $patient->date_naissance ? \Carbon\Carbon::parse($patient->date_naissance)->age . ' ans' : '' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($patient->genre == 'M')
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">♂ Masculin</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-pink-100 text-pink-700 text-xs font-semibold">♀ Féminin</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-2">
                                <a href="{{ route('patients.show', $patient->id) }}" class="text-blue-600 hover:text-blue-800" title="Voir">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('patients.edit', $patient->id) }}" class="text-yellow-500 hover:text-yellow-700" title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5h2m-1 0v14m-7-7h14"/></svg>
                                </a>
                                <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" onsubmit="return confirm('Supprimer ce patient ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $patients->links() }}
    </div>
</div>
@endsection
