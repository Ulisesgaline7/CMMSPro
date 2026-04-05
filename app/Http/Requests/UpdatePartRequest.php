<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePartRequest extends FormRequest
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
            'name'             => ['required', 'string', 'max:255'],
            'part_number'      => ['nullable', 'string', 'max:100', Rule::unique('parts', 'part_number')->ignore($this->route('part'))],
            'brand'            => ['nullable', 'string', 'max:100'],
            'description'      => ['nullable', 'string', 'max:5000'],
            'unit'             => ['required', 'string', 'max:30'],
            'stock_quantity'   => ['required', 'integer', 'min:0'],
            'min_stock'        => ['required', 'integer', 'min:0'],
            'unit_cost'        => ['nullable', 'numeric', 'min:0'],
            'storage_location' => ['nullable', 'string', 'max:255'],
        ];
    }
}
