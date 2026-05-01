<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        // Tareas por estado
        $byStatus = Task::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($item) => [$item->status => $item->total]);

        // Tareas vencidas
        $overdue = Task::where('due_date', '<', now())
            ->where('status', '!=', 'completed')
            ->count();

        // Tareas por employee (top 5)
        $byEmployee = User::where('role', 'employee')
            ->withCount('tasks')
            ->orderByDesc('tasks_count')
            ->limit(5)
            ->get()
            ->map(fn($u) => [
                'name'  => $u->name,
                'total' => $u->tasks_count,
            ]);

        // Tareas creadas últimos 7 días
        $lastWeek = Task::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(fn($item) => [$item->date => $item->total]);

        // Rellenar días sin tareas con 0
        $days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $days[$date] = $lastWeek[$date] ?? 0;
        }

        return response()->json([
            'message' => 'Estadísticas obtenidas correctamente',
            'data'    => [
                'by_status'   => [
                    'pending'   => $byStatus['pending']   ?? 0,
                    'completed' => $byStatus['completed'] ?? 0,
                    'overdue'   => $overdue,
                ],
                'by_employee' => $byEmployee,
                'last_week'   => $days,
            ],
        ]);
    }
}
