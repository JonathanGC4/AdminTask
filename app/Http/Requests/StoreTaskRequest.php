<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // El control de acceso lo maneja el middleware
    }

public function rules(): array
{
    return [
        'title'       => 'required|string|max:255',
        'description' => 'nullable|string',
        'status'      => 'in:pending,completed',
        'due_date'    => 'nullable|date|after_or_equal:today',
        'user_id'     => 'required|exists:users,id',
    ];
}

public function messages(): array
{
    return [
        'title.required'        => 'El título es obligatorio.',
        'title.max'             => 'El título no puede superar 255 caracteres.',
        'status.in'             => 'El estado debe ser pending o completed.',
        'due_date.date'         => 'La fecha límite debe ser una fecha válida.',
        'due_date.after_or_equal' => 'La fecha límite no puede ser en el pasado.',
        'user_id.required'      => 'Debes asignar la tarea a un usuario.',
        'user_id.exists'        => 'El usuario seleccionado no existe.',
    ];
}
}
