<?php

namespace App\Services;

interface ProviderPortalInterface 
{
    public function createOrder(string $type): array;
    public function getOrder(string $id): array;
    public function deleteOrder(string $id): bool;
}
