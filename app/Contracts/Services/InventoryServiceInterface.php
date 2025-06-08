<?php

namespace App\Contracts\Services;

use App\Models\Order;
use App\Models\Product;

interface InventoryServiceInterface
{
    public function checkAvailability(int $productId, int $quantity): bool;
    public function reserveStock(Order $order): void;
    public function releaseStock(Order $order): void;
    public function reduceStock(Order $order): void;
    public function updateStock(Product $product, int $quantity): void;
} 