<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,    // 1. Primero usuarios
            TaskSeeder::class,    // 2. Luego tareas (necesitan usuarios)
            CommentSeeder::class, // 3. Finalmente comentarios (necesitan tareas)
        ]);
    }
}
