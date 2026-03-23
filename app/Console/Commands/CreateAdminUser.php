<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \App\Models\User::create([
            'name' => 'Administrateur',
            'email' => 'admin@cabinet.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123')
        ]);

        $this->info('Admin user created successfully.');
    }
}
