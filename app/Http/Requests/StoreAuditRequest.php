<?php

namespace App\Http\Requests;

use App\Enums\AuditType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAuditRequest extends FormRequest
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
            'type' => ['required', Rule::enum(AuditType::class)],
            'scope' => ['nullable', 'string', 'max:2000'],
            'auditor_id' => ['nullable', 'integer', 'exists:users,id'],
            'scheduled_date' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
