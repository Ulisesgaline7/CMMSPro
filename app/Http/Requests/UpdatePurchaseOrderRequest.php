<?php

namespace App\Http\Requests;

use App\Enums\PurchaseOrderPriority;
use App\Enums\PurchaseOrderStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'supplier_name' => ['required', 'string', 'max:255'],
            'supplier_contact' => ['nullable', 'string', 'max:255'],
            'work_order_id' => ['nullable', 'integer', 'exists:work_orders,id'],
            'priority' => ['required', Rule::enum(PurchaseOrderPriority::class)],
            'status' => ['required', Rule::enum(PurchaseOrderStatus::class)],
            'expected_delivery' => ['nullable', 'date'],
            'received_at' => ['nullable', 'date'],
            'currency' => ['nullable', 'string', 'size:3'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.part_number' => ['nullable', 'string', 'max:100'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit' => ['required', 'string', 'max:20'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
