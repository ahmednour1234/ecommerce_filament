<?php

namespace App\Filament\Resources\ServiceTransfer\ServiceTransferResource\Pages;

use App\Filament\Resources\ServiceTransfer\ServiceTransferResource;
use App\Filament\Pages\BaseCreateRecord;
use Filament\Forms\Form;

class CreateServiceTransfer extends BaseCreateRecord
{
    protected static string $resource = ServiceTransferResource::class;

    public function form(Form $form): Form
    {
        $form = parent::form($form);
        
        $schema = $form->getSchema();
        $filteredSchema = [];
        
        foreach ($schema as $component) {
            if ($component instanceof \Filament\Forms\Components\Section && $component->getLabel() === 'التسعير') {
                continue;
            }
            $filteredSchema[] = $component;
        }
        
        return $form->schema($filteredSchema);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
