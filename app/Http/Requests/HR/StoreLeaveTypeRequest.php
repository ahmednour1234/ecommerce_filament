<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('hr.leave_types.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'allowed_days_per_year' => ['required', 'integer', 'min:0', 'max:365'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}

