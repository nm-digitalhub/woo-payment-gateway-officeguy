<?php

namespace NmDigitalHub\SumitPayment\Services;

use NmDigitalHub\SumitPayment\Models\PaymentToken;
use Illuminate\Support\Facades\Config;

class TokenService
{
    protected ApiService $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Create a payment token from card details
     */
    public function createToken(array $cardData, ?int $userId = null): array
    {
        $request = $this->buildTokenRequest($cardData);
        
        $response = $this->apiService->post($request, '/creditguy/gateway/transaction/', false);

        if ($response === null) {
            return [
                'success' => false,
                'error' => 'No response from payment gateway',
            ];
        }

        if ($response['Status'] == 0 && ($response['Data']['Success'] ?? false)) {
            $token = $this->saveTokenFromResponse($response, $userId);
            
            return [
                'success' => true,
                'token' => $token,
            ];
        }

        return [
            'success' => false,
            'error' => $response['Data']['ResultDescription'] ?? $response['UserErrorMessage'] ?? 'Token creation failed',
        ];
    }

    /**
     * Build token creation request
     */
    protected function buildTokenRequest(array $cardData): array
    {
        $request = [
            'ParamJ' => Config::get('sumit-payment.payment.token_param', 'J2'),
            'Amount' => 1,
            'Credentials' => [
                'CompanyID' => Config::get('sumit-payment.credentials.company_id'),
                'APIKey' => Config::get('sumit-payment.credentials.api_key'),
            ],
        ];

        $pciMode = Config::get('sumit-payment.payment.pci_mode');

        if ($pciMode === 'yes') {
            $request['CardNumber'] = $cardData['card_number'] ?? '';
            $request['CVV'] = $cardData['cvv'] ?? '';
            $request['CitizenID'] = $cardData['citizen_id'] ?? '';
            
            $expMonth = (int) ($cardData['exp_month'] ?? 0);
            $request['ExpirationMonth'] = $expMonth < 10 ? '0' . $expMonth : (string) $expMonth;
            $request['ExpirationYear'] = (string) ($cardData['exp_year'] ?? '');
        } else {
            $request['SingleUseToken'] = $cardData['single_use_token'] ?? '';
        }

        return $request;
    }

    /**
     * Save token from API response
     */
    protected function saveTokenFromResponse(array $response, ?int $userId = null): PaymentToken
    {
        $responseData = $response['Data'];
        
        return PaymentToken::create([
            'user_id' => $userId,
            'token' => $responseData['CardToken'] ?? '',
            'card_type' => 'card',
            'card_last4' => substr($responseData['CardPattern'] ?? '', -4),
            'card_brand' => $responseData['Brand'] ?? null,
            'exp_month' => $responseData['ExpirationMonth'] ?? null,
            'exp_year' => $responseData['ExpirationYear'] ?? null,
            'citizen_id' => $responseData['CitizenID'] ?? null,
            'is_default' => false,
        ]);
    }

    /**
     * Get token by ID
     */
    public function getToken(int $tokenId, ?int $userId = null): ?PaymentToken
    {
        $query = PaymentToken::where('id', $tokenId);
        
        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        return $query->first();
    }

    /**
     * Get all tokens for a user
     */
    public function getUserTokens(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return PaymentToken::where('user_id', $userId)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get default token for a user
     */
    public function getDefaultToken(int $userId): ?PaymentToken
    {
        return PaymentToken::where('user_id', $userId)
            ->where('is_default', true)
            ->first();
    }

    /**
     * Set token as default for user
     */
    public function setDefaultToken(int $tokenId, int $userId): bool
    {
        // Remove default from all other tokens
        PaymentToken::where('user_id', $userId)
            ->update(['is_default' => false]);

        // Set this token as default
        $token = $this->getToken($tokenId, $userId);
        
        if ($token) {
            $token->is_default = true;
            return $token->save();
        }

        return false;
    }

    /**
     * Delete a token
     */
    public function deleteToken(int $tokenId, ?int $userId = null): bool
    {
        $query = PaymentToken::where('id', $tokenId);
        
        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        return $query->delete() > 0;
    }

    /**
     * Validate card data
     */
    public function validateCardData(array $cardData): array
    {
        $errors = [];

        $pciMode = Config::get('sumit-payment.payment.pci_mode');

        if ($pciMode === 'yes') {
            if (empty($cardData['card_number']) || !ctype_digit($cardData['card_number'])) {
                $errors[] = 'Card number is invalid';
            }

            if (empty($cardData['cvv']) || !ctype_digit($cardData['cvv'])) {
                $errors[] = 'Card security code is invalid';
            }

            $currentYear = (int) date('Y');
            $expMonth = (int) ($cardData['exp_month'] ?? 0);
            $expYear = (int) ($cardData['exp_year'] ?? 0);

            if ($expMonth < 1 || $expMonth > 12) {
                $errors[] = 'Expiration month is invalid';
            }

            if ($expYear < $currentYear || $expYear > $currentYear + 20) {
                $errors[] = 'Expiration year is invalid';
            }
        } elseif ($pciMode === 'no') {
            if (empty($cardData['single_use_token'])) {
                $errors[] = 'Payment token is required';
            }
        }

        return $errors;
    }

    /**
     * Get payment method array from token for API requests
     */
    public function getPaymentMethodFromToken(PaymentToken $token): array
    {
        return [
            'Token' => $token->token,
        ];
    }
}
