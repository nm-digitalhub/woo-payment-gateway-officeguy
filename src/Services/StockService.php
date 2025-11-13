<?php

namespace NmDigitalHub\SumitPayment\Services;

use NmDigitalHub\SumitPayment\Settings\SumitPaymentSettings;

class StockService
{
    protected ApiService $apiService;
    protected SumitPaymentSettings $settings;

    public function __construct(ApiService $apiService, SumitPaymentSettings $settings)
    {
        $this->apiService = $apiService;
        $this->settings = $settings;
    }

    /**
     * Sync stock levels from SUMIT
     */
    public function syncStock(array $products): array
    {
        if (!$this->settings->stock_sync_enabled) {
            return [
                'success' => false,
                'error' => 'Stock sync is disabled',
            ];
        }

        $request = [
            'Credentials' => [
                'CompanyID' => $this->settings->company_id,
                'APIKey' => $this->settings->api_key,
            ],
            'Products' => $this->prepareProductsForSync($products),
        ];

        $response = $this->apiService->post($request, '/inventory/stock/sync/', false);

        if ($response === null) {
            return [
                'success' => false,
                'error' => 'No response from stock sync API',
            ];
        }

        if ($response['Status'] == 0) {
            return [
                'success' => true,
                'synced_products' => $response['Data']['SyncedProducts'] ?? [],
            ];
        }

        return [
            'success' => false,
            'error' => $response['UserErrorMessage'] ?? 'Stock sync failed',
        ];
    }

    /**
     * Prepare products array for stock sync
     */
    protected function prepareProductsForSync(array $products): array
    {
        $prepared = [];

        foreach ($products as $product) {
            $prepared[] = [
                'SKU' => $product['sku'] ?? '',
                'ExternalID' => $product['external_id'] ?? '',
                'Name' => $product['name'] ?? '',
            ];
        }

        return $prepared;
    }

    /**
     * Update stock for a single product after purchase
     */
    public function updateStockAfterPurchase(string $productId, int $quantity): array
    {
        $request = [
            'Credentials' => [
                'CompanyID' => $this->settings->company_id,
                'APIKey' => $this->settings->api_key,
            ],
            'ProductID' => $productId,
            'Quantity' => -$quantity, // Negative for deduction
        ];

        $response = $this->apiService->post($request, '/inventory/stock/update/', false);

        if ($response === null) {
            return [
                'success' => false,
                'error' => 'No response from stock update API',
            ];
        }

        if ($response['Status'] == 0) {
            return [
                'success' => true,
                'new_stock' => $response['Data']['NewStock'] ?? null,
            ];
        }

        return [
            'success' => false,
            'error' => $response['UserErrorMessage'] ?? 'Stock update failed',
        ];
    }

    /**
     * Get current stock level for a product
     */
    public function getStockLevel(string $productId): ?int
    {
        $request = [
            'Credentials' => [
                'CompanyID' => $this->settings->company_id,
                'APIKey' => $this->settings->api_key,
            ],
            'ProductID' => $productId,
        ];

        $response = $this->apiService->post($request, '/inventory/stock/get/', false);

        if ($response && $response['Status'] == 0) {
            return $response['Data']['Stock'] ?? null;
        }

        return null;
    }
}
