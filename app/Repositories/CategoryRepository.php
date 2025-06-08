<?php

namespace App\Repositories;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * Get all categories
     * 
     * @return Collection
     */
    public function getAll(): Collection
    {
        return Category::with('parent')->orderBy('sort_order')->get();
    }
    
    /**
     * Get active categories
     * 
     * @return Collection
     */
    public function getActive(): Collection
    {
        return Category::with('parent')->where('is_active', true)->orderBy('sort_order')->get();
    }
    
    /**
     * Get categories with pagination
     * 
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Category::with('parent');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('slug', 'like', '%' . $filters['search'] . '%');
        }
        
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }
        
        if (!empty($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        }
        
        return $query->orderBy('sort_order')->paginate($perPage);
    }
    
    /**
     * Get category by ID
     * 
     * @param int $id
     * @return Category|null
     */
    public function findById(int $id): ?Category
    {
        return Category::with('parent', 'children')->find($id);
    }
    
    /**
     * Get category by slug
     * 
     * @param string $slug
     * @return Category|null
     */
    public function findBySlug(string $slug): ?Category
    {
        return Category::with('parent', 'children')->where('slug', $slug)->first();
    }
    
    /**
     * Create a new category
     * 
     * @param array $data
     * @return Category
     */
    public function create(array $data): Category
    {
        // Generate slug if not provided
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        return Category::create($data);
    }
    
    /**
     * Update category
     * 
     * @param Category $category
     * @param array $data
     * @return Category
     */
    public function update(Category $category, array $data): Category
    {
        // Generate slug if not provided
        if (isset($data['name']) && (!isset($data['slug']) || empty($data['slug']))) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        $category->update($data);
        return $category->refresh();
    }
    
    /**
     * Delete category
     * 
     * @param Category $category
     * @return bool
     */
    public function delete(Category $category): bool
    {
        return $category->delete();
    }
    
    /**
     * Get categories hierarchy
     * 
     * @return Collection
     */
    public function getCategoryHierarchy(): Collection
    {
        $categories = Category::with('children')->whereNull('parent_id')->orderBy('sort_order')->get();
        return $this->buildCategoryTree($categories);
    }
    
    /**
     * Build category tree recursively
     * 
     * @param Collection $categories
     * @param int $level
     * @return Collection
     */
    private function buildCategoryTree(Collection $categories, int $level = 0): Collection
    {
        foreach ($categories as $category) {
            $category->level = $level;
            if ($category->children->count() > 0) {
                $category->children = $this->buildCategoryTree($category->children, $level + 1);
            }
        }
        
        return $categories;
    }
} 