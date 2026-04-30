<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'sometimes|in:pending,completed',
            'due_date'    => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.max'  => 'El título no puede superar 255 caracteres.',
            'status.in'  => 'El estado debe ser pending o completed.',
        ];
    }
}
