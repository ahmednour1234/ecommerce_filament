<?php

namespace App\Services\Accounting;

use App\Models\Accounting\Journal;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Rap2hpoutre\FastExcel\FastExcel;

class JournalPrintService
{
    public function html(Journal $journal, Collection $signatures): string
    {
        return View::make('print.journals.show', [
            'journal' => $journal,
            'signatures' => $signatures,
        ])->render();
    }

    public function pdf(Journal $journal, Collection $signatures)
    {
        $isRtl = app()->getLocale() === 'ar';
        
        return Pdf::loadView('print.journals.show', [
            'journal' => $journal,
            'signatures' => $signatures,
        ])
        ->setPaper('a4')
        ->setOption('enable-local-file-access', true)
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true)
        ->setOption('defaultFont', 'DejaVu Sans')
        ->setOption('fontDir', [
            public_path('fonts'),
            resource_path('fonts'),
        ]);
    }

    public function excelDownload(Journal $journal, Collection $signatures)
    {
        // مثال: تصدير بيانات السند/القيود المرتبطة (عدّل حسب بياناتك)
        $rows = $journal->journalEntries()
            ->with('lines.account') // عدّل العلاقات عندك
            ->get()
            ->flatMap(fn ($entry) => $entry->lines->map(function ($line) use ($entry) {
                return [
                    'Entry No' => $entry->id,
                    'Date'     => optional($entry->date)->format('Y-m-d'),
                    'Account'  => $line->account?->name,
                    'Debit'    => $line->debit,
                    'Credit'   => $line->credit,
                    'Note'     => $line->description,
                ];
            }));

        return (new FastExcel($rows))
            ->download("journal-{$journal->code}.xlsx");
    }
}
