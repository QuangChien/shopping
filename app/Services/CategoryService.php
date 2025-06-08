<?php

namespace App\Services;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Services\CategoryServiceInterface;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryService implements CategoryServiceInterface
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;
    
    /**
     * CategoryService constructor.
     * 
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }
    
    /**
     * Get all categories
     * 
     * @return Collection
     */
    public function getAllCategories(): Collection
    {
        return $this->categoryRepository->getAll();
    }
    
    /**
     * Get active categories
     * 
     * @return Collection
     */
    public function getActiveCategories(): Collection
    {
        return $this->categoryRepository->getActive();
    }
    
    /**
     * Get categories with pagination
     * 
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginatedCategories(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->categoryRepository->getPaginated($perPage, $filters);
    }
    
    /**
     * Get category by ID
     * 
     * @param int $id
     * @return Category|null
     */
    public function getCategoryById(int $id): ?Category
    {
        return $this->categoryRepository->findById($id);
    }
    
    /**
     * Get category by slug
     * 
     * @param string $slug
     * @return Category|null
     */
    public function getCategoryBySlug(string $slug): ?Category
    {
        return $this->categoryRepository->findBySlug($slug);
    }
    
    /**
     * Create a new category
     * 
     * @param array $data
     * @return Category
     */
    public function createCategory(array $data): Category
    {
        try {
            DB::beginTransaction();
            
            // Ensure is_active is properly set
            if (!isset($data['is_active'])) {
                $data['is_active'] = true;
            }
            
            $category = $this->categoryRepository->create($data);
            
            DB::commit();
            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating category: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update category
     * 
     * @param Category $category
     * @param array $data
     * @return Category
     */
    public function updateCategory(Category $category, array $data): Category
    {
        try {
            DB::beginTransaction();
            
            // Check for circular reference in parent_id
            if (isset($data['parent_id']) && $data['parent_id'] == $category->id) {
                throw new \Exception('Category cannot be its own parent');
            }
            
            // Check for deep circular reference
            if (isset($data['parent_id']) && $data['parent_id']) {
                $this->checkCircularReference($category->id, $data['parent_id']);
            }
            
            $updatedCategory = $this->categoryRepository->update($category, $data);
            
            DB::commit();
            return $updatedCategory;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating category: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Delete category
     * 
     * @param Category $category
     * @return bool
     */
    public function deleteCategory(Category $category): bool
    {
        try {
            DB::beginTransaction();
            
            // Move child categories to parent
            if ($category->children->count() > 0) {
                foreach ($category->children as $child) {
                    $child->parent_id = $category->parent_id;
                    $child->save();
                }
            }
            
            $result = $this->categoryRepository->delete($category);
            
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting category: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get categories hierarchy for dropdown
     * 
     * @return Collection
     */
    public function getCategoryHierarchy(): Collection
    {
        return $this->categoryRepository->getCategoryHierarchy();
    }
    
    /**
     * Check for circular reference in category hierarchy
     * 
     * @param int $categoryId
     * @param int $parentId
     * @return void
     * @throws \Exception
     */
    private function checkCircularReference(int $categoryId, int $parentId): void
    {
        $parent = $this->categoryRepository->findById($parentId);
        
        if (!$parent) {
            return;
        }
        
        if ($parent->parent_id === $categoryId) {
            throw new \Exception('Circular reference detected in category hierarchy');
        }
        
        if ($parent->parent_id) {
            $this->checkCircularReference($categoryId, $parent->parent_id);
        }
    }
} 