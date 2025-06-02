<?php

namespace Modules\CRM\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NoteRequest extends FormRequest
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
            'content' => 'required|string|max:5000',
            'user_id' => 'nullable|exists:users,id',
            'notable_type' => 'nullable|string|in:Modules\\CRM\\App\\Models\\Contact,Modules\\CRM\\App\\Models\\Company,Modules\\CRM\\App\\Models\\Deal',
            'notable_id' => 'nullable|integer|required_with:notable_type',
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
            'notable_type' => 'related entity type',
            'notable_id' => 'related entity',
        ];
    }
}
