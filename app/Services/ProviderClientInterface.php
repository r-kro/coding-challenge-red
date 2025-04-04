<?php

namespace App\Services;

interface ProviderClientInterface 
{
    public function createOrder(string $type): array;
    public function getOrderStatus(string $id): string;
    public function deleteOrder(string $id): void;
}
