<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $admin     = User::where('role', 'admin')->first();
        $employees = User::where('role', 'employee')->get();
        $tasks     = Task::all();

        $comments = [
            'Estoy trabajando en esto, listo para mañana.',
            'Necesito acceso al servidor para completar esta tarea.',
            'Ya terminé la primera parte, revisando detalles.',
            'Encontré un problema, lo estoy investigando.',
            '¿Puedes darme más contexto sobre los requisitos?',
            'Listo, puedes revisar los cambios en el repositorio.',
            'Voy a necesitar un día más para terminarlo correctamente.',
            'Tarea completada y testeada en local.',
            'Hay una dependencia con otra tarea que está bloqueando esto.',
            'Revisando con el equipo antes de cerrar.',
        ];

        $adminComments = [
            'Por favor prioriza esto, es urgente.',
            'Recuerda seguir los estándares del equipo.',
            'Buen trabajo, ya lo revisé.',
            'Necesito esto antes del viernes.',
            'Coordina con María para esta tarea.',
        ];

        foreach ($tasks->take(8) as $index => $task) {
            $employee = $employees[$index % $employees->count()];

            // Comentario del employee
            Comment::create([
                'task_id'    => $task->id,
                'user_id'    => $employee->id,
                'body'       => $comments[$index % count($comments)],
                'created_at' => now()->subHours(rand(1, 48)),
            ]);

            // Comentario del admin en algunas tareas
            if ($index % 2 === 0) {
                Comment::create([
                    'task_id'    => $task->id,
                    'user_id'    => $admin->id,
                    'body'       => $adminComments[$index % count($adminComments)],
                    'created_at' => now()->subHours(rand(1, 24)),
                ]);
            }
        }
    }
}
