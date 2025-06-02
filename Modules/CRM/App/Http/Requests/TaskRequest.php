<?php

namespace Modules\CRM\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'status' => 'required|string|in:not_started,in_progress,completed,deferred',
            'due_date' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'reminder_at' => 'nullable|date',
            'creator_id' => 'nullable|exists:users,id',
            'assignee_id' => 'nullable|exists:users,id',
            'taskable_type' => 'nullable|string|in:Modules\\CRM\\App\\Models\\Contact,Modules\\CRM\\App\\Models\\Company,Modules\\CRM\\App\\Models\\Deal',
            'taskable_id' => 'nullable|integer|required_with:taskable_type',
        ];
    }
    
    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'taskable_type' => 'related entity type',
            'taskable_id' => 'related entity',
        ];
    }
}
