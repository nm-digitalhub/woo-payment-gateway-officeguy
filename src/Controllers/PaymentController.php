<?php

namespace NmDigitalHub\SumitPayment\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use NmDigitalHub\SumitPayment\Services\PaymentService;
use NmDigitalHub\SumitPayment\Services\TokenService;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;
    protected TokenService $tokenService;

    public function __construct(PaymentService $paymentService, TokenService $tokenService)
    {
        $this->paymentService = $paymentService;
        $this->tokenService = $tokenService;
    }

    /**
     * Process payment
     */
    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'items' => 'required|array',
            'customer' => 'required|array',
            'payment_method' => 'required|array',
            'payments_count' => 'nullable|integer|min:1',
        ]);

        // Validate payment fields
        $errors = $this->paymentService->validatePaymentFields($validated['payment_method']);
        
        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'errors' => $errors,
            ], 422);
        }

        // Process payment
        $result = $this->paymentService->processPayment(
            $validated,
            $validated['payment_method'],
            $validated['payments_count'] ?? 1
        );

        return response()->json($result);
    }

    /**
     * Handle payment redirect callback
     */
    public function handleRedirect(Request $request)
    {
        $orderId = $request->query('OG-OrderID');
        $success = $request->query('Success');
        
        // Handle redirect response
        // This would typically update order status and redirect to thank you page
        
        if ($success === 'true') {
            return response()->json([
                'success' => true,
                'order_id' => $orderId,
                'message' => 'Payment completed successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'order_id' => $orderId,
            'message' => 'Payment failed',
        ], 400);
    }

    /**
     * Process refund
     */
    public function processRefund(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $result = $this->paymentService->processRefund(
            $validated['transaction_id'],
            $validated['amount']
        );

        return response()->json($result);
    }
}
