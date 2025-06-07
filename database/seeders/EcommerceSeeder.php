<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EcommerceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear all data first to avoid foreign key conflicts
        $this->clearAllData();
        
        // Seed core data first
        $this->seedProductTaxClasses();
        $this->seedCategories();
        $this->seedProductAttributes();
        $this->seedOrderStatuses();
        $this->seedShippingMethods();
        $this->seedPaymentMethods();
        $this->seedTaxes();
        $this->seedCoupons();
        
        // Seed customers first (required for foreign keys)
        $this->seedCustomers();
        
        // Seed user related data
        $this->seedCustomerAddresses();
        
        // Seed product related data
        $this->seedProducts();
        $this->seedProductCategories();
        $this->seedProductMedia();
        $this->seedInventory();
        
        // Seed order related data (only seedSampleOrders, remove duplicate seedOrderItems)
        $this->seedSampleOrders();
        $this->seedOrderTaxes();
        $this->seedOrderCoupons();
        
        // Seed customer related data
        $this->seedReviews();
        $this->seedWishlists();
        $this->seedCompareLists();
        
        // Seed settings last
        $this->seedSettings();
    }

    private function clearAllData(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear all tables in the correct order (child tables first)
        DB::table('compare_list_items')->truncate();
        DB::table('compare_lists')->truncate();
        DB::table('wishlist_items')->truncate();
        DB::table('wishlists')->truncate();
        DB::table('reviews')->truncate();
        DB::table('order_coupons')->truncate();
        DB::table('order_taxes')->truncate();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        DB::table('inventory')->truncate();
        DB::table('product_media')->truncate();
        DB::table('product_categories')->truncate();
        DB::table('product_attribute_values_decimal')->truncate();
        DB::table('product_attribute_values_text')->truncate();
        DB::table('product_attribute_values_varchar')->truncate();
        DB::table('product_attribute_values_int')->truncate();
        DB::table('product_attribute_values_datetime')->truncate();
        DB::table('product_attribute_values_boolean')->truncate();
        DB::table('products')->truncate();
        DB::table('customer_addresses')->truncate();
        DB::table('customers')->truncate();
        DB::table('settings')->truncate();
        DB::table('coupons')->truncate();
        DB::table('taxes')->truncate();
        DB::table('payment_methods')->truncate();
        DB::table('shipping_methods')->truncate();
        DB::table('order_statuses')->truncate();
        DB::table('product_attributes')->truncate();
        DB::table('categories')->truncate();
        DB::table('product_tax_classes')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function seedCustomers(): void
    {
        DB::table('customers')->insert([
            [
                'id' => 1,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password123'),
                'phone' => '+1-555-0123',
                'date_of_birth' => '1990-05-15',
                'gender' => 'male',
                'is_active' => true,
                'last_login_at' => now()->subDays(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password123'),
                'phone' => '+1-555-0456',
                'date_of_birth' => '1985-08-22',
                'gender' => 'female',
                'is_active' => true,
                'last_login_at' => now()->subDays(1),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    private function seedCustomerAddresses(): void
    {
        DB::table('customer_addresses')->insert([
            [
                'customer_id' => 1,
                'address_type' => 'billing',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'street_address_1' => '123 Main Street',
                'street_address_2' => 'Apt 4B',
                'city' => 'New York',
                'state_province' => 'NY',
                'postal_code' => '10001',
                'country_code' => 'US',
                'phone' => '+1-555-0123',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_id' => 1,
                'address_type' => 'shipping',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'street_address_1' => '456 Oak Avenue',
                'street_address_2' => null,
                'city' => 'Brooklyn',
                'state_province' => 'NY',
                'postal_code' => '11201',
                'country_code' => 'US',
                'phone' => '+1-555-0123',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_id' => 2,
                'address_type' => 'billing',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'street_address_1' => '789 Pine Street',
                'street_address_2' => null,
                'city' => 'Los Angeles',
                'state_province' => 'CA',
                'postal_code' => '90210',
                'country_code' => 'US',
                'phone' => '+1-555-0456',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    private function seedProductTaxClasses(): void
    {
        DB::table('product_tax_classes')->insert([['name' => 'Standard Tax', 'description' => 'Standard tax rate for most products', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()], ['name' => 'Reduced Tax', 'description' => 'Reduced tax rate for specific items', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()], ['name' => 'No Tax', 'description' => 'Tax-free products', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],]);
    }

    private function seedCategories(): void
    {
        DB::table('categories')->insert([// Root categories
            ['id' => 1, 'parent_id' => null, 'name' => 'Men\'s Fashion', 'slug' => 'mens-fashion', 'description' => 'Fashion and clothing for men', 'image' => '/images/categories/mens-fashion.jpg', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()], ['id' => 2, 'parent_id' => null, 'name' => 'Women\'s Fashion', 'slug' => 'womens-fashion', 'description' => 'Fashion and clothing for women', 'image' => '/images/categories/womens-fashion.jpg', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()], ['id' => 3, 'parent_id' => null, 'name' => 'Accessories', 'slug' => 'accessories', 'description' => 'Fashion accessories and jewelry', 'image' => '/images/categories/accessories.jpg', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()], ['id' => 4, 'parent_id' => null, 'name' => 'Shoes', 'slug' => 'shoes', 'description' => 'Footwear for all occasions', 'image' => '/images/categories/shoes.jpg', 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Men's subcategories
            ['id' => 5, 'parent_id' => 1, 'name' => 'T-Shirts', 'slug' => 'mens-t-shirts', 'description' => 'Men\'s casual and formal t-shirts', 'image' => '/images/categories/mens-tshirts.jpg', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()], ['id' => 6, 'parent_id' => 1, 'name' => 'Jeans', 'slug' => 'mens-jeans', 'description' => 'Men\'s denim jeans and pants', 'image' => '/images/categories/mens-jeans.jpg', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()], ['id' => 7, 'parent_id' => 1, 'name' => 'Shirts', 'slug' => 'mens-shirts', 'description' => 'Men\'s formal and casual shirts', 'image' => '/images/categories/mens-shirts.jpg', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Women's subcategories
            ['id' => 8, 'parent_id' => 2, 'name' => 'Dresses', 'slug' => 'womens-dresses', 'description' => 'Women\'s casual and formal dresses', 'image' => '/images/categories/womens-dresses.jpg', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()], ['id' => 9, 'parent_id' => 2, 'name' => 'Tops', 'slug' => 'womens-tops', 'description' => 'Women\'s blouses and tops', 'image' => '/images/categories/womens-tops.jpg', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()], ['id' => 10, 'parent_id' => 2, 'name' => 'Skirts', 'slug' => 'womens-skirts', 'description' => 'Women\'s skirts and bottoms', 'image' => '/images/categories/womens-skirts.jpg', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Accessories subcategories
            ['id' => 11, 'parent_id' => 3, 'name' => 'Bags', 'slug' => 'bags', 'description' => 'Handbags, backpacks and purses', 'image' => '/images/categories/bags.jpg', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()], ['id' => 12, 'parent_id' => 3, 'name' => 'Jewelry', 'slug' => 'jewelry', 'description' => 'Necklaces, rings and earrings', 'image' => '/images/categories/jewelry.jpg', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()], ['id' => 13, 'parent_id' => 3, 'name' => 'Watches', 'slug' => 'watches', 'description' => 'Fashion and luxury watches', 'image' => '/images/categories/watches.jpg', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Shoes subcategories
            ['id' => 14, 'parent_id' => 4, 'name' => 'Sneakers', 'slug' => 'sneakers', 'description' => 'Casual and athletic sneakers', 'image' => '/images/categories/sneakers.jpg', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()], ['id' => 15, 'parent_id' => 4, 'name' => 'Boots', 'slug' => 'boots', 'description' => 'Fashion and work boots', 'image' => '/images/categories/boots.jpg', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],]);
    }

    private function seedProductAttributes(): void
    {
        DB::table('product_attributes')->insert([['id' => 1, 'code' => 'name', 'backend_type' => 'varchar', 'frontend_input' => 'text', 'frontend_label' => 'Product Name', 'is_required' => true, 'is_filterable' => false, 'is_searchable' => true, 'is_visible_on_front' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()], ['id' => 2, 'code' => 'description', 'backend_type' => 'text', 'frontend_input' => 'textarea', 'frontend_label' => 'Description', 'is_required' => false, 'is_filterable' => false, 'is_searchable' => true, 'is_visible_on_front' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()], ['id' => 3, 'code' => 'short_description', 'backend_type' => 'text', 'frontend_input' => 'textarea', 'frontend_label' => 'Short Description', 'is_required' => false, 'is_filterable' => false, 'is_searchable' => true, 'is_visible_on_front' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()], ['id' => 4, 'code' => 'price', 'backend_type' => 'decimal', 'frontend_input' => 'price', 'frontend_label' => 'Price', 'is_required' => true, 'is_filterable' => true, 'is_searchable' => false, 'is_visible_on_front' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()], ['id' => 5, 'code' => 'special_price', 'backend_type' => 'decimal', 'frontend_input' => 'price', 'frontend_label' => 'Special Price', 'is_required' => false, 'is_filterable' => true, 'is_searchable' => false, 'is_visible_on_front' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()], ['id' => 6, 'code' => 'weight', 'backend_type' => 'decimal', 'frontend_input' => 'weight', 'frontend_label' => 'Weight', 'is_required' => false, 'is_filterable' => false, 'is_searchable' => false, 'is_visible_on_front' => true, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()], ['id' => 7, 'code' => 'color', 'backend_type' => 'varchar', 'frontend_input' => 'select', 'frontend_label' => 'Color', 'is_required' => false, 'is_filterable' => true, 'is_searchable' => true, 'is_visible_on_front' => true, 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()], ['id' => 8, 'code' => 'size', 'backend_type' => 'varchar', 'frontend_input' => 'select', 'frontend_label' => 'Size', 'is_required' => false, 'is_filterable' => true, 'is_searchable' => true, 'is_visible_on_front' => true, 'sort_order' => 8, 'created_at' => now(), 'updated_at' => now()], ['id' => 9, 'code' => 'material', 'backend_type' => 'varchar', 'frontend_input' => 'text', 'frontend_label' => 'Material', 'is_required' => false, 'is_filterable' => true, 'is_searchable' => true, 'is_visible_on_front' => true, 'sort_order' => 9, 'created_at' => now(), 'updated_at' => now()], ['id' => 10, 'code' => 'brand', 'backend_type' => 'varchar', 'frontend_input' => 'text', 'frontend_label' => 'Brand', 'is_required' => false, 'is_filterable' => true, 'is_searchable' => true, 'is_visible_on_front' => true, 'sort_order' => 10, 'created_at' => now(), 'updated_at' => now()],]);
    }

    private function seedOrderStatuses(): void
    {
        DB::table('order_statuses')->insert([['id' => 1, 'name' => 'pending', 'label' => 'Pending', 'color' => '#FFA500', 'is_default' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()], ['id' => 2, 'name' => 'processing', 'label' => 'Processing', 'color' => '#0066CC', 'is_default' => false, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()], ['id' => 3, 'name' => 'shipped', 'label' => 'Shipped', 'color' => '#9900CC', 'is_default' => false, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()], ['id' => 4, 'name' => 'delivered', 'label' => 'Delivered', 'color' => '#009900', 'is_default' => false, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()], ['id' => 5, 'name' => 'cancelled', 'label' => 'Cancelled', 'color' => '#CC0000', 'is_default' => false, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],]);
    }

    private function seedShippingMethods(): void
    {
        DB::table('shipping_methods')->insert([['id' => 1, 'name' => 'Standard Shipping', 'code' => 'standard', 'description' => 'Standard shipping 5-7 business days', 'price' => 9.99, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()], ['id' => 2, 'name' => 'Express Shipping', 'code' => 'express', 'description' => 'Express shipping 2-3 business days', 'price' => 19.99, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()], ['id' => 3, 'name' => 'Overnight Shipping', 'code' => 'overnight', 'description' => 'Next business day delivery', 'price' => 39.99, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()], ['id' => 4, 'name' => 'Free Shipping', 'code' => 'free', 'description' => 'Free shipping for orders over $100', 'price' => 0.00, 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],]);
    }

    private function seedPaymentMethods(): void
    {
        DB::table('payment_methods')->insert([['id' => 1, 'name' => 'Credit Card', 'code' => 'credit_card', 'description' => 'Pay with credit or debit card', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()], ['id' => 2, 'name' => 'PayPal', 'code' => 'paypal', 'description' => 'Pay with PayPal account', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()], ['id' => 3, 'name' => 'Bank Transfer', 'code' => 'bank_transfer', 'description' => 'Direct bank transfer', 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()], ['id' => 4, 'name' => 'Cash on Delivery', 'code' => 'cod', 'description' => 'Pay when you receive your order', 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],]);
    }

    private function seedTaxes(): void
    {
        DB::table('taxes')->insert([
            [
                'name' => 'US Federal Tax',
                'rate' => 0.0000,
                'country_code' => 'US',
                'state_code' => null,
                'city' => null,
                'zip_code' => null,
                'is_compound' => false,
                'priority' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'New York State Tax',
                'rate' => 8.2500,
                'country_code' => 'US',
                'state_code' => 'NY',
                'city' => null,
                'zip_code' => null,
                'is_compound' => false,
                'priority' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'NYC Local Tax',
                'rate' => 4.5000,
                'country_code' => 'US',
                'state_code' => 'NY',
                'city' => 'New York',
                'zip_code' => null,
                'is_compound' => false,
                'priority' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'California State Tax',
                'rate' => 7.2500,
                'country_code' => 'US',
                'state_code' => 'CA',
                'city' => null,
                'zip_code' => null,
                'is_compound' => false,
                'priority' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Los Angeles County Tax',
                'rate' => 2.2500,
                'country_code' => 'US',
                'state_code' => 'CA',
                'city' => 'Los Angeles',
                'zip_code' => null,
                'is_compound' => false,
                'priority' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Beverly Hills Special Tax',
                'rate' => 1.5000,
                'country_code' => 'US',
                'state_code' => 'CA',
                'city' => 'Beverly Hills',
                'zip_code' => '90210',
                'is_compound' => false,
                'priority' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Texas State Tax',
                'rate' => 6.2500,
                'country_code' => 'US',
                'state_code' => 'TX',
                'city' => null,
                'zip_code' => null,
                'is_compound' => false,
                'priority' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Florida State Tax',
                'rate' => 6.0000,
                'country_code' => 'US',
                'state_code' => 'FL',
                'city' => null,
                'zip_code' => null,
                'is_compound' => false,
                'priority' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Canada GST',
                'rate' => 5.0000,
                'country_code' => 'CA',
                'state_code' => null,
                'city' => null,
                'zip_code' => null,
                'is_compound' => false,
                'priority' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ontario HST',
                'rate' => 13.0000,
                'country_code' => 'CA',
                'state_code' => 'ON',
                'city' => null,
                'zip_code' => null,
                'is_compound' => true,
                'priority' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    private function seedCoupons(): void
    {
        DB::table('coupons')->insert([
            [
                'code' => 'WELCOME10',
                'type' => 'percent_cart',
                'amount' => 10.0000,
                'minimum_amount' => 50.0000,
                'maximum_amount' => null,
                'usage_limit_per_coupon' => 100,
                'usage_limit_per_customer' => 1,
                'used_count' => 5,
                'individual_use' => false,
                'exclude_sale_items' => false,
                'starts_at' => now()->subDays(30),
                'expires_at' => now()->addDays(30),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SAVE20',
                'type' => 'fixed_cart',
                'amount' => 20.0000,
                'minimum_amount' => 100.0000,
                'maximum_amount' => null,
                'usage_limit_per_coupon' => null,
                'usage_limit_per_customer' => null,
                'used_count' => 12,
                'individual_use' => false,
                'exclude_sale_items' => true,
                'starts_at' => now()->subDays(7),
                'expires_at' => now()->addDays(14),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'PRODUCT15',
                'type' => 'percent_product',
                'amount' => 15.0000,
                'minimum_amount' => null,
                'maximum_amount' => 50.0000,
                'usage_limit_per_coupon' => 50,
                'usage_limit_per_customer' => 3,
                'used_count' => 0,
                'individual_use' => true,
                'exclude_sale_items' => false,
                'starts_at' => now(),
                'expires_at' => now()->addDays(60),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    private function seedProducts(): void
    {
        // Insert basic product records
        DB::table('products')->insert([
            [
                'id' => 1,
                'sku' => 'TSH-001',
                'tax_class_id' => 1,
                'status' => 'enabled',
                'visibility' => 'catalog_search',
                'type_id' => 'simple',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'sku' => 'JNS-001',
                'tax_class_id' => 1,
                'status' => 'enabled',
                'visibility' => 'catalog_search',
                'type_id' => 'simple',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'sku' => 'DRS-001',
                'tax_class_id' => 1,
                'status' => 'enabled',
                'visibility' => 'catalog_search',
                'type_id' => 'simple',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'sku' => 'BAG-001',
                'tax_class_id' => 1,
                'status' => 'enabled',
                'visibility' => 'catalog_search',
                'type_id' => 'simple',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'sku' => 'WTH-001',
                'tax_class_id' => 1,
                'status' => 'enabled',
                'visibility' => 'catalog_search',
                'type_id' => 'simple',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insert product attribute values (VARCHAR)
        DB::table('product_attribute_values_varchar')->insert([
            // Product 1 - Classic Cotton T-Shirt
            ['product_id' => 1, 'attribute_id' => 1, 'value' => 'Classic Cotton T-Shirt'],
            ['product_id' => 1, 'attribute_id' => 7, 'value' => 'White'],
            ['product_id' => 1, 'attribute_id' => 8, 'value' => 'M'],
            ['product_id' => 1, 'attribute_id' => 9, 'value' => '100% Cotton'],
            ['product_id' => 1, 'attribute_id' => 10, 'value' => 'Fashion Brand'],
            
            // Product 2 - Slim Fit Denim Jeans
            ['product_id' => 2, 'attribute_id' => 1, 'value' => 'Slim Fit Denim Jeans'],
            ['product_id' => 2, 'attribute_id' => 7, 'value' => 'Blue'],
            ['product_id' => 2, 'attribute_id' => 8, 'value' => '32'],
            ['product_id' => 2, 'attribute_id' => 9, 'value' => 'Denim'],
            ['product_id' => 2, 'attribute_id' => 10, 'value' => 'Denim Co'],
            
            // Product 3 - Elegant Summer Dress
            ['product_id' => 3, 'attribute_id' => 1, 'value' => 'Elegant Summer Dress'],
            ['product_id' => 3, 'attribute_id' => 7, 'value' => 'Floral'],
            ['product_id' => 3, 'attribute_id' => 8, 'value' => 'S'],
            ['product_id' => 3, 'attribute_id' => 9, 'value' => 'Polyester'],
            ['product_id' => 3, 'attribute_id' => 10, 'value' => 'Summer Style'],
            
            // Product 4 - Leather Crossbody Bag
            ['product_id' => 4, 'attribute_id' => 1, 'value' => 'Leather Crossbody Bag'],
            ['product_id' => 4, 'attribute_id' => 7, 'value' => 'Brown'],
            ['product_id' => 4, 'attribute_id' => 9, 'value' => 'Genuine Leather'],
            ['product_id' => 4, 'attribute_id' => 10, 'value' => 'Luxury Bags'],
            
            // Product 5 - Classic Analog Watch
            ['product_id' => 5, 'attribute_id' => 1, 'value' => 'Classic Analog Watch'],
            ['product_id' => 5, 'attribute_id' => 7, 'value' => 'Silver'],
            ['product_id' => 5, 'attribute_id' => 9, 'value' => 'Stainless Steel'],
            ['product_id' => 5, 'attribute_id' => 10, 'value' => 'TimeKeeper'],
        ]);

        // Insert product attribute values (TEXT)
        DB::table('product_attribute_values_text')->insert([
            // Descriptions
            ['product_id' => 1, 'attribute_id' => 2, 'value' => 'Comfortable and stylish cotton t-shirt perfect for everyday wear. Made from 100% premium cotton with a soft feel and durable construction.'],
            ['product_id' => 1, 'attribute_id' => 3, 'value' => 'Premium cotton t-shirt for everyday comfort'],
            
            ['product_id' => 2, 'attribute_id' => 2, 'value' => 'Modern slim fit jeans crafted from premium denim. Features a comfortable stretch blend and classic five-pocket styling.'],
            ['product_id' => 2, 'attribute_id' => 3, 'value' => 'Stylish slim fit jeans with comfortable stretch'],
            
            ['product_id' => 3, 'attribute_id' => 2, 'value' => 'Beautiful flowing summer dress perfect for warm weather. Made from lightweight breathable fabric with an elegant design.'],
            ['product_id' => 3, 'attribute_id' => 3, 'value' => 'Lightweight and elegant dress for summer'],
            
            ['product_id' => 4, 'attribute_id' => 2, 'value' => 'Premium leather crossbody bag with multiple compartments. Perfect for daily use with a timeless design.'],
            ['product_id' => 4, 'attribute_id' => 3, 'value' => 'Stylish leather bag for everyday use'],
            
            ['product_id' => 5, 'attribute_id' => 2, 'value' => 'Timeless analog watch with stainless steel case and leather strap. Water resistant and perfect for any occasion.'],
            ['product_id' => 5, 'attribute_id' => 3, 'value' => 'Elegant analog watch with leather strap'],
        ]);

        // Insert product attribute values (DECIMAL)
        DB::table('product_attribute_values_decimal')->insert([
            // Prices
            ['product_id' => 1, 'attribute_id' => 4, 'value' => 29.99],
            ['product_id' => 1, 'attribute_id' => 5, 'value' => 24.99],
            ['product_id' => 1, 'attribute_id' => 6, 'value' => 0.25],
            
            ['product_id' => 2, 'attribute_id' => 4, 'value' => 79.99],
            ['product_id' => 2, 'attribute_id' => 6, 'value' => 0.80],
            
            ['product_id' => 3, 'attribute_id' => 4, 'value' => 89.99],
            ['product_id' => 3, 'attribute_id' => 5, 'value' => 69.99],
            ['product_id' => 3, 'attribute_id' => 6, 'value' => 0.35],
            
            ['product_id' => 4, 'attribute_id' => 4, 'value' => 129.99],
            ['product_id' => 4, 'attribute_id' => 6, 'value' => 0.60],
            
            ['product_id' => 5, 'attribute_id' => 4, 'value' => 199.99],
            ['product_id' => 5, 'attribute_id' => 5, 'value' => 149.99],
            ['product_id' => 5, 'attribute_id' => 6, 'value' => 0.15],
        ]);
    }

    private function seedProductCategories(): void
    {
        DB::table('product_categories')->insert([['product_id' => 1, 'category_id' => 5], // Classic Cotton T-Shirt -> T-Shirts
            ['product_id' => 2, 'category_id' => 6], // Slim Fit Denim Jeans -> Jeans
            ['product_id' => 3, 'category_id' => 8], // Elegant Summer Dress -> Dresses
            ['product_id' => 4, 'category_id' => 11], // Leather Crossbody Bag -> Bags
            ['product_id' => 5, 'category_id' => 13], // Classic Analog Watch -> Watches
        ]);
    }

    private function seedProductMedia(): void
    {
        DB::table('product_media')->insert([
            [
                'product_id' => 1,
                'type' => 'image',
                'path' => '/images/products/tshirt-1.jpg',
                'alt_text' => 'Classic White T-Shirt',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 1,
                'type' => 'image',
                'path' => '/images/products/tshirt-1-back.jpg',
                'alt_text' => 'Classic White T-Shirt Back View',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    private function seedInventory(): void
    {
        DB::table('inventory')->insert([
            [
                'product_id' => 1,
                'quantity' => 150,
                'reserved_quantity' => 0,
                'min_quantity' => 10,
                'max_quantity' => 500,
                'is_in_stock' => true,
                'manage_stock' => true,
                'backorders' => 'no',
                'updated_at' => now(),
            ],
            [
                'product_id' => 2,
                'quantity' => 75,
                'reserved_quantity' => 0,
                'min_quantity' => 5,
                'max_quantity' => 200,
                'is_in_stock' => true,
                'manage_stock' => true,
                'backorders' => 'no',
                'updated_at' => now(),
            ],
            [
                'product_id' => 3,
                'quantity' => 45,
                'reserved_quantity' => 0,
                'min_quantity' => 3,
                'max_quantity' => 100,
                'is_in_stock' => true,
                'manage_stock' => true,
                'backorders' => 'notify',
                'updated_at' => now(),
            ],
            [
                'product_id' => 4,
                'quantity' => 30,
                'reserved_quantity' => 0,
                'min_quantity' => 2,
                'max_quantity' => 50,
                'is_in_stock' => true,
                'manage_stock' => true,
                'backorders' => 'no',
                'updated_at' => now(),
            ],
            [
                'product_id' => 5,
                'quantity' => 25,
                'reserved_quantity' => 0,
                'min_quantity' => 2,
                'max_quantity' => 40,
                'is_in_stock' => true,
                'manage_stock' => true,
                'backorders' => 'no',
                'updated_at' => now(),
            ],
        ]);
    }

    private function seedSampleOrders(): void
    {
        // Insert sample orders
        DB::table('orders')->insert([
            [
                'id' => 1, 
                'order_number' => 'ORD-2024-001', 
                'customer_id' => 1, 
                'status_id' => 2, // processing
                'currency_code' => 'USD', 
                'subtotal' => 109.98, 
                'tax_amount' => 9.07, 
                'shipping_amount' => 9.99, 
                'discount_amount' => 10.00, 
                'total' => 119.04, 
                'payment_method_id' => 1, 
                'payment_status' => 'paid', 
                'shipping_method_id' => 1, 
                'billing_address' => '{"first_name":"John","last_name":"Doe","street_address_1":"123 Main Street","street_address_2":"Apt 4B","city":"New York","state_province":"NY","postal_code":"10001","country_code":"US","phone":"+1-555-0123"}', 
                'shipping_address' => '{"first_name":"John","last_name":"Doe","street_address_1":"456 Oak Avenue","city":"Brooklyn","state_province":"NY","postal_code":"11201","country_code":"US","phone":"+1-555-0123"}', 
                'notes' => 'Please handle with care', 
                'ordered_at' => now()->subDays(5),
                'created_at' => now()->subDays(5), 
                'updated_at' => now()->subDays(4)
            ], 
            [
                'id' => 2, 
                'order_number' => 'ORD-2024-002', 
                'customer_id' => 2, 
                'status_id' => 4, // delivered
                'currency_code' => 'USD', 
                'subtotal' => 219.98, 
                'tax_amount' => 18.15, 
                'shipping_amount' => 0.00, 
                'discount_amount' => 20.00, 
                'total' => 218.13, 
                'payment_method_id' => 2, 
                'payment_status' => 'paid', 
                'shipping_method_id' => 4, 
                'billing_address' => '{"first_name":"Jane","last_name":"Smith","street_address_1":"789 Pine Street","city":"Los Angeles","state_province":"CA","postal_code":"90210","country_code":"US","phone":"+1-555-0456"}', 
                'shipping_address' => '{"first_name":"Jane","last_name":"Smith","street_address_1":"789 Pine Street","city":"Los Angeles","state_province":"CA","postal_code":"90210","country_code":"US","phone":"+1-555-0456"}', 
                'notes' => null, 
                'ordered_at' => now()->subDays(10),
                'created_at' => now()->subDays(10), 
                'updated_at' => now()->subDays(2)
            ]
        ]);

        // Insert order items
        DB::table('order_items')->insert([
            [
                'order_id' => 1,
                'product_id' => 1,
                'product_name' => 'Classic Cotton T-Shirt', 
                'product_sku' => 'TSH-001', 
                'quantity' => 2,
                'price' => 24.99, 
                'row_total' => 49.98, 
                'product_options' => null,
                'created_at' => now()->subDays(5), 
                'updated_at' => now()->subDays(5)
            ], 
            [
                'order_id' => 1, 
                'product_id' => 3, 
                'product_name' => 'Elegant Summer Dress', 
                'product_sku' => 'DRS-001', 
                'quantity' => 1, 
                'price' => 69.99, 
                'row_total' => 69.99, 
                'product_options' => null,
                'created_at' => now()->subDays(5), 
                'updated_at' => now()->subDays(5)
            ], 
            [
                'order_id' => 2, 
                'product_id' => 4, 
                'product_name' => 'Leather Crossbody Bag', 
                'product_sku' => 'BAG-001', 
                'quantity' => 1, 
                'price' => 129.99, 
                'row_total' => 129.99, 
                'product_options' => null,
                'created_at' => now()->subDays(10), 
                'updated_at' => now()->subDays(10)
            ], 
            [
                'order_id' => 2, 
                'product_id' => 5, 
                'product_name' => 'Classic Analog Watch', 
                'product_sku' => 'WTH-001', 
                'quantity' => 1, 
                'price' => 149.99, 
                'row_total' => 149.99, 
                'product_options' => null,
                'created_at' => now()->subDays(10), 
                'updated_at' => now()->subDays(10)
            ]
        ]);
    }



    private function seedOrderTaxes(): void
    {
        DB::table('order_taxes')->insert([
            [
                'order_id' => 1,
                'tax_id' => 2,
                'name' => 'New York State Tax',
                'rate' => 8.25,
                'amount' => 4.95,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    private function seedOrderCoupons(): void
    {
        DB::table('order_coupons')->insert([
            [
                'order_id' => 1,
                'coupon_id' => 1,
                'code' => 'WELCOME10',
                'discount_amount' => 5.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 2,
                'coupon_id' => 2,
                'code' => 'SAVE20',
                'discount_amount' => 20.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    private function seedReviews(): void
    {
        DB::table('reviews')->insert([
            [
                'product_id' => 1, 
                'customer_id' => 1, 
                'order_id' => 1,
                'rating' => 5, 
                'title' => 'Excellent Quality!', 
                'content' => 'This t-shirt is amazing! The fabric is soft and comfortable, and the fit is perfect. Highly recommend!', 
                'status' => 'approved',
                'helpful_count' => 0,
                'created_at' => now()->subDays(3), 
                'updated_at' => now()->subDays(3)
            ], 
            [
                'product_id' => 3, 
                'customer_id' => 1, 
                'order_id' => 1,
                'rating' => 4, 
                'title' => 'Beautiful dress', 
                'content' => 'Love this dress! Perfect for summer events. The material is lightweight and the design is elegant.', 
                'status' => 'approved',
                'helpful_count' => 2,
                'created_at' => now()->subDays(7), 
                'updated_at' => now()->subDays(7)
            ], 
            [
                'product_id' => 2, 
                'customer_id' => 2, 
                'order_id' => null,
                'rating' => 5, 
                'title' => 'Perfect fit jeans', 
                'content' => 'These jeans fit perfectly! Great quality denim and very comfortable to wear all day.', 
                'status' => 'approved',
                'helpful_count' => 1,
                'created_at' => now()->subDays(12), 
                'updated_at' => now()->subDays(12)
            ], 
            [
                'product_id' => 4, 
                'customer_id' => 2, 
                'order_id' => 2,
                'rating' => 5, 
                'title' => 'Love this bag!', 
                'content' => 'Beautiful leather bag with great craftsmanship. Perfect size for daily use and very stylish.', 
                'status' => 'approved',
                'helpful_count' => 3,
                'created_at' => now()->subDays(8), 
                'updated_at' => now()->subDays(8)
            ], 
            [
                'product_id' => 5, 
                'customer_id' => 2, 
                'order_id' => 2,
                'rating' => 4, 
                'title' => 'Classic and elegant', 
                'content' => 'Beautiful watch with classic design. Good quality materials and accurate timekeeping.', 
                'status' => 'approved',
                'helpful_count' => 0,
                'created_at' => now()->subDays(15), 
                'updated_at' => now()->subDays(15)
            ]
        ]);
    }

    private function seedWishlists(): void
    {
        // First create wishlists
        DB::table('wishlists')->insert([
            [
                'id' => 1,
                'customer_id' => 1,
                'name' => 'My Wishlist',
                'is_default' => true,
                'is_public' => false,
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20),
            ],
            [
                'id' => 2,
                'customer_id' => 2,
                'name' => 'My Wishlist',
                'is_default' => true,
                'is_public' => false,
                'created_at' => now()->subDays(25),
                'updated_at' => now()->subDays(25),
            ],
        ]);

        // Then create wishlist items
        DB::table('wishlist_items')->insert([
            [
                'wishlist_id' => 1,
                'product_id' => 2,
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20),
            ],
            [
                'wishlist_id' => 1,
                'product_id' => 4,
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ],
            [
                'wishlist_id' => 2,
                'product_id' => 1,
                'created_at' => now()->subDays(25),
                'updated_at' => now()->subDays(25),
            ],
            [
                'wishlist_id' => 2,
                'product_id' => 5,
                'created_at' => now()->subDays(18),
                'updated_at' => now()->subDays(18),
            ],
        ]);
    }

    private function seedCompareLists(): void
    {
        DB::table('compare_lists')->insert([
            [
                'customer_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('compare_list_items')->insert([
            [
                'compare_list_id' => 1,
                'product_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'compare_list_id' => 1,
                'product_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    private function seedSettings(): void
    {
        // Insert all essential settings
        DB::table('settings')->insert([// GENERAL STORE INFORMATION
            ['group_name' => 'general', 'key' => 'store_name', 'value' => 'Fashion Store', 'type' => 'string', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'general', 'key' => 'store_email', 'value' => 'info@fashionstore.com', 'type' => 'string', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'general', 'key' => 'store_phone', 'value' => '+1-800-FASHION', 'type' => 'string', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'general', 'key' => 'store_address', 'value' => '123 Fashion Avenue, Style City, SC 12345', 'type' => 'text', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'general', 'key' => 'admin_email', 'value' => 'admin@fashionstore.com', 'type' => 'string', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'general', 'key' => 'timezone', 'value' => 'America/New_York', 'type' => 'string', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],

            // CURRENCY SETTINGS
            ['group_name' => 'currency', 'key' => 'default_currency', 'value' => 'USD', 'type' => 'string', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'currency', 'key' => 'currency_symbol', 'value' => '$', 'type' => 'string', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'currency', 'key' => 'currency_position', 'value' => 'before', 'type' => 'string', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'currency', 'key' => 'decimal_places', 'value' => '2', 'type' => 'number', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],

            // CATALOG SETTINGS
            ['group_name' => 'catalog', 'key' => 'products_per_page', 'value' => '12', 'type' => 'number', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'catalog', 'key' => 'show_out_of_stock', 'value' => 'true', 'type' => 'boolean', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'catalog', 'key' => 'enable_reviews', 'value' => 'true', 'type' => 'boolean', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'catalog', 'key' => 'enable_wishlist', 'value' => 'true', 'type' => 'boolean', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'catalog', 'key' => 'low_stock_threshold', 'value' => '5', 'type' => 'number', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],

            // INVENTORY SETTINGS
            ['group_name' => 'inventory', 'key' => 'track_inventory', 'value' => 'true', 'type' => 'boolean', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'inventory', 'key' => 'allow_backorders', 'value' => 'false', 'type' => 'boolean', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'inventory', 'key' => 'auto_reduce_stock', 'value' => 'true', 'type' => 'boolean', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],

            // CART & CHECKOUT
            ['group_name' => 'cart', 'key' => 'enable_guest_checkout', 'value' => 'true', 'type' => 'boolean', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'cart', 'key' => 'cart_session_lifetime', 'value' => '7', 'type' => 'number', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'cart', 'key' => 'min_order_amount', 'value' => '0', 'type' => 'number', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],

            ['group_name' => 'checkout', 'key' => 'require_phone_number', 'value' => 'true', 'type' => 'boolean', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'checkout', 'key' => 'enable_order_notes', 'value' => 'true', 'type' => 'boolean', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'checkout', 'key' => 'enable_terms_and_conditions', 'value' => 'true', 'type' => 'boolean', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],

            // SHIPPING
            ['group_name' => 'shipping', 'key' => 'enable_shipping', 'value' => 'true', 'type' => 'boolean', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'shipping', 'key' => 'free_shipping_threshold', 'value' => '100.00', 'type' => 'number', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'shipping', 'key' => 'default_shipping_cost', 'value' => '9.99', 'type' => 'number', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],

            // TAX SETTINGS
            ['group_name' => 'tax', 'key' => 'enable_taxes', 'value' => 'true', 'type' => 'boolean', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'tax', 'key' => 'default_tax_rate', 'value' => '8.25', 'type' => 'number', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'tax', 'key' => 'tax_included_in_price', 'value' => 'false', 'type' => 'boolean', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],

            // PAYMENT METHODS
            ['group_name' => 'payment', 'key' => 'enable_cod', 'value' => 'true', 'type' => 'boolean', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'payment', 'key' => 'cod_fee', 'value' => '5.00', 'type' => 'number', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'payment', 'key' => 'enable_bank_transfer', 'value' => 'true', 'type' => 'boolean', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'payment', 'key' => 'bank_details', 'value' => 'Bank: Fashion Bank\nAccount: 1234567890\nRouting: 123456789', 'type' => 'text', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],

            // ORDER SETTINGS
            ['group_name' => 'orders', 'key' => 'order_number_prefix', 'value' => 'ORD-', 'type' => 'string', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'orders', 'key' => 'default_order_status', 'value' => '1', 'type' => 'number', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'orders', 'key' => 'enable_order_tracking', 'value' => 'true', 'type' => 'boolean', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'orders', 'key' => 'allow_order_cancellation', 'value' => 'true', 'type' => 'boolean', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],

            // CUSTOMER SETTINGS
            ['group_name' => 'customers', 'key' => 'enable_customer_registration', 'value' => 'true', 'type' => 'boolean', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'customers', 'key' => 'require_email_verification', 'value' => 'false', 'type' => 'boolean', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'customers', 'key' => 'password_min_length', 'value' => '6', 'type' => 'number', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],

            // MAIL SERVER SETTINGS
            ['group_name' => 'mail', 'key' => 'mail_driver', 'value' => 'smtp', 'type' => 'string', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'mail', 'key' => 'mail_host', 'value' => 'smtp.gmail.com', 'type' => 'string', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'mail', 'key' => 'mail_port', 'value' => '587', 'type' => 'number', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'mail', 'key' => 'mail_username', 'value' => 'your-email@gmail.com', 'type' => 'string', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'mail', 'key' => 'mail_password', 'value' => 'your-app-password', 'type' => 'string', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'mail', 'key' => 'mail_encryption', 'value' => 'tls', 'type' => 'string', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'mail', 'key' => 'mail_from_address', 'value' => 'noreply@fashionstore.com', 'type' => 'string', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'mail', 'key' => 'mail_from_name', 'value' => 'Fashion Store', 'type' => 'string', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'mail', 'key' => 'send_order_confirmation', 'value' => 'true', 'type' => 'boolean', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'mail', 'key' => 'send_order_status_updates', 'value' => 'true', 'type' => 'boolean', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'mail', 'key' => 'send_password_reset', 'value' => 'true', 'type' => 'boolean', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'mail', 'key' => 'send_welcome_email', 'value' => 'true', 'type' => 'boolean', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],

            // BASIC SEO
            ['group_name' => 'seo', 'key' => 'meta_title_suffix', 'value' => ' - Fashion Store', 'type' => 'string', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'seo', 'key' => 'default_meta_description', 'value' => 'Shop the latest fashion trends at Fashion Store. Quality clothing and accessories.', 'type' => 'text', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],

            // SOCIAL MEDIA
            ['group_name' => 'social', 'key' => 'facebook_url', 'value' => 'https://facebook.com/fashionstore', 'type' => 'string', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'social', 'key' => 'instagram_url', 'value' => 'https://instagram.com/fashionstore', 'type' => 'string', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],

            // SECURITY
            ['group_name' => 'security', 'key' => 'max_login_attempts', 'value' => '5', 'type' => 'number', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'security', 'key' => 'session_lifetime', 'value' => '120', 'type' => 'number', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],

            // MAINTENANCE
            ['group_name' => 'maintenance', 'key' => 'maintenance_mode', 'value' => 'false', 'type' => 'boolean', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()], ['group_name' => 'maintenance', 'key' => 'maintenance_message', 'value' => 'We are currently performing maintenance. Please check back soon.', 'type' => 'text', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],]);
    }
}


