<?php

namespace App\Contracts\Repositories;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    /**
     * Get all categories
     * 
     * @return Collection
     */
    public function getAll(): Collection;
    
    /**
     * Get active categories
     * 
     * @return Collection
     */
    public function getActive(): Collection;
    
    /**
     * Get categories with pagination
     * 
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    /**
     * Get category by ID
     * 
     * @param int $id
     * @return Category|null
     */
    public function findById(int $id): ?Category;
    
    /**
     * Get category by slug
     * 
     * @param string $slug
     * @return Category|null
     */
    public function findBySlug(string $slug): ?Category;
    
    /**
     * Create a new category
     * 
     * @param array $data
     * @return Category
     */
    public function create(array $data): Category;
    
    /**
     * Update category
     * 
     * @param Category $category
     * @param array $data
     * @return Category
     */
    public function update(Category $category, array $data): Category;
    
    /**
     * Delete category
     * 
     * @param Category $category
     * @return bool
     */
    public function delete(Category $category): bool;
    
    /**
     * Get categories hierarchy
     * 
     * @return Collection
     */
    public function getCategoryHierarchy(): Collection;
} 