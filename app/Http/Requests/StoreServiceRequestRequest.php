<?php

namespace App\Http\Requests;

use App\Enums\ServiceRequestCategory;
use App\Enums\ServiceRequestPriority;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreServiceRequestRequest extends FormRequest
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
            'title'                => ['required', 'string', 'max:200'],
            'category'             => ['required', new Enum(ServiceRequestCategory::class)],
            'priority'             => ['required', new Enum(ServiceRequestPriority::class)],
            'description'          => ['nullable', 'string'],
            'asset_id'             => ['nullable', 'exists:assets,id'],
            'assigned_to'          => ['nullable', 'exists:users,id'],
            'location_description' => ['nullable', 'string', 'max:200'],
        ];
    }
}
