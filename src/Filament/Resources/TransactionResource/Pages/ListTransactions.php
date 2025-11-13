<?php

namespace NmDigitalhub\WooPaymentGatewayAdmin\Filament\Resources\TransactionResource\Pages;

use NmDigitalhub\WooPaymentGatewayAdmin\Filament\Resources\TransactionResource;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
