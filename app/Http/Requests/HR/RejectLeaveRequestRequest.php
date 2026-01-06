<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class RejectLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('hr.leave_requests.reject') ?? false;
    }

    public function rules(): array
    {
        return [
            'manager_note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

