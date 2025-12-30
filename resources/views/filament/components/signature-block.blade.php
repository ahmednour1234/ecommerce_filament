@php
    $isRTL = app()->getLocale() === 'ar';
@endphp

<div class="signature-block" dir="{{ $isRTL ? 'rtl' : 'ltr' }}">
    <div class="border-t-2 border-gray-400 pt-2 mt-4">
        <div class="text-sm font-semibold mb-1">{{ $label }}</div>
        @if($name)
            <div class="text-sm">{{ trans_dash('accounting.name', 'Name') }}: {{ $name }}</div>
        @endif
        @if($title)
            <div class="text-sm">{{ trans_dash('accounting.title', 'Title') }}: {{ $title }}</div>
        @endif
        @if($date)
            <div class="text-sm">{{ trans_dash('accounting.date', 'Date') }}: {{ $date }}</div>
        @endif
        @if($signatureImage)
            <div class="mt-2">
                <img src="{{ $signatureImage }}" alt="Signature" class="max-h-16">
            </div>
        @endif
    </div>
</div>

