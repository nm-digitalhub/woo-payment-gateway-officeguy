<?php

namespace NmDigitalhub\WooPaymentGatewayAdmin\Filament\Resources\PaymentTokenResource\Pages;

use NmDigitalhub\WooPaymentGatewayAdmin\Filament\Resources\PaymentTokenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentToken extends EditRecord
{
    protected static string $resource = PaymentTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
