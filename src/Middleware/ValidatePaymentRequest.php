<?php

namespace NmDigitalHub\SumitPayment\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ValidatePaymentRequest
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Validate that API credentials are configured
        if (empty(Config::get('sumit-payment.credentials.company_id')) || 
            empty(Config::get('sumit-payment.credentials.api_key'))) {
            return response()->json([
                'success' => false,
                'error' => 'Payment gateway is not properly configured',
            ], 500);
        }

        // Validate request has required fields
        if ($request->isMethod('post')) {
            $requiredFields = ['amount', 'currency'];
            
            foreach ($requiredFields as $field) {
                if (!$request->has($field)) {
                    return response()->json([
                        'success' => false,
                        'error' => "Missing required field: {$field}",
                    ], 422);
                }
            }
        }

        return $next($request);
    }
}
