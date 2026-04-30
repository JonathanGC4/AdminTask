<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\Request;

class TaskController extends Controller
{
public function index(Request $request)
{
    $user  = $request->user();
    $query = $user->isAdmin()
        ? Task::with(['user', 'assignedBy'])->latest()
        : Task::where('user_id', $user->id)->latest();

    if ($request->has('status') && in_array($request->status, ['pending', 'completed'])) {
        $query->where('status', $request->status);
    }

    if ($request->has('search')) {
        $query->where('title', 'like', '%' . $request->search . '%');
    }

    if ($request->has('user_id') && $user->isAdmin()) {
        $query->where('user_id', $request->user_id);
    }

    // Filtro por tareas vencidas
    if ($request->has('overdue') && $request->overdue) {
        $query->where('due_date', '<', now())
              ->where('status', '!=', 'completed');
    }

    $perPage = $request->get('per_page', 10);
    $tasks   = $query->paginate($perPage);

    // Agregar flags de vencimiento a cada tarea
    $items = collect($tasks->items())->map(function ($task) {
        $task->is_overdue  = $task->isOverdue();
        $task->is_due_soon = $task->isDueSoon();
        return $task;
    });

    return response()->json([
        'message'      => 'Tareas obtenidas correctamente',
        'data'         => $items,
        'total'        => $tasks->total(),
        'per_page'     => $tasks->perPage(),
        'current_page' => $tasks->currentPage(),
        'last_page'    => $tasks->lastPage(),
    ]);

    }

    public function store(StoreTaskRequest $request)
    {
        // Solo admin puede crear tareas
        if (! $request->user()->isAdmin()) {
            return response()->json(['message' => 'No tienes permisos.'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $task = Task::create([
            'title'       => $request->title,
            'description' => $request->description,
            'status'      => $request->status ?? 'pending',
            'user_id'     => $request->user_id,
            'assigned_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Tarea asignada correctamente',
            'data'    => $task->load(['user', 'assignedBy']),
        ], 201);
    }

    public function show(Request $request, Task $task)
    {
        $user = $request->user();

        if (! $user->isAdmin() && $task->user_id !== $user->id) {
            return response()->json(['message' => 'No tienes permiso.'], 403);
        }

        return response()->json([
            'message' => 'Tarea obtenida correctamente',
            'data'    => $task->load(['user', 'assignedBy']),
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $user = $request->user();

        if (! $user->isAdmin() && $task->user_id !== $user->id) {
            return response()->json(['message' => 'No tienes permiso.'], 403);
        }

        // Employee solo puede cambiar el status
        if (! $user->isAdmin()) {
            $task->update(['status' => $request->status]);
        } else {
            $task->update($request->validated());
        }

        return response()->json([
            'message' => 'Tarea actualizada correctamente',
            'data'    => $task->load(['user', 'assignedBy']),
        ]);
    }

    public function destroy(Request $request, Task $task)
    {
        // Solo admin puede eliminar
        if (! $request->user()->isAdmin()) {
            return response()->json(['message' => 'No tienes permiso.'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Tarea eliminada correctamente']);
    }
}
