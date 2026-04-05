<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AddWorkOrderNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canExecuteWorkOrders();
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'notes' => ['required', 'string', 'max:2000'],
        ];
    }
}
