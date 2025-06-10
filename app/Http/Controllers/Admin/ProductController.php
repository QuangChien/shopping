<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\CategoryServiceInterface;
use App\Contracts\Services\ProductServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductCreateRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * @var ProductServiceInterface
     */
    protected $productService;

    /**
     * @var CategoryServiceInterface
     */
    protected $categoryService;

    /**
     * ProductController constructor.
     *
     * @param ProductServiceInterface $productService
     * @param CategoryServiceInterface $categoryService
     */
    public function __construct(
        ProductServiceInterface $productService,
        CategoryServiceInterface $categoryService
    ) {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the products.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'type_id', 'category_id']);

        $products = $this->productService->getPaginatedProducts(15, $filters);
        $categories = $this->categoryService->getActiveCategories()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name
                ];
            });

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'filters' => $filters,
            'categories' => $categories
        ]);
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $categoryHierarchy = $this->categoryService->getCategoryHierarchy()
            ->map(function ($category) {
                return $this->formatCategoryForDropdown($category);
            });

        // Get all product attributes
        $attributes = ProductAttribute::with('options')->get()->map(function ($attribute) {
            return [
                'id' => $attribute->id,
                'code' => $attribute->code,
                'label' => $attribute->frontend_label,
                'type' => $attribute->backend_type,
                'input' => $attribute->frontend_input,
                'required' => $attribute->is_required,
                'options' => $attribute->options ? $attribute->options->map(function ($option) {
                    return [
                        'value' => $option->value,
                        'label' => $option->label,
                        'swatch' => $option->swatch,
                    ];
                })->toArray() : [],
            ];
        });

        return Inertia::render('Admin/Products/Create', [
            'categoryHierarchy' => $categoryHierarchy,
            'attributes' => $attributes,
            'productTypes' => [
                ['id' => 'simple', 'name' => 'Simple Product'],
                ['id' => 'configurable', 'name' => 'Configurable Product'],
                ['id' => 'virtual', 'name' => 'Virtual Product'],
                ['id' => 'downloadable', 'name' => 'Downloadable Product'],
            ],
            'visibilityOptions' => [
                ['id' => 'not_visible', 'name' => 'Not Visible'],
                ['id' => 'catalog', 'name' => 'Catalog'],
                ['id' => 'search', 'name' => 'Search'],
                ['id' => 'catalog_search', 'name' => 'Catalog & Search'],
            ],
            'statusOptions' => [
                ['id' => 'enabled', 'name' => 'Enabled'],
                ['id' => 'disabled', 'name' => 'Disabled'],
            ]
        ]);
    }

    /**
     * Store a newly created product in storage.
     *
     * @param ProductCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProductCreateRequest $request)
    {
        try {
            Log::info('Product create data:', $request->validated());
            $product = $this->productService->createProduct($request->validated());

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'The product has been created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating product: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param Product $product
     * @return \Inertia\Response
     */
    public function edit(Product $product)
    {
        $product->load('categories');
        
        // Load all attribute values
        $product->attributeValues = $this->productService->getProductAttributeValues($product);

        $categoryHierarchy = $this->categoryService->getCategoryHierarchy()
            ->map(function ($cat) {
                return $this->formatCategoryForDropdown($cat);
            });

        // Get all product attributes
        $attributes = ProductAttribute::with('options')->get()->map(function ($attribute) {
            return [
                'id' => $attribute->id,
                'code' => $attribute->code,
                'label' => $attribute->frontend_label,
                'type' => $attribute->backend_type,
                'input' => $attribute->frontend_input,
                'required' => $attribute->is_required,
                'options' => $attribute->options ? $attribute->options->map(function ($option) {
                    return [
                        'value' => $option->value,
                        'label' => $option->label,
                        'swatch' => $option->swatch,
                    ];
                })->toArray() : [],
            ];
        });

        return Inertia::render('Admin/Products/Edit', [
            'product' => $product,
            'categoryHierarchy' => $categoryHierarchy,
            'attributes' => $attributes,
            'productTypes' => [
                ['id' => 'simple', 'name' => 'Simple Product'],
                ['id' => 'configurable', 'name' => 'Configurable Product'],
                ['id' => 'virtual', 'name' => 'Virtual Product'],
                ['id' => 'downloadable', 'name' => 'Downloadable Product'],
            ],
            'visibilityOptions' => [
                ['id' => 'not_visible', 'name' => 'Not Visible'],
                ['id' => 'catalog', 'name' => 'Catalog'],
                ['id' => 'search', 'name' => 'Search'],
                ['id' => 'catalog_search', 'name' => 'Catalog & Search'],
            ],
            'statusOptions' => [
                ['id' => 'enabled', 'name' => 'Enabled'],
                ['id' => 'disabled', 'name' => 'Disabled'],
            ]
        ]);
    }

    /**
     * Update the specified product in storage.
     *
     * @param ProductUpdateRequest $request
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        try {
            $this->productService->updateProduct($product, $request->validated());

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'The product has been updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified product from storage.
     *
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        try {
            $this->productService->deleteProduct($product);

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'The product was successfully deleted.');
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