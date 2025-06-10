<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * @var Product
     */
    protected $model;

    /**
     * ProductRepository constructor.
     *
     * @param Product $model
     */
    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    /**
     * Get all products
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * Get active products
     *
     * @return Collection
     */
    public function getActive(): Collection
    {
        return $this->model->where('status', 'enabled')->get();
    }

    /**
     * Get products with pagination
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        // Apply search filter
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('sku', 'like', $searchTerm)
                    ->orWhereHas('varcharValues', function ($q) use ($searchTerm) {
                        $q->where('value', 'like', $searchTerm);
                    });
            });
        }

        // Apply status filter
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        // Apply type filter
        if (isset($filters['type_id']) && $filters['type_id'] !== '') {
            $query->where('type_id', $filters['type_id']);
        }

        // Apply category filter
        if (isset($filters['category_id']) && $filters['category_id'] !== '') {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('categories.id', $filters['category_id']);
            });
        }

        // Get name attribute for sorting
        $nameAttributeId = \App\Models\ProductAttribute::where('code', 'name')->first()?->id;

        // Default sort by name if available, otherwise by SKU
        if ($nameAttributeId) {
            $query->leftJoin('product_attribute_values_varchar as name_attr', function ($join) use ($nameAttributeId) {
                $join->on('products.id', '=', 'name_attr.product_id')
                    ->where('name_attr.attribute_id', '=', $nameAttributeId);
            })
            ->orderBy('name_attr.value', 'asc')
            ->orderBy('products.sku', 'asc')
            ->select('products.*');
        } else {
            $query->orderBy('sku', 'asc');
        }

        return $query->with(['categories'])->paginate($perPage);
    }

    /**
     * Find product by ID
     *
     * @param int $id
     * @return Product|null
     */
    public function findById(int $id): ?Product
    {
        return $this->model->find($id);
    }

    /**
     * Find product by SKU
     *
     * @param string $sku
     * @return Product|null
     */
    public function findBySku(string $sku): ?Product
    {
        return $this->model->where('sku', $sku)->first();
    }

    /**
     * Create a new product
     *
     * @param array $data
     * @return Product
     */
    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    /**
     * Update product
     *
     * @param Product $product
     * @param array $data
     * @return Product
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product;
    }

    /**
     * Delete product
     *
     * @param Product $product
     * @return bool
     */
    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    /**
     * Sync product categories
     *
     * @param Product $product
     * @param array $categoryIds
     * @return void
     */
    public function syncCategories(Product $product, array $categoryIds): void
    {
        // Convert categoryIds to a format for sync with position
        $syncData = [];
        foreach ($categoryIds as $index => $categoryId) {
            $syncData[$categoryId] = ['position' => $index];
        }

        $product->categories()->sync($syncData);
    }
} 