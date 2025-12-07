# File Upload System

This project includes a comprehensive file upload system with a reusable trait and beautiful Filament components.

## Features

- ✅ **HasFileUploads Trait**: Reusable trait for handling file uploads in models
- ✅ **Custom FileUpload Component**: Beautiful Filament component with image editor
- ✅ **Support for Any File Type**: Images, documents, and any file type
- ✅ **Automatic File Management**: Upload, delete, and URL generation
- ✅ **Image Optimization**: Automatic resizing and cropping
- ✅ **Beautiful UI**: Modern design with preview, download, and edit capabilities

## Usage

### 1. Using the HasFileUploads Trait

Add the trait to your model:

```php
use App\Traits\HasFileUploads;

class Theme extends Model
{
    use HasFileUploads;
    
    // Your model code...
}
```

#### Available Methods

```php
// Upload any file
$path = $model->uploadFile($file, 'uploads', 'public');

// Upload image with optional resizing
$path = $model->uploadImage($file, 'images', 1920, 1080, 'public');

// Delete a file
$model->deleteFile($path, 'public');

// Get file URL
$url = $model->getFileUrl($path, 'public');

// Validate file type
$isValid = $model->validateFileType($file, ['image/jpeg', 'image/png']);

// Validate file size (in KB)
$isValid = $model->validateFileSize($file, 2048); // 2MB

// Get file info
$info = $model->getFileInfo($file);
// Returns: ['name', 'extension', 'mime_type', 'size', 'size_human']
```

### 2. Using the Custom FileUpload Component

#### For Images

```php
use App\Filament\Forms\Components\FileUpload;

FileUpload::image('logo', 'images')
    ->label('Logo')
    ->helperText('Upload your logo (max 5MB)')
    ->imagePreviewHeight('300')
    ->imageEditor()
    ->imageEditorAspectRatios([
        null,
        '16:9',
        '4:3',
        '1:1',
    ])
```

#### For Documents

```php
FileUpload::document('document', 'documents')
    ->label('Document')
    ->helperText('Upload PDF, Word, or Excel files (max 10MB)')
```

#### For Any File Type

```php
FileUpload::any('file', 'files')
    ->label('File')
    ->helperText('Upload any file type (max 20MB)')
```

#### Basic Usage

```php
FileUpload::make('attachment')
    ->label('Attachment')
    ->directory('uploads')
    ->maxSize(10240) // 10MB
    ->acceptedFileTypes(['image/*', 'application/pdf'])
```

### 3. Example: Theme Resource

```php
use App\Filament\Forms\Components\FileUpload;

public static function form(Form $form): Form
{
    return $form->schema([
        FileUpload::image('logo_light', 'themes/logos')
            ->label('Light Mode Logo')
            ->helperText('Logo for light backgrounds')
            ->imagePreviewHeight('200')
            ->imageEditor(),
        
        FileUpload::image('logo_dark', 'themes/logos')
            ->label('Dark Mode Logo')
            ->helperText('Logo for dark backgrounds')
            ->imagePreviewHeight('200')
            ->imageEditor(),
    ]);
}
```

## Component Features

### Image Upload Features
- ✅ Built-in image editor
- ✅ Multiple aspect ratios (16:9, 4:3, 1:1, free)
- ✅ Automatic image resizing
- ✅ Image preview
- ✅ Crop and edit before upload
- ✅ Download uploaded images
- ✅ Open images in new tab

### File Upload Features
- ✅ Drag and drop support
- ✅ Multiple file types support
- ✅ File size validation
- ✅ File type validation
- ✅ Progress indicator
- ✅ Preview for images
- ✅ Download capability
- ✅ Automatic file cleanup on delete

## Configuration

### File Storage

Files are stored in `storage/app/public` by default. Make sure to:

1. Create a symbolic link:
```bash
php artisan storage:link
```

2. Configure in `config/filesystems.php`:
```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
],
```

### File Size Limits

- **Images**: 5MB default
- **Documents**: 10MB default
- **Any File**: 20MB default

You can customize these in the component:

```php
->maxSize(10240) // 10MB in KB
```

### Accepted File Types

```php
// Images only
->acceptedFileTypes(['image/*'])

// Documents
->acceptedFileTypes([
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
])

// Any file type
->acceptedFileTypes(null)
```

## Best Practices

1. **Use appropriate directories**: Organize files by type
   - `images/` for images
   - `documents/` for documents
   - `uploads/` for general files

2. **Set reasonable size limits**: Prevent server overload

3. **Validate file types**: Only accept necessary file types

4. **Clean up old files**: Use the trait's `deleteFile()` method when deleting records

5. **Use image optimization**: Enable image resizing for better performance

## Example: Complete Model with File Uploads

```php
<?php

namespace App\Models;

use App\Traits\HasFileUploads;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFileUploads;

    protected $fillable = [
        'name',
        'image',
        'document',
    ];

    // Accessor for image URL
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? $this->getFileUrl($this->image) : null;
    }

    // Accessor for document URL
    public function getDocumentUrlAttribute(): ?string
    {
        return $this->document ? $this->getFileUrl($this->document) : null;
    }

    // Cleanup on delete
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($product) {
            $product->deleteFile($product->image);
            $product->deleteFile($product->document);
        });
    }
}
```

## Troubleshooting

### Files not displaying
- Check if `storage:link` is created
- Verify file permissions
- Check `APP_URL` in `.env`

### Upload fails
- Check file size limits
- Verify disk configuration
- Check PHP upload limits in `php.ini`

### Image editor not working
- Ensure image file types are accepted
- Check browser console for errors
- Verify Filament version compatibility

