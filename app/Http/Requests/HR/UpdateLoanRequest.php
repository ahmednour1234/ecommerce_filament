<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('hr.loans.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:hr_employees,id'],
            'loan_type_id' => ['required', 'exists:hr_loan_types,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency_id' => ['nullable', 'exists:currencies,id'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0'],
            'base_amount' => ['nullable', 'numeric', 'min:0'],
            'installments_count' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'purpose' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ];
    }
}
