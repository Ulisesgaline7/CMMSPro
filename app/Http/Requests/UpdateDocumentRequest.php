<?php

namespace App\Http\Requests;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentRequest extends FormRequest
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
            'description' => ['nullable', 'string', 'max:5000'],
            'type' => ['required', Rule::enum(DocumentType::class)],
            'status' => ['nullable', Rule::enum(DocumentStatus::class)],
            'category' => ['nullable', 'string', 'max:100'],
            'current_version' => ['nullable', 'string', 'max:20'],
            'review_date' => ['nullable', 'date'],
            'asset_id' => ['nullable', 'integer', 'exists:assets,id'],
        ];
    }
}
