<?php

namespace App\Contracts\Services;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductServiceInterface
{
    /**
     * Get all products
     * 
     * @return Collection
     */
    public function getAllProducts(): Collection;
    
    /**
     * Get active products
     * 
     * @return Collection
     */
    public function getActiveProducts(): Collection;
    
    /**
     * Get products with pagination
     * 
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginatedProducts(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    /**
     * Get product by ID
     * 
     * @param int $id
     * @return Product|null
     */
    public function getProductById(int $id): ?Product;
    
    /**
     * Get product by SKU
     * 
     * @param string $sku
     * @return Product|null
     */
    public function getProductBySku(string $sku): ?Product;
    
    /**
     * Create a new product
     * 
     * @param array $data
     * @return Product
     */
    public function createProduct(array $data): Product;
    
    /**
     * Update product
     * 
     * @param Product $product
     * @param array $data
     * @return Product
     */
    public function updateProduct(Product $product, array $data): Product;
    
    /**
     * Delete product
     * 
     * @param Product $product
     * @return bool
     */
    public function deleteProduct(Product $product): bool;
    
    /**
     * Get product attribute values
     * 
     * @param Product $product
     * @return array
     */
    public function getProductAttributeValues(Product $product): array;
} 