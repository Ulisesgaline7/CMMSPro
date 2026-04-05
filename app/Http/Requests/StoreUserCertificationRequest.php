<?php

namespace App\Http\Requests;

use App\Enums\CertificationStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserCertificationRequest extends FormRequest
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
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'issuing_body' => ['required', 'string', 'max:255'],
            'certificate_number' => ['nullable', 'string', 'max:100'],
            'issued_at' => ['required', 'date'],
            'expires_at' => ['nullable', 'date', 'after:issued_at'],
            'status' => ['required', Rule::enum(CertificationStatus::class)],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
