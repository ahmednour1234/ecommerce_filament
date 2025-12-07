<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\FileUpload as BaseFileUpload;
use Illuminate\Support\Facades\Storage;

class FileUpload extends BaseFileUpload
{
    public static function make(string $name): static
    {
        $component = parent::make($name);
        
        return $component
            ->disk('public')
            ->directory('uploads')
            ->visibility('public')
            ->acceptedFileTypes(['image/*', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
            ->maxSize(10240) // 10MB
            ->imageEditor()
            ->imageEditorAspectRatios([
                null,
                '16:9',
                '4:3',
                '1:1',
            ])
            ->imageCropAspectRatio('16:9')
            ->imageResizeTargetWidth('1920')
            ->imageResizeTargetHeight('1080')
            ->imageResizeMode('cover')
            ->imagePreviewHeight('250')
            ->loadingIndicatorPosition('left')
            ->removeUploadedFileButtonPosition('right')
            ->uploadButtonPosition('left')
            ->uploadProgressIndicatorPosition('left')
            ->panelAspectRatio('2:1')
            ->panelLayout('integrated')
            ->openable()
            ->downloadable()
            ->previewable()
            ->removeUploadedFileUsing(function ($file) {
                if ($file) {
                    Storage::disk('public')->delete($file);
                }
            });
    }

    /**
     * Configure for image uploads only
     */
    public static function image(string $name, string $directory = 'images'): static
    {
        return static::make($name)
            ->directory($directory)
            ->acceptedFileTypes(['image/*'])
            ->image()
            ->imageEditor()
            ->imageEditorAspectRatios([
                null,
                '16:9',
                '4:3',
                '1:1',
            ])
            ->imagePreviewHeight('300')
            ->maxSize(5120) // 5MB for images
            ->openable()
            ->downloadable()
            ->previewable()
            ->imageResizeTargetWidth('1920')
            ->imageResizeTargetHeight('1080')
            ->imageResizeMode('cover');
    }

    /**
     * Configure for document uploads
     */
    public static function document(string $name, string $directory = 'documents'): static
    {
        return static::make($name)
            ->directory($directory)
            ->acceptedFileTypes([
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->maxSize(10240) // 10MB
            ->downloadable()
            ->previewable(false);
    }

    /**
     * Configure for any file type
     */
    public static function any(string $name, string $directory = 'files'): static
    {
        return static::make($name)
            ->directory($directory)
            ->acceptedFileTypes(null) // Accept all
            ->maxSize(20480) // 20MB
            ->downloadable()
            ->previewable();
    }
}

