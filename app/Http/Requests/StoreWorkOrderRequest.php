<?php

namespace App\Http\Requests;

use App\Enums\WorkOrderPriority;
use App\Enums\WorkOrderType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkOrderRequest extends FormRequest
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
            'title'                => ['required', 'string', 'max:255'],
            'description'          => ['nullable', 'string', 'max:5000'],
            'type'                 => ['required', Rule::enum(WorkOrderType::class)],
            'priority'             => ['required', Rule::enum(WorkOrderPriority::class)],
            'asset_id'             => ['required', 'integer', 'exists:assets,id'],
            'assigned_to'          => ['nullable', 'integer', 'exists:users,id'],
            'due_date'             => ['nullable', 'date', 'after_or_equal:today'],
            'estimated_duration'   => ['nullable', 'integer', 'min:1', 'max:99999'],
            'failure_cause'        => ['nullable', 'string', 'max:2000'],
        ];
    }
}
