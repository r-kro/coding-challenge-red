<?php

namespace App\Services;

class MockProviderClient implements ProviderClientInterface
{
    public function createOrder(string $type): array
    {
        return [
            'id' => 'mock-' . uniqid(),
            'type' => $type,
            'status' => 'ordered',
        ];
    }

    public function getOrderStatus(string $providerOrderId): string
    {
        return 'completed';
    }

    public function deleteOrder(string $providerOrderId): void
    {
        //
    }
}