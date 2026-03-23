// Voir tous les utilisateurs
$users = \App\Models\User::all();

if ($users->isEmpty()) {
    echo "❌ AUCUN utilisateur trouvé dans la base !\n";
} else {
    echo "📋 LISTE DES UTILISATEURS :\n";
    echo "==========================\n";
    foreach ($users as $user) {
        echo "- {$user->email} (ID: {$user->id})\n";
    }
}

// Vérifier spécifiquement admin@cabinet.com
$admin = \App\Models\User::where('email', 'admin@cabinet.com')->first();
if ($admin) {
    echo "\n✅ admin@cabinet.com EXISTE !\n";
} else {
    echo "\n❌ admin@cabinet.com N'EXISTE PAS !\n";
}
