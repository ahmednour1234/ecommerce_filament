# Arabic Fonts for PDF Export

This directory should contain Arabic fonts for proper PDF rendering.

## Required Fonts

### Cairo Font (Recommended for Arabic)
Download from: https://fonts.google.com/specimen/Cairo

Required files:
- `Cairo-Regular.ttf`
- `Cairo-Bold.ttf`

### DejaVu Sans (Fallback)
These should already be available in dompdf, but you can add them here if needed:
- `DejaVuSans.ttf`
- `DejaVuSans-Bold.ttf`

## Installation

1. Download Cairo font from Google Fonts
2. Extract the TTF files
3. Copy `Cairo-Regular.ttf` and `Cairo-Bold.ttf` to this directory

## Alternative: Use Tajawal Font

If you prefer Tajawal (already used in voucher printing):
- Download from: https://fonts.google.com/specimen/Tajawal
- Copy `Tajawal-Regular.ttf` and `Tajawal-Bold.ttf` to this directory
- Update `PdfExport.php` to use 'Tajawal' instead of 'Cairo'
