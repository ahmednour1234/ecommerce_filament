<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class ApproveLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('hr.leave_requests.approve') ?? false;
    }

    public function rules(): array
    {
        return [
            'manager_note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

