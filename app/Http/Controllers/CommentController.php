<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // Listar comentarios de una tarea
    public function index(Request $request, Task $task)
    {
        $user = $request->user();

        // Verificar acceso a la tarea
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
            return response()->json(['message' => 'No tienes acceso a esta tarea.'], 403);
        }

        $comments = $task->comments()
            ->with('user:id,name,role')
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Comentarios obtenidos correctamente',
            'data'    => $comments,
        ]);
    }

    // Crear comentario
    public function store(Request $request, Task $task)
    {
        $user = $request->user();

        // Verificar acceso a la tarea
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
            return response()->json(['message' => 'No tienes acceso a esta tarea.'], 403);
        }

        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment = Comment::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'body'    => $request->body,
        ]);

        return response()->json([
            'message' => 'Comentario agregado correctamente',
            'data'    => $comment->load('user:id,name,role'),
        ], 201);
    }

    // Eliminar comentario
    public function destroy(Request $request, Task $task, Comment $comment)
    {
        $user = $request->user();

        // Solo el autor o admin puede eliminar
        if (!$user->isAdmin() && $comment->user_id !== $user->id) {
            return response()->json(['message' => 'No puedes eliminar este comentario.'], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comentario eliminado correctamente',
        ]);
    }
}
