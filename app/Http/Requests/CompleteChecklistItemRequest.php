<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CompleteChecklistItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canExecuteWorkOrders();
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'checklist_item_id' => ['required', 'integer', 'exists:work_order_checklist_items,id'],
        ];
    }
}
