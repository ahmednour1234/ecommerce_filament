<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasFileUploads
{
    /**
     * Upload a file and return the path
     */
    public function uploadFile(UploadedFile $file, string $folder = 'uploads', ?string $disk = 'public'): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        $path = $file->storeAs($folder, $filename, $disk);
        
        return $path;
    }

    /**
     * Upload an image with optional resizing
     */
    public function uploadImage(
        UploadedFile $file,
        string $folder = 'images',
        ?int $maxWidth = null,
        ?int $maxHeight = null,
        ?string $disk = 'public'
    ): string {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        $path = $file->storeAs($folder, $filename, $disk);
        
        // If image manipulation is needed, you can add it here
        // For now, we'll just store the file
        
        return $path;
    }

    /**
     * Delete a file
     */
    public function deleteFile(?string $path, ?string $disk = 'public'): bool
    {
        if (!$path) {
            return false;
        }
        
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }
        
        return false;
    }

    /**
     * Get file URL
     */
    public function getFileUrl(?string $path, ?string $disk = 'public'): ?string
    {
        if (!$path) {
            return null;
        }
        
        // Use direct URL format for public access
        if ($disk === 'public') {
            $baseUrl = rtrim(config('app.url'), '/');
            return $baseUrl . '/storage/' . ltrim($path, '/');
        }
        
        return Storage::disk($disk)->url($path);
    }

    /**
     * Validate file type
     */
    public function validateFileType(UploadedFile $file, array $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']): bool
    {
        return in_array($file->getMimeType(), $allowedTypes);
    }

    /**
     * Validate file size (in KB)
     */
    public function validateFileSize(UploadedFile $file, int $maxSizeKB = 2048): bool
    {
        return $file->getSize() <= ($maxSizeKB * 1024);
    }

    /**
     * Get file info
     */
    public function getFileInfo(UploadedFile $file): array
    {
        return [
            'name' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'size_human' => $this->formatBytes($file->getSize()),
        ];
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

