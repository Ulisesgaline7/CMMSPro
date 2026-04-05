<?php

namespace App\Http\Requests;

use App\Enums\WorkOrderPriority;
use App\Enums\WorkOrderType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateWorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canManageWorkOrders();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'              => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string', 'max:5000'],
            'failure_cause'      => ['nullable', 'string', 'max:5000'],
            'asset_id'           => ['nullable', 'integer', Rule::exists('assets', 'id')->where('tenant_id', Auth::user()->tenant_id)],
            'assigned_to'        => ['nullable', 'integer', 'exists:users,id'],
            'priority'           => ['required', Rule::enum(WorkOrderPriority::class)],
            'due_date'           => ['nullable', 'date'],
            'estimated_duration' => ['nullable', 'integer', 'min:1'],
            'type'               => ['required', Rule::enum(WorkOrderType::class)],
        ];
    }
}
