<?php

namespace App\Filament\Resources\PaymentTokenResource\Pages;

use App\Filament\Resources\PaymentTokenResource;
use Filament\Resources\Pages\ListRecords;

class ListPaymentTokens extends ListRecords
{
    protected static string $resource = PaymentTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
