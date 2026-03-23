use App\Models\User;

// Vérifier si l'utilisateur existe déjà
$user = User::where('email', 'admin@cabinet.com')->first();

if ($user) {
    echo "Utilisateur déjà existant.\n";
} else {
    // Créez l'utilisateur admin
    $user = User::create([
        'name' => 'Administrateur',
        'email' => 'admin@cabinet.com',
        'password' => bcrypt('Admin123'),
        'role' => 'admin',
    ]);

    echo "Utilisateur créé: " . $user->email . "\n";
}
