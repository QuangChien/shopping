<?php

namespace App\Services;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Services\ProductServiceInterface;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductService implements ProductServiceInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    
    /**
     * ProductService constructor.
     * 
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    
    /**
     * Get all products
     * 
     * @return Collection
     */
    public function getAllProducts(): Collection
    {
        return $this->productRepository->getAll();
    }
    
    /**
     * Get active products
     * 
     * @return Collection
     */
    public function getActiveProducts(): Collection
    {
        return $this->productRepository->getActive();
    }
    
    /**
     * Get products with pagination
     * 
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginatedProducts(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->productRepository->getPaginated($perPage, $filters);
    }
    
    /**
     * Get product by ID
     * 
     * @param int $id
     * @return Product|null
     */
    public function getProductById(int $id): ?Product
    {
        return $this->productRepository->findById($id);
    }
    
    /**
     * Get product by SKU
     * 
     * @param string $sku
     * @return Product|null
     */
    public function getProductBySku(string $sku): ?Product
    {
        return $this->productRepository->findBySku($sku);
    }
    
    /**
     * Create a new product
     * 
     * @param array $data
     * @return Product
     */
    public function createProduct(array $data): Product
    {
        try {
            DB::beginTransaction();
            
            // Ensure status is properly set
            if (!isset($data['status'])) {
                $data['status'] = 'disabled';
            }
            
            // Extract category data if provided
            $categoryIds = $data['category_ids'] ?? [];
            unset($data['category_ids']);
            
            // Extract attribute data if provided
            $attributeData = $data['attributes'] ?? [];
            unset($data['attributes']);
            
            $product = $this->productRepository->create($data);
            
            // Associate categories
            if (!empty($categoryIds)) {
                $this->productRepository->syncCategories($product, $categoryIds);
            }
            
            // Set attribute values
            if (!empty($attributeData)) {
                $this->setProductAttributeValues($product, $attributeData);
            }
            
            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating product: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update product
     * 
     * @param Product $product
     * @param array $data
     * @return Product
     */
    public function updateProduct(Product $product, array $data): Product
    {
        try {
            DB::beginTransaction();
            
            // Extract category data if provided
            $categoryIds = $data['category_ids'] ?? [];
            unset($data['category_ids']);
            
            // Extract attribute data if provided
            $attributeData = $data['attributes'] ?? [];
            unset($data['attributes']);
            
            $updatedProduct = $this->productRepository->update($product, $data);
            
            // Associate categories
            if (isset($categoryIds)) {
                $this->productRepository->syncCategories($updatedProduct, $categoryIds);
            }
            
            // Set attribute values
            if (!empty($attributeData)) {
                $this->setProductAttributeValues($updatedProduct, $attributeData);
            }
            
            DB::commit();
            return $updatedProduct;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating product: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Delete product
     * 
     * @param Product $product
     * @return bool
     */
    public function deleteProduct(Product $product): bool
    {
        try {
            DB::beginTransaction();
            
            $result = $this->productRepository->delete($product);
            
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting product: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get product attribute values
     * 
     * @param Product $product
     * @return array
     */
    public function getProductAttributeValues(Product $product): array
    {
        $attributes = ProductAttribute::all();
        $result = [];
        
        foreach ($attributes as $attribute) {
            $value = null;
            
            // Determine the appropriate table based on backend type
            switch ($attribute->backend_type) {
                case 'varchar':
                    $valueObj = $product->varcharValues()->where('attribute_id', $attribute->id)->first();
                    break;
                case 'text':
                    $valueObj = $product->textValues()->where('attribute_id', $attribute->id)->first();
                    break;
                case 'int':
                    $valueObj = $product->intValues()->where('attribute_id', $attribute->id)->first();
                    break;
                case 'decimal':
                    $valueObj = $product->decimalValues()->where('attribute_id', $attribute->id)->first();
                    break;
                case 'datetime':
                    $valueObj = $product->datetimeValues()->where('attribute_id', $attribute->id)->first();
                    break;
                case 'boolean':
                    $valueObj = $product->booleanValues()->where('attribute_id', $attribute->id)->first();
                    break;
                default:
                    $valueObj = null;
            }
            
            $value = $valueObj ? $valueObj->value : null;
            
            $result[$attribute->code] = [
                'attribute_id' => $attribute->id,
                'value' => $value,
                'type' => $attribute->backend_type
            ];
        }
        
        return $result;
    }
    
    /**
     * Set product attribute values
     * 
     * @param Product $product
     * @param array $attributeData
     * @return void
     */
    private function setProductAttributeValues(Product $product, array $attributeData): void
    {
        foreach ($attributeData as $code => $value) {
            // Skip if value is null
            if ($value === null) {
                continue;
            }
            
            // Find attribute by code
            $attribute = ProductAttribute::where('code', $code)->first();
            
            if (!$attribute) {
                continue;
            }
            
            // Determine the appropriate table based on backend type
            switch ($attribute->backend_type) {
                case 'varchar':
                    $product->varcharValues()->updateOrCreate(
                        ['attribute_id' => $attribute->id],
                        ['value' => (string) $value]
                    );
                    break;
                case 'text':
                    $product->textValues()->updateOrCreate(
                        ['attribute_id' => $attribute->id],
                        ['value' => (string) $value]
                    );
                    break;
                case 'int':
                    $product->intValues()->updateOrCreate(
                        ['attribute_id' => $attribute->id],
                        ['value' => (int) $value]
                    );
                    break;
                case 'decimal':
                    $product->decimalValues()->updateOrCreate(
                        ['attribute_id' => $attribute->id],
                        ['value' => (float) $value]
                    );
                    break;
                case 'datetime':
                    $product->datetimeValues()->updateOrCreate(
                        ['attribute_id' => $attribute->id],
                        ['value' => $value]
                    );
                    break;
                case 'boolean':
                    $product->booleanValues()->updateOrCreate(
                        ['attribute_id' => $attribute->id],
                        ['value' => (bool) $value]
                    );
                    break;
            }
        }
    }
} 