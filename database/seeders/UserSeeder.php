<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin principal
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@taskmanager.com',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);

        // Employees
        $employees = [
            ['name' => 'Carlos Méndez',   'email' => 'carlos@taskmanager.com'],
            ['name' => 'María González',  'email' => 'maria@taskmanager.com'],
            ['name' => 'Luis Rodríguez',  'email' => 'luis@taskmanager.com'],
            ['name' => 'Ana Martínez',    'email' => 'ana@taskmanager.com'],
            ['name' => 'Pedro Sánchez',   'email' => 'pedro@taskmanager.com'],
        ];

        foreach ($employees as $employee) {
            User::create([
                'name'     => $employee['name'],
                'email'    => $employee['email'],
                'password' => Hash::make('password123'),
                'role'     => 'employee',
            ]);
        }
    }
}
