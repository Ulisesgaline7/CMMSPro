<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetCategoryRequest extends FormRequest
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
            'code'        => ['nullable', 'string', 'max:50', Rule::unique('asset_categories', 'code')->ignore($this->route('assetCategory'))],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
