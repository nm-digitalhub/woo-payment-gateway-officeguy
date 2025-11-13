<?php

namespace NmDigitalHub\SumitPayment\Events;

use NmDigitalHub\SumitPayment\Models\PaymentTransaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentProcessed
{
    use Dispatchable, SerializesModels;

    public PaymentTransaction $transaction;
    public array $response;

    public function __construct(PaymentTransaction $transaction, array $response)
    {
        $this->transaction = $transaction;
        $this->response = $response;
    }
}
