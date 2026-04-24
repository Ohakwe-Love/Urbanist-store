<?php

namespace App\Services;

use Illuminate\Http\Client\Factory as HttpFactory;

class PaystackService
{
    public function __construct(
        private HttpFactory $http,
    ) {
    }

    public function isConfigured(): bool
    {
        return filled(config('services.paystack.secret_key'));
    }

    public function currency(): string
    {
        return (string) config('services.paystack.currency', 'USD');
    }

    public function initializeTransaction(array $payload): array
    {
        return $this->request()->post('/transaction/initialize', $payload)->throw()->json('data');
    }

    public function verifyTransaction(string $reference): array
    {
        return $this->request()->get("/transaction/verify/{$reference}")->throw()->json('data');
    }

    public function hasValidSignature(string $payload, ?string $signature): bool
    {
        $secret = (string) config('services.paystack.secret_key');

        if ($secret === '' || blank($signature)) {
            return false;
        }

        return hash_equals(hash_hmac('sha512', $payload, $secret), (string) $signature);
    }

    protected function request()
    {
        return $this->http
            ->baseUrl((string) config('services.paystack.base_url', 'https://api.paystack.co'))
            ->withToken((string) config('services.paystack.secret_key'))
            ->acceptJson();
    }
}
