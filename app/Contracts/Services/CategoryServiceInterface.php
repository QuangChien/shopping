<?php

namespace App\Contracts\Services;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CategoryServiceInterface
{
    /**
     * Get all categories
     * 
     * @return Collection
     */
    public function getAllCategories(): Collection;
    
    /**
     * Get active categories
     * 
     * @return Collection
     */
    public function getActiveCategories(): Collection;
    
    /**
     * Get categories with pagination
     * 
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginatedCategories(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    /**
     * Get category by ID
     * 
     * @param int $id
     * @return Category|null
     */
    public function getCategoryById(int $id): ?Category;
    
    /**
     * Get category by slug
     * 
     * @param string $slug
     * @return Category|null
     */
    public function getCategoryBySlug(string $slug): ?Category;
    
    /**
     * Create a new category
     * 
     * @param array $data
     * @return Category
     */
    public function createCategory(array $data): Category;
    
    /**
     * Update category
     * 
     * @param Category $category
     * @param array $data
     * @return Category
     */
    public function updateCategory(Category $category, array $data): Category;
    
    /**
     * Delete category
     * 
     * @param Category $category
     * @return bool
     */
    public function deleteCategory(Category $category): bool;
    
    /**
     * Get categories hierarchy for dropdown
     * 
     * @return Collection
     */
    public function getCategoryHierarchy(): Collection;
} 