<?php

namespace App\Filament\Resources\Packages\PackageResource\Pages;

use App\Filament\Resources\Packages\PackageResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Route;

class ViewPackage extends ViewRecord
{
    protected static string $resource = PackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(tr('common.edit', [], null, 'dashboard'))
                ->visible(fn () => PackageResource::canEdit($this->record)),
            Actions\Action::make('export_pdf')
                ->label(tr('buttons.export_pdf', [], null, 'packages'))
                ->icon('heroicon-o-document-arrow-down')
                ->action('exportPdf')
                ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('packages.export_pdf') ?? false),
        ];
    }

    public function exportPdf()
    {
        $package = $this->record;
        $package->load(['country', 'packageDetails.profession', 'packageDetails.country']);

        $pdf = Pdf::loadView('pdf.package', [
            'package' => $package,
        ]);

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('enable-local-file-access', true);
        $pdf->setOption('defaultFont', 'Cairo');
        $pdf->setOption('fontDir', [
            public_path('fonts'),
            resource_path('fonts'),
            storage_path('fonts'),
        ]);
        $pdf->setOption('fontCache', storage_path('fonts'));

        $filename = 'package_' . $package->id . '_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
