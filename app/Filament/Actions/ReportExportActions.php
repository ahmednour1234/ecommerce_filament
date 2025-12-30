<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;

/**
 * Unified Report Export Actions
 * Reusable export action group for all reports
 */
class ReportExportActions
{
    /**
     * Get export actions group
     *
     * @param callable $getPrintUrl Callback that returns print URL
     * @param callable $getPdfExport Callback that handles PDF export
     * @param callable $getExcelExport Callback that handles Excel export
     * @param array $options Additional options
     * @return ActionGroup
     */
    public static function group(
        callable $getPrintUrl,
        callable $getPdfExport,
        callable $getExcelExport,
        array $options = []
    ): ActionGroup {
        $printLabel = $options['printLabel'] ?? trans('reports.export.print', 'Print');
        $pdfLabel = $options['pdfLabel'] ?? trans('reports.export.pdf', 'Export PDF');
        $excelLabel = $options['excelLabel'] ?? trans('reports.export.excel', 'Export Excel');

        return ActionGroup::make([
            Action::make('print')
                ->label($printLabel)
                ->icon('heroicon-o-printer')
                ->url($getPrintUrl)
                ->openUrlInNewTab()
                ->color('gray'),

            Action::make('export_pdf')
                ->label($pdfLabel)
                ->icon('heroicon-o-document-arrow-down')
                ->action($getPdfExport)
                ->color('danger'),

            Action::make('export_excel')
                ->label($excelLabel)
                ->icon('heroicon-o-arrow-down-tray')
                ->action($getExcelExport)
                ->color('success'),
        ])
        ->label(trans_dash('reports.export.title', 'Export'))
        ->icon('heroicon-o-arrow-down-tray')
        ->color('primary')
        ->button();
    }

    /**
     * Get individual export actions (not grouped)
     *
     * @param callable $getPrintUrl
     * @param callable $getPdfExport
     * @param callable $getExcelExport
     * @param array $options
     * @return array
     */
    public static function actions(
        callable $getPrintUrl,
        callable $getPdfExport,
        callable $getExcelExport,
        array $options = []
    ): array {
        $printLabel = $options['printLabel'] ?? trans_dash('reports.export.print', 'Print');
        $pdfLabel = $options['pdfLabel'] ?? trans_dash('reports.export.pdf', 'Export PDF');
        $excelLabel = $options['excelLabel'] ?? trans_dash('reports.export.excel', 'Export Excel');

        return [
            Action::make('print')
                ->label($printLabel)
                ->icon('heroicon-o-printer')
                ->url($getPrintUrl)
                ->openUrlInNewTab()
                ->color('gray'),

            Action::make('export_pdf')
                ->label($pdfLabel)
                ->icon('heroicon-o-document-arrow-down')
                ->action($getPdfExport)
                ->color('danger'),

            Action::make('export_excel')
                ->label($excelLabel)
                ->icon('heroicon-o-arrow-down-tray')
                ->action($getExcelExport)
                ->color('success'),
        ];
    }
}

