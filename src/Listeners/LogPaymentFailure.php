<?php

namespace NmDigitalHub\SumitPayment\Listeners;

use NmDigitalHub\SumitPayment\Events\PaymentFailed;
use Illuminate\Support\Facades\Log;

class LogPaymentFailure
{
    /**
     * Handle the event.
     */
    public function handle(PaymentFailed $event): void
    {
        Log::error('Payment failed', [
            'order_id' => $event->orderData['order_id'] ?? null,
            'amount' => $event->orderData['total'] ?? null,
            'error' => $event->errorMessage,
        ]);
    }
}
