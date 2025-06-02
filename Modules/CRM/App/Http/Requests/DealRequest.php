<?php

namespace Modules\CRM\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DealRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'stage' => 'required|string|in:prospecting,qualification,proposal,negotiation,closed_won,closed_lost',
            'expected_close_date' => 'nullable|date',
            'probability' => 'nullable|integer|min:0|max:100',
            'contact_id' => 'nullable|exists:crm_contacts,id',
            'company_id' => 'nullable|exists:crm_companies,id',
            'description' => 'nullable|string|max:1000',
            'source' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|exists:users,id',
            'products' => 'nullable|array',
            'products.*' => 'string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'status' => 'required|string|in:active,inactive,won,lost',
        ];
    }
}
