<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCorrectiveActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['corrective', 'preventive'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'critical'])],
            'description' => ['required', 'string', 'max:5000'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'due_date' => ['nullable', 'date'],
            'root_cause' => ['nullable', 'string', 'max:3000'],
            'action_taken' => ['nullable', 'string', 'max:3000'],
            'finding_id' => ['nullable', 'integer', 'exists:audit_findings,id'],
            'work_order_id' => ['nullable', 'integer', 'exists:work_orders,id'],
        ];
    }
}
