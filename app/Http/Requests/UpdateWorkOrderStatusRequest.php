<?php

namespace App\Http\Requests;

use App\Enums\WorkOrderStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canManageWorkOrders();
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(WorkOrderStatus::class)],
        ];
    }
}
