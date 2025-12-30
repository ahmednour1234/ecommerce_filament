<?php

namespace App\Http\Requests\Accounting;

use App\Enums\Accounting\JournalEntryStatus;
use App\Models\Accounting\Account;
use App\Models\Accounting\Period;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'fiscal_year_id' => ['nullable', 'exists:fiscal_years,id'],
            'period_id' => ['nullable', 'exists:periods,id'],
            'status' => ['nullable', Rule::enum(JournalEntryStatus::class)],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.account_id' => ['required', 'exists:accounts,id'],
            'lines.*.description' => ['nullable', 'string'],
            'lines.*.debit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.credit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.branch_id' => ['required', 'exists:branches,id'],
            'lines.*.cost_center_id' => ['nullable', 'exists:cost_centers,id'],
            'lines.*.project_id' => ['nullable', 'exists:projects,id'],
            'lines.*.currency_id' => ['nullable', 'exists:currencies,id'],
            'lines.*.exchange_rate' => ['nullable', 'numeric', 'min:0'],
            'lines.*.amount' => ['nullable', 'numeric'],
            'lines.*.base_amount' => ['nullable', 'numeric'],
            'lines.*.reference' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate balance
            $lines = $this->input('lines', []);
            $totalDebits = 0;
            $totalCredits = 0;

            foreach ($lines as $index => $line) {
                $debit = (float) ($line['debit'] ?? 0);
                $credit = (float) ($line['credit'] ?? 0);
                $baseAmount = (float) ($line['base_amount'] ?? 0);

                // Use base_amount if available, otherwise use debit/credit
                if ($baseAmount > 0) {
                    if ($debit > 0) {
                        $totalDebits += $baseAmount;
                    } else {
                        $totalCredits += $baseAmount;
                    }
                } else {
                    $totalDebits += $debit;
                    $totalCredits += $credit;
                }

                // Validate each line has either debit or credit, not both
                if ($debit > 0 && $credit > 0) {
                    $validator->errors()->add(
                        "lines.{$index}.debit",
                        trans_dash('accounting.validation.debit_or_credit', 'Line must have either debit or credit, not both.')
                    );
                }

                if ($debit == 0 && $credit == 0) {
                    $validator->errors()->add(
                        "lines.{$index}.debit",
                        trans_dash('accounting.validation.amount_required', 'Line must have either debit or credit amount.')
                    );
                }

                // Validate account is active and allows manual entry
                if (isset($line['account_id'])) {
                    $account = Account::find($line['account_id']);
                    if ($account && (!$account->is_active || !$account->allow_manual_entry)) {
                        $validator->errors()->add(
                            "lines.{$index}.account_id",
                            trans_dash('accounting.validation.account_not_allowed', 'Account is not active or does not allow manual entry.')
                        );
                    }
                }
            }

            // Validate balance
            $difference = abs($totalDebits - $totalCredits);
            if ($difference >= 0.01) {
                $validator->errors()->add(
                    'lines',
                    trans_dash('accounting.validation.entries_not_balanced', 'Journal entry is not balanced. Difference: :difference', [
                        'difference' => number_format($difference, 2),
                    ])
                );
            }

            // Validate period is not closed
            if ($this->input('period_id')) {
                $period = Period::find($this->input('period_id'));
                if ($period && $period->is_closed) {
                    $validator->errors()->add(
                        'period_id',
                        trans_dash('accounting.validation.period_closed', 'Cannot post to a closed period.')
                    );
                }
            }
        });
    }
}
