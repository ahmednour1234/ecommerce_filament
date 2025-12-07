<?php

namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;

class StoreJournalEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('journal_entries.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'journal_id' => ['required', 'exists:journals,id'],
            'entry_number' => ['required', 'string', 'max:50', 'unique:journal_entries,entry_number'],
            'entry_date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'branch_id' => ['required', 'exists:branches,id'],
            'cost_center_id' => ['nullable', 'exists:cost_centers,id'],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.account_id' => ['required', 'exists:accounts,id'],
            'lines.*.debit' => ['required', 'numeric', 'min:0'],
            'lines.*.credit' => ['required', 'numeric', 'min:0'],
            'lines.*.description' => ['nullable', 'string'],
            'lines.*.branch_id' => ['required', 'exists:branches,id'],
            'lines.*.cost_center_id' => ['nullable', 'exists:cost_centers,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $lines = $this->input('lines', []);
            $totalDebits = collect($lines)->sum('debit');
            $totalCredits = collect($lines)->sum('credit');

            if (abs($totalDebits - $totalCredits) >= 0.01) {
                $validator->errors()->add('lines', 'Journal entry must be balanced. Total debits must equal total credits.');
            }

            // Validate each line has either debit or credit, not both
            foreach ($lines as $index => $line) {
                $debit = $line['debit'] ?? 0;
                $credit = $line['credit'] ?? 0;

                if ($debit > 0 && $credit > 0) {
                    $validator->errors()->add("lines.{$index}", 'Each line must have either a debit or credit, not both.');
                }

                if ($debit == 0 && $credit == 0) {
                    $validator->errors()->add("lines.{$index}", 'Each line must have either a debit or credit amount.');
                }
            }
        });
    }
}

