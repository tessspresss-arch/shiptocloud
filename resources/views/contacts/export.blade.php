@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <i class="fas fa-download text-purple-600"></i>
            Exporter les Contacts
        </h1>
        <p class="text-gray-600 mt-2">Télécharger la liste des contacts</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 text-center hover:shadow-lg transition cursor-pointer">
            <i class="fas fa-file-excel text-4xl text-emerald-600 mb-3"></i>
            <h3 class="font-bold text-gray-900 mb-2">Excel</h3>
            <p class="text-sm text-gray-600 mb-4">Format .xlsx</p>
            <button class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition w-full">
                Télécharger
            </button>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 text-center hover:shadow-lg transition cursor-pointer">
            <i class="fas fa-file-csv text-4xl text-blue-600 mb-3"></i>
            <h3 class="font-bold text-gray-900 mb-2">CSV</h3>
            <p class="text-sm text-gray-600 mb-4">Format .csv</p>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition w-full">
                Télécharger
            </button>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 text-center hover:shadow-lg transition cursor-pointer">
            <i class="fas fa-file-pdf text-4xl text-red-600 mb-3"></i>
            <h3 class="font-bold text-gray-900 mb-2">PDF</h3>
            <p class="text-sm text-gray-600 mb-4">Format .pdf</p>
            <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition w-full">
                Télécharger
            </button>
        </div>
    </div>
</div>
@endsection
