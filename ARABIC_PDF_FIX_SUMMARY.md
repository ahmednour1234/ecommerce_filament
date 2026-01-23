# Arabic PDF Rendering Fix - Implementation Summary

## Detection Results

**PDF Engine:** DOMPDF (barryvdh/laravel-dompdf)
**Export Class:** `App\Exports\PdfExport`
**View Template:** `resources/views/exports/table-pdf.blade.php`

## Changes Implemented

### 1. Updated `app/Exports/PdfExport.php`

**Changes:**
- Added automatic RTL detection using `detectRtl()` method that checks for Arabic characters (U+0600-U+06FF)
- Configured DOMPDF with Arabic font support:
  - `defaultFont`: Uses 'Cairo' for Arabic content, 'DejaVu Sans' for non-Arabic
  - `fontDir`: Added multiple font directories (public/fonts, resources/fonts, storage/fonts)
  - `fontCache`: Set to storage/fonts for font caching
  - `enable-local-file-access`: Enabled for local font file access
  - `isHtml5ParserEnabled`: Already enabled
  - `isRemoteEnabled`: Already enabled

**Key Methods:**
- `detectRtl()`: Detects Arabic characters in title and headers to determine if RTL is needed
- Both `download()` and `stream()` methods now use the same Arabic font configuration

### 2. Updated `resources/views/exports/table-pdf.blade.php`

**Changes:**
- Added automatic Arabic detection in PHP section
- Added `@font-face` declarations for:
  - DejaVu Sans (Regular & Bold) - fallback font
  - Cairo (Regular & Bold) - Arabic font
- Updated font-family to use Cairo for Arabic content
- Added `unicode-bidi: embed` for proper bidirectional text rendering
- Added `direction` CSS property to all text elements (body, th, td, h1)
- Enhanced meta tags with explicit UTF-8 charset declaration

**RTL Features:**
- Automatic detection of Arabic text
- Proper text alignment (right for RTL, left for LTR)
- Unicode bidirectional embedding for correct character shaping

### 3. Created Font Directory Structure

- Created `public/fonts/` directory
- Added `public/fonts/README.md` with font installation instructions

### 4. Added Diagnostic Route

**Route:** `/admin/exports/test-arabic-pdf`
**Controller Method:** `ExportController::testArabicPdf()`

**Test Data:**
- Arabic title: "تقرير كشف حساب الفرع - اختبار"
- Arabic headers: ['التاريخ', 'النوع', 'المبلغ', 'الرصيد']
- Sample Arabic data rows

### 5. Helper Script

Created `download_cairo_font.php` to automatically download Cairo fonts from Google Fonts.

## Next Steps (Required)

### Step 1: Download Cairo Font

**Option A: Use the helper script**
```bash
php download_cairo_font.php
```

**Option B: Manual download**
1. Visit: https://fonts.google.com/specimen/Cairo
2. Download the font family
3. Extract the TTF files
4. Copy these files to `public/fonts/`:
   - `Cairo-Regular.ttf`
   - `Cairo-Bold.ttf`

### Step 2: Test the Fix

1. **Test diagnostic route:**
   ```
   http://your-domain/admin/exports/test-arabic-pdf
   ```
   This will generate a test PDF with Arabic text to verify:
   - Arabic characters are connected (proper shaping)
   - RTL direction is correct
   - Font rendering is clear

2. **Test Branch Statement export:**
   - Go to Branch Statement page
   - Click "Export PDF"
   - Verify Arabic text appears correctly

### Step 3: Verify Font Files

Ensure these files exist:
- `public/fonts/Cairo-Regular.ttf`
- `public/fonts/Cairo-Bold.ttf`

If DejaVu Sans is not available in dompdf's default location, you may also need:
- `public/fonts/DejaVuSans.ttf`
- `public/fonts/DejaVuSans-Bold.ttf`

## Technical Details

### Arabic Character Detection
- Uses Unicode range `\x{0600}-\x{06FF}` to detect Arabic characters
- Automatically switches to RTL and Cairo font when Arabic is detected

### Font Loading Priority
1. Cairo (for Arabic content)
2. DejaVu Sans (fallback)
3. Arial (system fallback)
4. sans-serif (generic fallback)

### DOMPDF Configuration
- HTML5 parser enabled for better Unicode support
- Remote assets enabled for external resources
- Local file access enabled for font files
- Font directories configured for multiple locations

## Troubleshooting

### If Arabic still appears disconnected:
1. Verify font files exist in `public/fonts/`
2. Check file permissions (should be readable)
3. Clear dompdf font cache: delete `storage/fonts/` if it exists
4. Verify `public_path('fonts/...')` resolves correctly

### If RTL is wrong:
1. Check that `direction: rtl` is applied in CSS
2. Verify `unicode-bidi: embed` is present
3. Check that HTML `dir` attribute is set to "rtl"

### If fonts don't load:
1. Check `enable-local-file-access` is set to `true`
2. Verify font file paths are correct
3. Check dompdf logs for font loading errors

## Files Modified

1. `app/Exports/PdfExport.php` - Added Arabic font configuration
2. `resources/views/exports/table-pdf.blade.php` - Added Arabic font support and RTL styling
3. `app/Http/Controllers/ExportController.php` - Added diagnostic route method
4. `routes/web.php` - Added diagnostic route
5. `public/fonts/README.md` - Font installation guide
6. `download_cairo_font.php` - Font download helper script

## Cleanup (After Testing)

Once Arabic rendering is confirmed working, you can:
- Remove the diagnostic route (optional, but recommended for production)
- Remove `download_cairo_font.php` script (optional)

The diagnostic route can be kept for future testing if needed.
