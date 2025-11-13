<?php

namespace NmDigitalHub\SumitPayment\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class ApiService
{
    protected Client $client;
    protected string $environment;
    protected int $timeout;

    public function __construct()
    {
        $this->client = new Client();
        $this->environment = Config::get('sumit-payment.environment', 'www');
        $this->timeout = Config::get('sumit-payment.api.timeout', 180);
    }

    /**
     * Get API URL based on environment
     */
    public function getUrl(string $path): string
    {
        if ($this->environment === 'dev') {
            return Config::get('sumit-payment.api.dev_url') . $path;
        }
        
        return Config::get('sumit-payment.api.base_url') . $path;
    }

    /**
     * Send POST request to SUMIT API
     */
    public function post(array $request, string $path, ?bool $sendClientIP = null): ?array
    {
        try {
            $response = $this->postRaw($request, $path, $sendClientIP);
            return $response;
        } catch (\Exception $e) {
            $this->writeToLog("API Error: {$e->getMessage()}", 'error');
            return null;
        }
    }

    /**
     * Send raw POST request to SUMIT API
     */
    protected function postRaw(array $request, string $path, ?bool $sendClientIP = null): array
    {
        $url = $this->getUrl($path);
        
        // Log request (sanitized)
        $this->logRequest($request, $url);

        $sendClientIP = $sendClientIP ?? Config::get('sumit-payment.payment.send_client_ip', true);

        $headers = [
            'Content-Type' => 'application/json',
            'Content-Language' => app()->getLocale(),
            'X-OG-Client' => 'Laravel',
        ];

        if ($sendClientIP && request()->ip()) {
            $headers['X-OG-ClientIP'] = request()->ip();
        }

        try {
            $response = $this->client->post($url, [
                'json' => $request,
                'headers' => $headers,
                'timeout' => $this->timeout,
                'verify' => Config::get('sumit-payment.api.ssl_verify', true),
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            
            // Log response
            $this->writeToLog("Response from {$url}: " . json_encode($body, JSON_PRETTY_PRINT), 'debug');
            
            return $body;
        } catch (GuzzleException $e) {
            $this->writeToLog("HTTP Error for {$url}: " . $e->getMessage(), 'error');
            throw $e;
        }
    }

    /**
     * Check API credentials validity
     */
    public function checkCredentials(string $companyId, string $apiKey): ?string
    {
        $request = [
            'Credentials' => [
                'CompanyID' => $companyId,
                'APIKey' => $apiKey,
            ],
        ];

        $response = $this->post($request, '/website/companies/getdetails/', false);
        
        if ($response === null) {
            return 'No response from server';
        }

        if ($response['Status'] === 'Success') {
            return null; // No error
        }

        return $response['UserErrorMessage'] ?? 'Unknown error';
    }

    /**
     * Check public API credentials validity
     */
    public function checkPublicCredentials(string $companyId, string $apiPublicKey): ?string
    {
        $request = [
            'Credentials' => [
                'CompanyID' => $companyId,
                'APIPublicKey' => $apiPublicKey,
            ],
            'CardNumber' => '12345678',
            'ExpirationMonth' => '01',
            'ExpirationYear' => '2030',
            'CVV' => '123',
            'CitizenID' => '123456789',
        ];

        $response = $this->post($request, '/creditguy/vault/tokenizesingleusejson/', false);
        
        if ($response === null) {
            return 'No response from server';
        }

        if ($response['Status'] === 'Success') {
            return null; // No error
        }

        return $response['UserErrorMessage'] ?? 'Unknown error';
    }

    /**
     * Log request with sensitive data sanitized
     */
    protected function logRequest(array $request, string $url): void
    {
        $requestLog = $request;
        
        // Sanitize sensitive data
        if (isset($requestLog['PaymentMethod'])) {
            $requestLog['PaymentMethod']['CreditCard_Number'] = '****';
            $requestLog['PaymentMethod']['CreditCard_CVV'] = '***';
        }
        
        $requestLog['CardNumber'] = '****';
        $requestLog['CVV'] = '***';

        $this->writeToLog("Request to {$url}: " . json_encode($requestLog, JSON_PRETTY_PRINT), 'debug');
    }

    /**
     * Write to application log
     */
    public function writeToLog(string $message, string $level = 'debug'): void
    {
        if (!Config::get('sumit-payment.logging.enabled', true)) {
            return;
        }

        $logLevel = Config::get('sumit-payment.logging.level', 'debug');
        
        // Only log if the message level is at or above configured level
        $levels = ['debug' => 0, 'info' => 1, 'warning' => 2, 'error' => 3];
        
        if (($levels[$level] ?? 0) >= ($levels[$logLevel] ?? 0)) {
            Log::channel('single')->log($level, "[SUMIT] {$message}");
        }
    }
}
