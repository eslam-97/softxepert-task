<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $task = $this->route('task');

        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'assigned_user_id' => 'sometimes|exists:users,id',
            'due_date' => 'sometimes|date',
            'status' => 'sometimes|in:pending,completed,canceled',
            'dependencies' => 'sometimes|array',
            'dependencies.*' => [
                'exists:tasks,id',
                Rule::notIn([$task->id])
            ],
        ];
    }
}
