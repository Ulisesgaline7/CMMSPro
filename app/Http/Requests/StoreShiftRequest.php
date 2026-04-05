<?php

namespace App\Http\Requests;

use App\Enums\ShiftStatus;
use App\Enums\ShiftType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreShiftRequest extends FormRequest
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
            'user_id'    => ['required', 'exists:users,id'],
            'name'       => ['required', 'string', 'max:100'],
            'type'       => ['required', new Enum(ShiftType::class)],
            'date'       => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i'],
            'notes'      => ['nullable', 'string'],
        ];
    }
}
