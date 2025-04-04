<?php

namespace App\Services;

class MockProviderPortal implements ProviderPortalInterface
{
    public function createOrder(string $type): array
    {
        return ['id' => uniqid(), 'type' => $type, 'status' => 'ordered'];
    }

    public function getOrder(string $id): array
    {
        return ['id' => $id, 'type' => 'connector', 'status' => 'completed'];
    }

    public function deleteOrder(string $id): bool
    {
        return true;
    }
}
