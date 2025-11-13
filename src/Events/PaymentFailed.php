<?php

namespace NmDigitalHub\SumitPayment\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentFailed
{
    use Dispatchable, SerializesModels;

    public array $orderData;
    public string $errorMessage;

    public function __construct(array $orderData, string $errorMessage)
    {
        $this->orderData = $orderData;
        $this->errorMessage = $errorMessage;
    }
}
