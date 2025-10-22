<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaystackClient
{
    protected string $baseUrl;
    protected string $secretKey;

    public function __construct()
    {
        $this->baseUrl = config('payments.paystack.base_url');
        $this->secretKey = config('payments.paystack.secret_key');
    }

    public function initializeTransaction(array $payload): array
    {
        $response = Http::withToken($this->secretKey)
            ->post($this->baseUrl . '/transaction/initialize', $payload);
        if (!$response->successful()) {
            throw new \RuntimeException('Failed to initialize Paystack transaction');
        }
        return $response->json('data');
    }

    public function verifyTransaction(string $reference): array
    {
        $response = Http::withToken($this->secretKey)
            ->get($this->baseUrl . '/transaction/verify/' . $reference);
        if (!$response->successful()) {
            throw new \RuntimeException('Failed to verify Paystack transaction');
        }
        return $response->json('data');
    }

    public static function verifySignature(string $payload, ?string $signature): bool
    {
        if (!$signature) return false;
        $computed = hash_hmac('sha512', $payload, config('payments.paystack.secret_key'));
        return hash_equals($computed, $signature);
    }
}
