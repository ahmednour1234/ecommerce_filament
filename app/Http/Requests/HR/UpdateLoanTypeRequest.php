<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoanTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('hr.loan_types.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'name_json' => ['required', 'array'],
            'name_json.ar' => ['required', 'string', 'max:255'],
            'name_json.en' => ['required', 'string', 'max:255'],
            'description_json' => ['nullable', 'array'],
            'description_json.ar' => ['nullable', 'string'],
            'description_json.en' => ['nullable', 'string'],
            'max_amount' => ['required', 'numeric', 'min:0'],
            'max_installments' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
