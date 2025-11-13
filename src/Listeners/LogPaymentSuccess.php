<?php

namespace NmDigitalHub\SumitPayment\Listeners;

use NmDigitalHub\SumitPayment\Events\PaymentProcessed;
use Illuminate\Support\Facades\Log;

class LogPaymentSuccess
{
    /**
     * Handle the event.
     */
    public function handle(PaymentProcessed $event): void
    {
        Log::info('Payment processed successfully', [
            'transaction_id' => $event->transaction->id,
            'order_id' => $event->transaction->order_id,
            'amount' => $event->transaction->amount,
            'currency' => $event->transaction->currency,
        ]);
    }
}
