<?php

namespace NmDigitalHub\SumitPayment\Services;

use Illuminate\Support\Facades\Config;

class MarketplaceService
{
    protected ApiService $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Get vendor credentials for marketplace order
     */
    public function getVendorCredentials(int $vendorId): ?array
    {
        // This would typically fetch from database where vendor credentials are stored
        // For now, returning a structure that should be implemented
        
        // In a real implementation, you'd query a vendors table:
        // $vendor = Vendor::find($vendorId);
        // return $vendor ? ['CompanyID' => $vendor->company_id, 'APIKey' => $vendor->api_key] : null;
        
        return null;
    }

    /**
     * Process marketplace order with vendor-specific credentials
     */
    public function processMarketplaceOrder(array $orderData, int $vendorId): array
    {
        $vendorCredentials = $this->getVendorCredentials($vendorId);

        if ($vendorCredentials === null) {
            return [
                'success' => false,
                'error' => 'Vendor credentials not found',
            ];
        }

        // Override default credentials with vendor credentials
        $request = array_merge($orderData, [
            'Credentials' => $vendorCredentials,
        ]);

        $response = $this->apiService->post($request, '/creditguy/gateway/transaction/', true);

        if ($response === null) {
            return [
                'success' => false,
                'error' => 'No response from payment gateway',
            ];
        }

        if ($response['Status'] == 0 && ($response['Data']['Success'] ?? false)) {
            return [
                'success' => true,
                'transaction_id' => $response['Data']['TransactionID'] ?? null,
                'document_id' => $response['Data']['DocumentID'] ?? null,
            ];
        }

        return [
            'success' => false,
            'error' => $response['Data']['ResultDescription'] ?? $response['UserErrorMessage'] ?? 'Payment failed',
        ];
    }

    /**
     * Validate vendor credentials
     */
    public function validateVendorCredentials(string $companyId, string $apiKey): ?string
    {
        return $this->apiService->checkCredentials($companyId, $apiKey);
    }

    /**
     * Check if Dokan marketplace is enabled
     */
    public function isDokanEnabled(): bool
    {
        return Config::get('sumit-payment.marketplace.dokan_enabled', false);
    }

    /**
     * Check if WCFM marketplace is enabled
     */
    public function isWCFMEnabled(): bool
    {
        return Config::get('sumit-payment.marketplace.wcfm_enabled', false);
    }

    /**
     * Check if WC Vendors marketplace is enabled
     */
    public function isWCVendorsEnabled(): bool
    {
        return Config::get('sumit-payment.marketplace.wcvendors_enabled', false);
    }

    /**
     * Determine if order should use vendor credentials
     */
    public function shouldUseVendorCredentials(array $orderData): bool
    {
        // Check if any marketplace integration is enabled
        if (!$this->isDokanEnabled() && !$this->isWCFMEnabled() && !$this->isWCVendorsEnabled()) {
            return false;
        }

        // Check if order has a vendor ID
        return isset($orderData['vendor_id']) && $orderData['vendor_id'] > 0;
    }

    /**
     * Split order by vendors
     */
    public function splitOrderByVendors(array $orderData): array
    {
        $vendorOrders = [];

        foreach ($orderData['items'] as $item) {
            $vendorId = $item['vendor_id'] ?? 0;

            if (!isset($vendorOrders[$vendorId])) {
                $vendorOrders[$vendorId] = [
                    'vendor_id' => $vendorId,
                    'items' => [],
                    'total' => 0,
                ];
            }

            $vendorOrders[$vendorId]['items'][] = $item;
            $vendorOrders[$vendorId]['total'] += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        }

        return array_values($vendorOrders);
    }
}
