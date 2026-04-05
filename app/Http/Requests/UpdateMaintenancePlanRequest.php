<?php

namespace App\Http\Requests;

use App\Enums\MaintenancePlanFrequency;
use App\Enums\WorkOrderPriority;
use App\Enums\WorkOrderType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateMaintenancePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'asset_id' => ['required', Rule::exists('assets', 'id')->where('tenant_id', Auth::user()->tenant_id)],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'type' => ['required', Rule::in(array_column(WorkOrderType::cases(), 'value'))],
            'frequency' => ['required', Rule::in(array_column(MaintenancePlanFrequency::cases(), 'value'))],
            'frequency_value' => ['nullable', 'integer', 'min:1'],
            'priority' => ['required', Rule::in(array_column(WorkOrderPriority::cases(), 'value'))],
            'estimated_duration' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'next_execution_date' => ['nullable', 'date'],
            'is_active' => ['boolean'],
        ];
    }
}
