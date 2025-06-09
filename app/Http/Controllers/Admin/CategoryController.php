<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\CategoryServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryCreateRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * @var CategoryServiceInterface
     */
    protected $categoryService;

    /**
     * CategoryController constructor.
     *
     * @param CategoryServiceInterface $categoryService
     */
    public function __construct(CategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the categories.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'is_active', 'parent_id']);

        $categories = $this->categoryService->getPaginatedCategories(15, $filters);
        $parentCategories = $this->categoryService->getAllCategories()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name
                ];
            });

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
            'filters' => $filters,
            'parentCategories' => $parentCategories
        ]);
    }

    /**
     * Show the form for creating a new category.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $categoryHierarchy = $this->categoryService->getCategoryHierarchy()
            ->map(function ($category) {
                return $this->formatCategoryForDropdown($category);
            });

        return Inertia::render('Admin/Categories/Create', [
            'categoryHierarchy' => $categoryHierarchy
        ]);
    }

    /**
     * Store a newly created category in storage.
     *
     * @param CategoryCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CategoryCreateRequest $request)
    {
        try {
            Log::info('Category create data:', $request->validated());
            $category = $this->categoryService->createCategory($request->validated());

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'The category has been created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified category.
     *
     * @param Category $category
     * @return \Inertia\Response
     */
    public function edit(Category $category)
    {
        $category->load('parent');

        $categoryHierarchy = $this->categoryService->getCategoryHierarchy()
            ->map(function ($cat) {
                return $this->formatCategoryForDropdown($cat);
            });

        return Inertia::render('Admin/Categories/Edit', [
            'category' => $category,
            'categoryHierarchy' => $categoryHierarchy
        ]);
    }

    /**
     * Update the specified category in storage.
     *
     * @param CategoryUpdateRequest $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        try {
            $this->categoryService->updateCategory($category, $request->validated());

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'The category has been updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified category from storage.
     *
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        try {
            $this->categoryService->deleteCategory($category);

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'The category was successfully deleted.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Format category for dropdown
     *
     * @param Category $category
     * @return array
     */
    private function formatCategoryForDropdown($category)
    {
        $result = [
            'id' => $category->id,
            'name' => $category->name,
            'level' => $category->level ?? 0
        ];

        if ($category->children && $category->children->count() > 0) {
            $result['children'] = $category->children->map(function ($child) {
                return $this->formatCategoryForDropdown($child);
            });
        }

        return $result;
    }
}
