<?php

namespace App\Http\Requests;

use App\Enums\AssetCriticality;
use App\Enums\AssetStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
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
            'name'                => ['required', 'string', 'max:255'],
            'code'                => ['required', 'string', 'max:50', Rule::unique('assets', 'code')->ignore($this->route('asset'))],
            'serial_number'       => ['nullable', 'string', 'max:255'],
            'brand'               => ['nullable', 'string', 'max:100'],
            'model'               => ['nullable', 'string', 'max:100'],
            'manufacture_year'    => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'purchase_date'       => ['nullable', 'date'],
            'installation_date'   => ['nullable', 'date'],
            'warranty_expires_at' => ['nullable', 'date'],
            'purchase_cost'       => ['nullable', 'numeric', 'min:0'],
            'status'              => ['required', Rule::enum(AssetStatus::class)],
            'criticality'         => ['required', Rule::enum(AssetCriticality::class)],
            'location_id'         => ['nullable', 'integer', 'exists:locations,id'],
            'asset_category_id'   => ['nullable', 'integer', 'exists:asset_categories,id'],
            'parent_id'           => ['nullable', 'integer', Rule::exists('assets', 'id')->whereNot('id', $this->route('asset')?->id)],
            'notes'               => ['nullable', 'string', 'max:5000'],
        ];
    }
}
