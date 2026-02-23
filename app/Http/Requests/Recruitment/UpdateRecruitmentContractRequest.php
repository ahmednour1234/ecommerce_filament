<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecruitmentContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'gregorian_request_date' => ['required', 'date'],
            'hijri_request_date' => ['nullable', 'string'],
            'visa_type' => ['required', 'in:paid,qualification,additional'],
            'visa_no' => ['required', 'string', 'max:255'],
            'visa_date' => ['nullable', 'date', 'required_if:visa_type,paid'],
            'arrival_country_id' => ['required', 'exists:countries,id'],
            'departure_country_id' => ['required', 'exists:countries,id'],
            'profession_id' => ['nullable', 'exists:professions,id'],
            'gender' => ['nullable', 'in:male,female'],
            'experience' => ['nullable', 'string', 'max:255'],
            'religion' => ['nullable', 'string', 'max:255'],
            'workplace_ar' => ['nullable', 'string', 'max:255'],
            'workplace_en' => ['nullable', 'string', 'max:255'],
            'monthly_salary' => ['nullable', 'numeric', 'min:0'],
            'musaned_contract_no' => ['nullable', 'string', 'max:255'],
            'musaned_auth_no' => ['nullable', 'string', 'max:255'],
            'musaned_contract_date' => ['nullable', 'date'],
            'direct_cost' => ['nullable', 'numeric', 'min:0'],
            'internal_ticket_cost' => ['nullable', 'numeric', 'min:0'],
            'external_cost' => ['nullable', 'numeric', 'min:0'],
            'vat_cost' => ['nullable', 'numeric', 'min:0'],
            'gov_cost' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:new,processing,contract_signed,ticket_booked,worker_received,closed,returned'],
            'notes' => ['nullable', 'string'],
            'visa_image' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'musaned_contract_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'worker_id' => ['nullable', 'exists:laborers,id'],
        ];
    }
}
