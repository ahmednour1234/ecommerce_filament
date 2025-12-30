<?php

namespace App\Http\Requests\Accounting;

use App\Enums\Accounting\JournalEntryStatus;
use App\Models\Accounting\JournalEntry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJournalEntryRequest extends StoreJournalEntryRequest
{
    public function authorize(): bool
    {
        $entry = $this->route('record');
        
        if ($entry instanceof JournalEntry) {
            if ($entry->is_posted) {
                return false; // Posted entries cannot be edited
            }
            
            $status = JournalEntryStatus::from($entry->status ?? JournalEntryStatus::DRAFT->value);
            if (!$status->canBeEdited()) {
                return false;
            }
        }
        
        return auth()->user()?->can('journal_entries.update') ?? false;
    }

    public function rules(): array
    {
        $rules = parent::rules();
        
        // Update entry_number rule to ignore current record
        $entry = $this->route('record');
        if ($entry instanceof JournalEntry) {
            $rules['entry_number'] = [
                'required',
                'string',
                'max:50',
                Rule::unique('journal_entries', 'entry_number')->ignore($entry->id),
            ];
        }
        
        return $rules;
    }

    public function withValidator($validator): void
    {
        parent::withValidator($validator);
        
        $validator->after(function ($validator) {
            $entry = $this->route('record');
            
            if ($entry instanceof JournalEntry) {
                if ($entry->is_posted) {
                    $validator->errors()->add(
                        'entry',
                        trans_dash('accounting.validation.cannot_edit_posted', 'Cannot edit a posted entry. Create a reversal entry instead.')
                    );
                }
            }
        });
    }
}

