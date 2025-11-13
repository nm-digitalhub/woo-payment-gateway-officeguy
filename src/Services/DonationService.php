<?php

namespace NmDigitalHub\SumitPayment\Services;

use Illuminate\Support\Facades\Config;

class DonationService
{
    /**
     * Check if an order contains donation items
     */
    public function containsDonation(array $items): bool
    {
        foreach ($items as $item) {
            if (isset($item['is_donation']) && $item['is_donation'] === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Mark order as donation for document type
     */
    public function getDocumentType(array $orderData): string
    {
        if ($this->containsDonation($orderData['items'] ?? [])) {
            return 'DonationReceipt';
        }

        return 'Invoice'; // or 'Receipt' based on configuration
    }

    /**
     * Prepare donation items for payment request
     */
    public function prepareDonationItems(array $items): array
    {
        $preparedItems = [];

        foreach ($items as $item) {
            $preparedItem = [
                'Name' => $item['name'] ?? '',
                'Price' => $item['price'] ?? 0,
                'Quantity' => $item['quantity'] ?? 1,
                'IsPriceIncludeVAT' => true,
            ];

            // Mark as donation if applicable
            if (isset($item['is_donation']) && $item['is_donation'] === true) {
                $preparedItem['IsDonation'] = true;
            }

            $preparedItems[] = $preparedItem;
        }

        return $preparedItems;
    }

    /**
     * Validate donation items
     */
    public function validateDonationItems(array $items): array
    {
        $errors = [];

        foreach ($items as $index => $item) {
            if (isset($item['is_donation']) && $item['is_donation'] === true) {
                // Donations typically require specific validation
                if (empty($item['name'])) {
                    $errors[] = "Donation item at index {$index} must have a name";
                }

                if (!isset($item['price']) || $item['price'] <= 0) {
                    $errors[] = "Donation item at index {$index} must have a valid price";
                }
            }
        }

        return $errors;
    }

    /**
     * Check if donations are enabled
     */
    public function isEnabled(): bool
    {
        return Config::get('sumit-payment.donations.enabled', false);
    }
}
