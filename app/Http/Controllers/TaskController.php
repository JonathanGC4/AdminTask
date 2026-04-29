<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Listar tareas del usuario autenticado
// Listar tareas del usuario autenticado (con filtros y paginación)
public function index(Request $request)
{
    $query = $request->user()->tasks()->latest();

    // Filtro por status: /api/tasks?status=pending
    if ($request->has('status') && in_array($request->status, ['pending', 'completed'])) {
        $query->where('status', $request->status);
    }

    // Filtro por título: /api/tasks?search=laravel
    if ($request->has('search')) {
        $query->where('title', 'like', '%' . $request->search . '%');
    }

    // Paginación: /api/tasks?per_page=5
    $perPage = $request->get('per_page', 10);
    $tasks   = $query->paginate($perPage);

    return response()->json([
        'message'     => 'Tareas obtenidas exitosamente',
        'data'        => $tasks->items(),
        'total'       => $tasks->total(),
        'per_page'    => $tasks->perPage(),
        'current_page'=> $tasks->currentPage(),
        'last_page'   => $tasks->lastPage(),
    ]);
}

    // Crear tarea
    public function store(StoreTaskRequest $request)
    {
        $task = $request->user()->tasks()->create($request->validated());

        return response()->json([
            'message' => 'Tarea creada exitosamente',
            'data'    => $task,
        ], 201);
    }

    // Ver una tarea específica
    public function show(Request $request, Task $task)
    {
        // Verificar que la tarea pertenece al usuario autenticado
        if ($request->user()->id !== $task->user_id) {
            return response()->json([
                'message' => 'No tienes permiso para ver esta tarea',
            ], 403);
        }

        return response()->json([
            'message' => 'Tarea obtenida exitosamente',
            'data'    => $task,
        ]);
    }

    // Actualizar tarea
    public function update(UpdateTaskRequest $request, Task $task)
    {
        if ($request->user()->id !== $task->user_id) {
            return response()->json([
                'message' => 'No tienes permiso para actualizar esta tarea',
            ], 403);
        }

        $task->update($request->validated());

        return response()->json([
            'message' => 'Tarea actualizada exitosamente',
            'data'    => $task,
        ]);
    }

    // Eliminar tarea
    public function destroy(Request $request, Task $task)
    {
        if ($request->user()->id !== $task->user_id) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar esta tarea',
            ], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'Tarea eliminada exitosamente',
        ]);
    }
}
