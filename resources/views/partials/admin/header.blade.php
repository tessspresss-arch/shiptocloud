<header class="bg-white shadow px-6 py-4 flex justify-between items-center">

    <!-- Titre / Date -->
    <div>
        <h1 class="text-xl font-semibold text-gray-800">
            Tableau de bord
        </h1>
        <p class="text-sm text-gray-500">
            {{ $date_aujourdhui ?? '' }}
        </p>
    </div>

    <!-- Profil utilisateur -->
    <div class="flex items-center space-x-4">

        <span class="text-gray-700 font-medium">
            {{ auth()->user()->name ?? 'Utilisateur' }}
        </span>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="text-red-600 hover:text-red-800 font-medium">
                Déconnexion
            </button>
        </form>

    </div>

</header>
