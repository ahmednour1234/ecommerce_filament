<?php

namespace App\Filament\Components;

use Illuminate\View\Component;

class SignatureBlock extends Component
{
    public function __construct(
        public ?string $name = null,
        public ?string $title = null,
        public ?string $date = null,
        public ?string $signatureImage = null,
        public string $label = 'Signature'
    ) {}

    public function render()
    {
        return view('filament.components.signature-block');
    }
}

