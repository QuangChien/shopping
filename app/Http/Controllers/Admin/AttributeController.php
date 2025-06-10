<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Models\AttributeOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AttributeController extends Controller
{
    /**
     * Display a listing of the attributes.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'backend_type', 'frontend_input', 'is_filterable']);

        $query = ProductAttribute::query();

        // Apply search filter
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('code', 'like', $searchTerm)
                  ->orWhere('frontend_label', 'like', $searchTerm);
            });
        }

        // Apply backend type filter
        if (!empty($filters['backend_type'])) {
            $query->where('backend_type', $filters['backend_type']);
        }

        // Apply frontend input filter
        if (!empty($filters['frontend_input'])) {
            $query->where('frontend_input', $filters['frontend_input']);
        }

        // Apply is_filterable filter
        if (isset($filters['is_filterable']) && $filters['is_filterable'] !== '') {
            $query->where('is_filterable', (bool) $filters['is_filterable']);
        }

        $attributes = $query->orderBy('sort_order')
                           ->orderBy('frontend_label')
                           ->paginate(15)
                           ->withQueryString();

        return Inertia::render('Admin/Attributes/Index', [
            'attributes' => $attributes,
            'filters' => $filters,
            'backendTypes' => [
                ['id' => 'varchar', 'name' => 'Text (Short)'],
                ['id' => 'text', 'name' => 'Text (Long)'],
                ['id' => 'int', 'name' => 'Integer'],
                ['id' => 'decimal', 'name' => 'Decimal'],
                ['id' => 'datetime', 'name' => 'Date/Time'],
                ['id' => 'boolean', 'name' => 'Boolean'],
            ],
            'frontendInputs' => [
                ['id' => 'text', 'name' => 'Text Field'],
                ['id' => 'textarea', 'name' => 'Text Area'],
                ['id' => 'select', 'name' => 'Dropdown'],
                ['id' => 'multiselect', 'name' => 'Multiple Select'],
                ['id' => 'boolean', 'name' => 'Yes/No'],
                ['id' => 'date', 'name' => 'Date'],
                ['id' => 'datetime', 'name' => 'Date & Time'],
                ['id' => 'price', 'name' => 'Price'],
                ['id' => 'weight', 'name' => 'Weight'],
                ['id' => 'media_image', 'name' => 'Media Image'],
            ],
        ]);
    }

    /**
     * Show the form for creating a new attribute.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Admin/Attributes/Create', [
            'backendTypes' => [
                ['id' => 'varchar', 'name' => 'Text (Short)'],
                ['id' => 'text', 'name' => 'Text (Long)'],
                ['id' => 'int', 'name' => 'Integer'],
                ['id' => 'decimal', 'name' => 'Decimal'],
                ['id' => 'datetime', 'name' => 'Date/Time'],
                ['id' => 'boolean', 'name' => 'Boolean'],
            ],
            'frontendInputs' => [
                ['id' => 'text', 'name' => 'Text Field'],
                ['id' => 'textarea', 'name' => 'Text Area'],
                ['id' => 'select', 'name' => 'Dropdown'],
                ['id' => 'multiselect', 'name' => 'Multiple Select'],
                ['id' => 'boolean', 'name' => 'Yes/No'],
                ['id' => 'date', 'name' => 'Date'],
                ['id' => 'datetime', 'name' => 'Date & Time'],
                ['id' => 'price', 'name' => 'Price'],
                ['id' => 'weight', 'name' => 'Weight'],
                ['id' => 'media_image', 'name' => 'Media Image'],
            ],
        ]);
    }

    /**
     * Store a newly created attribute in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:product_attributes,code|regex:/^[a-z][a-z0-9_]*$/',
            'backend_type' => 'required|in:varchar,text,int,decimal,datetime,boolean',
            'frontend_input' => 'required|in:text,textarea,select,multiselect,boolean,date,datetime,price,weight,media_image',
            'frontend_label' => 'required|string|max:255',
            'is_required' => 'boolean',
            'is_unique' => 'boolean',
            'is_filterable' => 'boolean',
            'is_searchable' => 'boolean',
            'is_comparable' => 'boolean',
            'is_visible_on_front' => 'boolean',
            'sort_order' => 'integer|min:0',
            'default_value' => 'nullable|string',
            'frontend_class' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        // Set default values for boolean fields
        foreach (['is_required', 'is_unique', 'is_filterable', 'is_searchable', 'is_comparable', 'is_visible_on_front'] as $field) {
            $validated[$field] = $validated[$field] ?? false;
        }

        DB::transaction(function () use ($validated) {
            ProductAttribute::create($validated);
        });

        return redirect()
            ->route('admin.attributes.index')
            ->with('success', 'Attribute created successfully.');
    }

    /**
     * Show the form for editing the specified attribute.
     *
     * @param ProductAttribute $attribute
     * @return \Inertia\Response
     */
    public function edit(ProductAttribute $attribute)
    {
        $attribute->load('options');

        return Inertia::render('Admin/Attributes/Edit', [
            'attribute' => $attribute,
            'backendTypes' => [
                ['id' => 'varchar', 'name' => 'Text (Short)'],
                ['id' => 'text', 'name' => 'Text (Long)'],
                ['id' => 'int', 'name' => 'Integer'],
                ['id' => 'decimal', 'name' => 'Decimal'],
                ['id' => 'datetime', 'name' => 'Date/Time'],
                ['id' => 'boolean', 'name' => 'Boolean'],
            ],
            'frontendInputs' => [
                ['id' => 'text', 'name' => 'Text Field'],
                ['id' => 'textarea', 'name' => 'Text Area'],
                ['id' => 'select', 'name' => 'Dropdown'],
                ['id' => 'multiselect', 'name' => 'Multiple Select'],
                ['id' => 'boolean', 'name' => 'Yes/No'],
                ['id' => 'date', 'name' => 'Date'],
                ['id' => 'datetime', 'name' => 'Date & Time'],
                ['id' => 'price', 'name' => 'Price'],
                ['id' => 'weight', 'name' => 'Weight'],
                ['id' => 'media_image', 'name' => 'Media Image'],
            ],
        ]);
    }

    /**
     * Update the specified attribute in storage.
     *
     * @param Request $request
     * @param ProductAttribute $attribute
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ProductAttribute $attribute)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:100', 'regex:/^[a-z][a-z0-9_]*$/', Rule::unique('product_attributes')->ignore($attribute)],
            'backend_type' => 'required|in:varchar,text,int,decimal,datetime,boolean',
            'frontend_input' => 'required|in:text,textarea,select,multiselect,boolean,date,datetime,price,weight,media_image',
            'frontend_label' => 'required|string|max:255',
            'is_required' => 'boolean',
            'is_unique' => 'boolean',
            'is_filterable' => 'boolean',
            'is_searchable' => 'boolean',
            'is_comparable' => 'boolean',
            'is_visible_on_front' => 'boolean',
            'sort_order' => 'integer|min:0',
            'default_value' => 'nullable|string',
            'frontend_class' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        // Set default values for boolean fields
        foreach (['is_required', 'is_unique', 'is_filterable', 'is_searchable', 'is_comparable', 'is_visible_on_front'] as $field) {
            $validated[$field] = $validated[$field] ?? false;
        }

        DB::transaction(function () use ($attribute, $validated) {
            $attribute->update($validated);
        });

        return redirect()
            ->route('admin.attributes.index')
            ->with('success', 'Attribute updated successfully.');
    }

    /**
     * Remove the specified attribute from storage.
     *
     * @param ProductAttribute $attribute
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ProductAttribute $attribute)
    {
        try {
            DB::transaction(function () use ($attribute) {
                $attribute->delete();
            });

            return redirect()
                ->route('admin.attributes.index')
                ->with('success', 'Attribute deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete this attribute. It is being used by products.');
        }
    }

    /**
     * Display the options for the specified attribute.
     *
     * @param ProductAttribute $attribute
     * @return \Inertia\Response
     */
    public function options(ProductAttribute $attribute)
    {
        $attribute->load('options');

        return Inertia::render('Admin/Attributes/Options', [
            'attribute' => $attribute,
        ]);
    }

    /**
     * Store a new option for the specified attribute.
     *
     * @param Request $request
     * @param ProductAttribute $attribute
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeOption(Request $request, ProductAttribute $attribute)
    {
        $validated = $request->validate([
            'value' => ['required', 'string', 'max:255', Rule::unique('attribute_options')->where('attribute_id', $attribute->id)],
            'label' => 'nullable|string|max:255',
            'swatch' => 'nullable|string|max:255',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['attribute_id'] = $attribute->id;
        
        if (empty($validated['label'])) {
            $validated['label'] = $validated['value'];
        }

        DB::transaction(function () use ($validated) {
            AttributeOption::create($validated);
        });

        return redirect()
            ->route('admin.attributes.options', $attribute)
            ->with('success', 'Option created successfully.');
    }

    /**
     * Update the specified option.
     *
     * @param Request $request
     * @param ProductAttribute $attribute
     * @param AttributeOption $option
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateOption(Request $request, ProductAttribute $attribute, AttributeOption $option)
    {
        // Check if the option belongs to the attribute
        if ($option->attribute_id !== $attribute->id) {
            abort(404);
        }

        $validated = $request->validate([
            'value' => ['required', 'string', 'max:255', Rule::unique('attribute_options')->where('attribute_id', $attribute->id)->ignore($option)],
            'label' => 'nullable|string|max:255',
            'swatch' => 'nullable|string|max:255',
            'sort_order' => 'integer|min:0',
        ]);

        if (empty($validated['label'])) {
            $validated['label'] = $validated['value'];
        }

        DB::transaction(function () use ($option, $validated) {
            $option->update($validated);
        });

        return redirect()
            ->route('admin.attributes.options', $attribute)
            ->with('success', 'Option updated successfully.');
    }

    /**
     * Remove the specified option.
     *
     * @param ProductAttribute $attribute
     * @param AttributeOption $option
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyOption(ProductAttribute $attribute, AttributeOption $option)
    {
        // Check if the option belongs to the attribute
        if ($option->attribute_id !== $attribute->id) {
            abort(404);
        }

        try {
            DB::transaction(function () use ($option) {
                $option->delete();
            });

            return redirect()
                ->route('admin.attributes.options', $attribute)
                ->with('success', 'Option deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete this option. It is being used by products.');
        }
    }
} 