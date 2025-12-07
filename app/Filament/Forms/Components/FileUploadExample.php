<?php

namespace App\Filament\Forms\Components;

/**
 * Example usage of the FileUpload component
 * 
 * This file demonstrates how to use the custom FileUpload component
 * in your Filament resources.
 */

class FileUploadExample
{
    /**
     * Example 1: Image upload with editor
     */
    public static function imageExample()
    {
        return \App\Filament\Forms\Components\FileUpload::image('avatar', 'avatars')
            ->label('Profile Picture')
            ->helperText('Upload your profile picture (max 5MB)')
            ->imagePreviewHeight('300')
            ->imageEditor()
            ->imageEditorAspectRatios([
                null,      // Free aspect ratio
                '16:9',    // Wide
                '4:3',     // Standard
                '1:1',     // Square
            ])
            ->required();
    }

    /**
     * Example 2: Document upload
     */
    public static function documentExample()
    {
        return \App\Filament\Forms\Components\FileUpload::document('contract', 'documents')
            ->label('Contract Document')
            ->helperText('Upload PDF, Word, or Excel files (max 10MB)')
            ->downloadable()
            ->required();
    }

    /**
     * Example 3: Any file type
     */
    public static function anyFileExample()
    {
        return \App\Filament\Forms\Components\FileUpload::any('attachment', 'files')
            ->label('Attachment')
            ->helperText('Upload any file type (max 20MB)')
            ->downloadable()
            ->previewable();
    }

    /**
     * Example 4: Multiple images
     */
    public static function multipleImagesExample()
    {
        return \App\Filament\Forms\Components\FileUpload::image('gallery', 'gallery')
            ->label('Image Gallery')
            ->multiple()
            ->maxFiles(10)
            ->helperText('Upload up to 10 images')
            ->imagePreviewHeight('200')
            ->imageEditor();
    }

    /**
     * Example 5: Custom configuration
     */
    public static function customExample()
    {
        return \App\Filament\Forms\Components\FileUpload::make('custom_file')
            ->label('Custom File')
            ->directory('custom')
            ->disk('public')
            ->acceptedFileTypes(['image/*', 'application/pdf'])
            ->maxSize(5120) // 5MB
            ->imageEditor()
            ->imagePreviewHeight('250')
            ->openable()
            ->downloadable()
            ->previewable()
            ->helperText('Custom file upload with specific settings');
    }
}

