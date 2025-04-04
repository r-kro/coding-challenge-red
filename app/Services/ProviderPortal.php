<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ProviderPortal implements ProviderPortalInterface
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $token;
    protected string $cert;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('PROVIDER_URL'), '/');
        $this->clientId = env('PROVIDER_CLIENT_ID');
        $this->clientSecret = env('PROVIDER_CLIENT_SECRET');
        $this->cert = storage_path('app/ssl_cert.pem');
        $this->token = $this->fetchToken();
    }

    protected function fetchToken(): string
    {
        $response = Http::withOptions([
            'verify' => $this->cert,
        ])->post("{$this->baseUrl}/api/v1/token", [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        return $response->json('access_token');
    }

    protected function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function createOrder(string $type): array
    {
        $response = Http::withOptions(['verify' => $this->cert])
            ->withHeaders($this->authHeaders())
            ->post("{$this->baseUrl}/api/v1/orders", ['type' => $type]);

        return $response->json();
    }

    public function getOrder(string $id): array
    {
        $response = Http::withOptions(['verify' => $this->cert])
            ->withHeaders($this->authHeaders())
            ->get("{$this->baseUrl}/api/v1/order/{$id}");

        return $response->json();
    }

    public function deleteOrder(string $id): bool
    {
        $response = Http::withOptions(['verify' => $this->cert])
            ->withHeaders($this->authHeaders())
            ->delete("{$this->baseUrl}/api/v1/order/{$id}");

        return $response->successful();
    }
}