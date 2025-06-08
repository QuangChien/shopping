<?php

namespace App\Services;

use App\Contracts\Services\InventoryServiceInterface;
use App\Exceptions\OrderException;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class InventoryService implements InventoryServiceInterface
{
    public function checkAvailability(int $productId, int $quantity): bool
    {
        $product = Product::find($productId);

        if (!$product) {
            return false;
        }

        $inventory = $product->inventory;

        if (!$inventory) {
            return false;
        }

        // Check if the product has inventory management
        if (!$inventory->manage_stock) {
            return true;
        }

        // Check if the product allows backorder
        if ($inventory->backorders !== 'no') {
            return true;
        }

        // Check remaining quantity
        $availableQuantity = $inventory->quantity - $inventory->reserved_quantity;

        return $availableQuantity >= $quantity;
    }

    public function reserveStock(Order $order): void
    {
        foreach ($order->items as $item) {
            $product = $item->product;

            if (!$product || !$product->inventory) {
                continue;
            }

            $inventory = $product->inventory;

            // Pre-order only if product is in stock
            if ($inventory->manage_stock) {
                $inventory->reserved_quantity += $item->quantity;
                $inventory->save();

                // Pre-order only if product is in stock
                $this->updateStockStatus($inventory);
            }
        }
    }

    public function releaseStock(Order $order): void
    {
        foreach ($order->items as $item) {
            $product = $item->product;

            if (!$product || !$product->inventory) {
                continue;
            }

            $inventory = $product->inventory;

            // Release only if product is in inventory management
            if ($inventory->manage_stock) {
                $inventory->reserved_quantity = max(0, $inventory->reserved_quantity - $item->quantity);
                $inventory->save();

                // Update inventory status
                $this->updateStockStatus($inventory);
            }
        }
    }

    public function reduceStock(Order $order): void
    {
        foreach ($order->items as $item) {
            $product = $item->product;

            if (!$product || !$product->inventory) {
                continue;
            }

            $inventory = $product->inventory;

            // Update inventory status
            if ($inventory->manage_stock) {
                $inventory->quantity = max(0, $inventory->quantity - $item->quantity);
                $inventory->reserved_quantity = max(0, $inventory->reserved_quantity - $item->quantity);
                $inventory->save();

                // Update inventory status
                $this->updateStockStatus($inventory);

                // Log if inventory is below minimum
                if ($inventory->quantity <= $inventory->min_quantity) {
                    Log::warning("Product {$product->name} (ID: {$product->id}) low inventory. Currently: {$inventory->quantity}, Tối thiểu: {$inventory->min_quantity}");
                }
            }
        }
    }

    public function updateStock(Product $product, int $quantity): void
    {
        $inventory = $product->inventory;

        if (!$inventory) {
            throw new OrderException("Product {$product->name} No inventory information");
        }

        // Update quantity
        $inventory->quantity = max(0, $quantity);
        $inventory->save();

        // Update inventory status
        $this->updateStockStatus($inventory);
    }

    private function updateStockStatus($inventory): void
    {
        if ($inventory->manage_stock) {
            $availableQuantity = $inventory->quantity - $inventory->reserved_quantity;
            $inventory->is_in_stock = $availableQuantity > 0 || $inventory->backorders !== 'no';
            $inventory->save();
        }
    }
}
