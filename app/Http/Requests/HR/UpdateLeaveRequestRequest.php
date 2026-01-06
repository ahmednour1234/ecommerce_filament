<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('hr.leave_requests.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:hr_employees,id'],
            'leave_type_id' => ['required', 'exists:hr_leave_types,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:1000'],
            'attachment_path' => ['nullable', 'string', 'max:500'],
        ];
    }
}

