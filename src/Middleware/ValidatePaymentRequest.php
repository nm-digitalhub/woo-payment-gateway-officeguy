<?php

namespace NmDigitalHub\SumitPayment\Middleware;

use Closure;
use Illuminate\Http\Request;
use NmDigitalHub\SumitPayment\Settings\SumitPaymentSettings;

class ValidatePaymentRequest
{
    protected SumitPaymentSettings $settings;

    public function __construct(SumitPaymentSettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Validate that API credentials are configured
        if (empty($this->settings->company_id) || empty($this->settings->api_key)) {
            return response()->json([
                'success' => false,
                'error' => 'Payment gateway is not properly configured',
            ], 503);
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
