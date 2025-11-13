<?php

namespace NmDigitalHub\SumitPayment\Filament\Resources\PaymentTransactionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use NmDigitalHub\SumitPayment\Filament\Resources\PaymentTransactionResource;

class ListPaymentTransactions extends ListRecords
{
    protected static string $resource = PaymentTransactionResource::class;
}
