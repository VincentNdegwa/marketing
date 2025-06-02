<?php

namespace Modules\CRM\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
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
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|string|in:call,meeting,email,task,deadline,other',
            'status' => 'required|string|in:planned,in_progress,completed,cancelled',
            'due_date' => 'nullable|date',
            'duration' => 'nullable|integer|min:0',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'creator_id' => 'nullable|exists:users,id',
            'assignee_id' => 'nullable|exists:users,id',
            'activitable_type' => 'nullable|string|in:Modules\\CRM\\App\\Models\\Contact,Modules\\CRM\\App\\Models\\Company,Modules\\CRM\\App\\Models\\Deal',
            'activitable_id' => 'nullable|integer|required_with:activitable_type',
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
            'activitable_type' => 'related entity type',
            'activitable_id' => 'related entity',
        ];
    }
}
