<?php

namespace App\Http\Requests;

use App\Enums\LocationType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateLocationRequest extends FormRequest
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
            'name'        => ['required', 'string', 'max:255'],
            'code'        => ['nullable', 'string', 'max:50'],
            'type'        => ['required', Rule::enum(LocationType::class)],
            'parent_id'   => [
                'nullable',
                Rule::exists('locations', 'id')->where('tenant_id', Auth::user()->tenant_id),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'address'     => ['nullable', 'string', 'max:500'],
        ];
    }
}
