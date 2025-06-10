<?php

namespace App\Contracts\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    /**
     * Get all products
     * 
     * @return Collection
     */
    public function getAll(): Collection;
    
    /**
     * Get active products
     * 
     * @return Collection
     */
    public function getActive(): Collection;
    
    /**
     * Get products with pagination
     * 
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    /**
     * Find product by ID
     * 
     * @param int $id
     * @return Product|null
     */
    public function findById(int $id): ?Product;
    
    /**
     * Find product by SKU
     * 
     * @param string $sku
     * @return Product|null
     */
    public function findBySku(string $sku): ?Product;
    
    /**
     * Create a new product
     * 
     * @param array $data
     * @return Product
     */
    public function create(array $data): Product;
    
    /**
     * Update product
     * 
     * @param Product $product
     * @param array $data
     * @return Product
     */
    public function update(Product $product, array $data): Product;
    
    /**
     * Delete product
     * 
     * @param Product $product
     * @return bool
     */
    public function delete(Product $product): bool;
    
    /**
     * Sync product categories
     * 
     * @param Product $product
     * @param array $categoryIds
     * @return void
     */
    public function syncCategories(Product $product, array $categoryIds): void;
} 