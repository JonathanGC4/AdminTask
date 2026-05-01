<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $admin     = User::where('role', 'admin')->first();
        $employees = User::where('role', 'employee')->get();

        $tasks = [
            // Tareas pendientes normales
            [
                'title'       => 'Revisar documentación del proyecto',
                'description' => 'Leer y validar los documentos técnicos del Q1.',
                'status'      => 'pending',
                'due_date'    => Carbon::now()->addDays(5),
            ],
            [
                'title'       => 'Actualizar dependencias del sistema',
                'description' => 'Revisar y actualizar todas las librerías desactualizadas.',
                'status'      => 'pending',
                'due_date'    => Carbon::now()->addDays(3),
            ],
            [
                'title'       => 'Preparar informe mensual',
                'description' => 'Consolidar métricas y resultados del mes anterior.',
                'status'      => 'pending',
                'due_date'    => Carbon::now()->addDays(7),
            ],
            [
                'title'       => 'Reunión con cliente',
                'description' => 'Presentar avances del sprint actual.',
                'status'      => 'pending',
                'due_date'    => Carbon::now()->addDays(1),
            ],
            [
                'title'       => 'Configurar ambiente de staging',
                'description' => 'Preparar el servidor de pruebas para el deploy.',
                'status'      => 'pending',
                'due_date'    => Carbon::now()->addDays(4),
            ],

            // Tareas completadas
            [
                'title'       => 'Diseñar base de datos',
                'description' => 'Crear el esquema inicial con todas las relaciones.',
                'status'      => 'completed',
                'due_date'    => Carbon::now()->subDays(2),
            ],
            [
                'title'       => 'Implementar autenticación',
                'description' => 'Login y registro con Laravel Sanctum.',
                'status'      => 'completed',
                'due_date'    => Carbon::now()->subDays(5),
            ],
            [
                'title'       => 'Crear endpoints de usuarios',
                'description' => 'CRUD completo con validaciones.',
                'status'      => 'completed',
                'due_date'    => Carbon::now()->subDays(3),
            ],

            // Tareas vencidas (due_date en el pasado y pendientes)
            [
                'title'       => 'Corregir bugs reportados',
                'description' => 'Resolver los 3 bugs críticos del backlog.',
                'status'      => 'pending',
                'due_date'    => Carbon::now()->subDays(2),
            ],
            [
                'title'       => 'Optimizar queries lentas',
                'description' => 'Mejorar performance de las consultas principales.',
                'status'      => 'pending',
                'due_date'    => Carbon::now()->subDays(4),
            ],
            [
                'title'       => 'Escribir tests unitarios',
                'description' => 'Cubrir al menos 70% del código con tests.',
                'status'      => 'pending',
                'due_date'    => Carbon::now()->subDays(1),
            ],

            // Tareas sin fecha límite
            [
                'title'       => 'Mejorar UI del dashboard',
                'description' => 'Ajustes visuales según feedback del equipo.',
                'status'      => 'pending',
                'due_date'    => null,
            ],
            [
                'title'       => 'Investigar nueva librería de gráficas',
                'description' => 'Evaluar opciones para el módulo de reportes.',
                'status'      => 'pending',
                'due_date'    => null,
            ],
        ];

        foreach ($tasks as $index => $taskData) {
            // Distribuir tareas entre employees de forma rotativa
            $employee = $employees[$index % $employees->count()];

            Task::create([
                'title'       => $taskData['title'],
                'description' => $taskData['description'],
                'status'      => $taskData['status'],
                'due_date'    => $taskData['due_date'],
                'user_id'     => $employee->id,
                'assigned_by' => $admin->id,
            ]);
        }
    }
}
