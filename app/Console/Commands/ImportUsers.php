<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ImportUsers extends Command
{
    protected $signature = 'import:users';
    protected $description = 'Import users from an external API and save to the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Consumir la API
        $response = Http::get('https://jsonplaceholder.typicode.com/users');

        if ($response->successful()) {
            $users = $response->json();

            // Guardar los usuarios en la base de datos
            foreach ($users as $userData) {
                User::updateOrCreate(
                    ['email' => $userData['email']], // Evita duplicados usando el email
                    [
                        'name' => $userData['name'],
                        'username' => $userData['username'],
                        'email' => $userData['email'],
                        'role' => 'usuarios', // Asignar el rol de "usuarios"
                        'password' => Hash::make('123456789'), // Encriptar la contraseña
                    ]
                );
            }

            $this->info('Usuarios importados correctamente.');
        } else {
            $this->error('Error al consumir la API.');
        }

        return 0;
    }
}
