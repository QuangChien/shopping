-- =============================
-- TABLE: users (Laravel Breeze default)
-- =============================
CREATE TABLE users (
                       id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                       name VARCHAR(255) NOT NULL,
                       email VARCHAR(255) UNIQUE NOT NULL,
                       email_verified_at TIMESTAMP NULL DEFAULT NULL,
                       password VARCHAR(255) NOT NULL,
                       is_active BOOLEAN DEFAULT TRUE,
                       last_login_at TIMESTAMP NULL DEFAULT NULL,
                       remember_token VARCHAR(100) NULL DEFAULT NULL,
                       created_at TIMESTAMP NULL DEFAULT NULL,
                       updated_at TIMESTAMP NULL DEFAULT NULL,

                       INDEX idx_users_email (email),
                       INDEX idx_users_active (is_active)
);

-- =============================
-- TABLE: customers
-- =============================
CREATE TABLE customers (
                           id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                           first_name VARCHAR(100) NOT NULL,
                           last_name VARCHAR(100) NOT NULL,
                           email VARCHAR(255) UNIQUE NOT NULL,
                           email_verified_at TIMESTAMP NULL DEFAULT NULL,
                           password VARCHAR(255) NOT NULL,
                           phone VARCHAR(20) NULL DEFAULT NULL,
                           date_of_birth DATE NULL DEFAULT NULL,
                           gender ENUM('male', 'female', 'other') NULL DEFAULT NULL,
                           is_active BOOLEAN DEFAULT TRUE,
                           last_login_at TIMESTAMP NULL DEFAULT NULL,
                           remember_token VARCHAR(100) NULL DEFAULT NULL,
                           created_at TIMESTAMP NULL DEFAULT NULL,
                           updated_at TIMESTAMP NULL DEFAULT NULL,

                           INDEX idx_customers_email (email),
                           INDEX idx_customers_name (first_name, last_name),
                           INDEX idx_customers_active (is_active),
                           INDEX idx_customers_phone (phone)
);

-- =============================
-- TABLE: customer_addresses
-- =============================
CREATE TABLE customer_addresses (
                                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                    customer_id BIGINT UNSIGNED NOT NULL,
                                    address_type ENUM('billing', 'shipping') NOT NULL,
                                    first_name VARCHAR(100) NOT NULL,
                                    last_name VARCHAR(100) NOT NULL,
                                    company VARCHAR(100) NULL DEFAULT NULL,
                                    street_address_1 VARCHAR(255) NOT NULL,
                                    street_address_2 VARCHAR(255) NULL DEFAULT NULL,
                                    city VARCHAR(100) NOT NULL,
                                    state_province VARCHAR(100) NOT NULL,
                                    postal_code VARCHAR(20) NOT NULL,
                                    country_code VARCHAR(2) NOT NULL DEFAULT 'US',
                                    phone VARCHAR(20) NULL DEFAULT NULL,
                                    is_default BOOLEAN DEFAULT FALSE,
                                    created_at TIMESTAMP NULL DEFAULT NULL,
                                    updated_at TIMESTAMP NULL DEFAULT NULL,

                                    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
                                    INDEX idx_customer_addresses_customer_id (customer_id),
                                    INDEX idx_customer_addresses_type (address_type),
                                    INDEX idx_customer_addresses_default (is_default)
);

-- =============================
-- TABLE: product_tax_classes
-- =============================
CREATE TABLE product_tax_classes (
                                     id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                     name VARCHAR(255) NOT NULL,
                                     description TEXT NULL DEFAULT NULL,
                                     is_active BOOLEAN DEFAULT TRUE,
                                     created_at TIMESTAMP NULL DEFAULT NULL,
                                     updated_at TIMESTAMP NULL DEFAULT NULL,

                                     INDEX idx_tax_classes_active (is_active)
);

-- =============================
-- TABLE: categories
-- =============================
CREATE TABLE categories (
                            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                            parent_id BIGINT UNSIGNED DEFAULT NULL,
                            name VARCHAR(255) NOT NULL,
                            slug VARCHAR(255) UNIQUE NOT NULL,
                            description TEXT NULL DEFAULT NULL,
                            image VARCHAR(255) NULL DEFAULT NULL,
                            meta_title VARCHAR(255) NULL DEFAULT NULL,
                            meta_description TEXT NULL DEFAULT NULL,
                            sort_order INT DEFAULT 0,
                            is_active BOOLEAN DEFAULT TRUE,
                            created_at TIMESTAMP NULL DEFAULT NULL,
                            updated_at TIMESTAMP NULL DEFAULT NULL,

                            FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
                            INDEX idx_categories_parent_id (parent_id),
                            INDEX idx_categories_slug (slug),
                            INDEX idx_categories_active (is_active),
                            INDEX idx_categories_sort (sort_order)
);

-- =============================
-- TABLE: product_attributes
-- =============================
CREATE TABLE product_attributes (
                                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                    code VARCHAR(100) UNIQUE NOT NULL,
                                    backend_type ENUM('varchar', 'text', 'int', 'decimal', 'datetime', 'boolean') NOT NULL,
                                    frontend_input ENUM('text', 'textarea', 'select', 'multiselect', 'boolean', 'date', 'datetime', 'price', 'weight', 'media_image') NOT NULL,
                                    frontend_label VARCHAR(255) NOT NULL,
                                    is_required BOOLEAN DEFAULT FALSE,
                                    is_unique BOOLEAN DEFAULT FALSE,
                                    is_filterable BOOLEAN DEFAULT FALSE,
                                    is_searchable BOOLEAN DEFAULT FALSE,
                                    is_comparable BOOLEAN DEFAULT FALSE,
                                    is_visible_on_front BOOLEAN DEFAULT TRUE,
                                    sort_order INT DEFAULT 0,
                                    default_value TEXT NULL DEFAULT NULL,
                                    frontend_class VARCHAR(255) NULL DEFAULT NULL,
                                    note TEXT NULL DEFAULT NULL,
                                    created_at TIMESTAMP NULL DEFAULT NULL,
                                    updated_at TIMESTAMP NULL DEFAULT NULL,

                                    INDEX idx_product_attributes_code (code),
                                    INDEX idx_product_attributes_backend_type (backend_type),
                                    INDEX idx_product_attributes_filterable (is_filterable),
                                    INDEX idx_product_attributes_searchable (is_searchable)
);

-- =============================
-- TABLE: products (Core product info only)
-- =============================
CREATE TABLE products (
                          id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                          sku VARCHAR(100) UNIQUE NOT NULL,
                          tax_class_id BIGINT UNSIGNED DEFAULT NULL,
                          status ENUM('enabled', 'disabled') DEFAULT 'disabled',
                          visibility ENUM('not_visible', 'catalog', 'search', 'catalog_search') DEFAULT 'catalog_search',
                          type_id ENUM('simple', 'configurable', 'grouped', 'virtual', 'bundle', 'downloadable') DEFAULT 'simple',
                          created_at TIMESTAMP NULL DEFAULT NULL,
                          updated_at TIMESTAMP NULL DEFAULT NULL,

                          FOREIGN KEY (tax_class_id) REFERENCES product_tax_classes(id) ON DELETE SET NULL,
                          INDEX idx_products_sku (sku),
                          INDEX idx_products_status (status),
                          INDEX idx_products_visibility (visibility),
                          INDEX idx_products_type (type_id)
);

-- =============================
-- TABLE: product_attribute_values_varchar
-- =============================
CREATE TABLE product_attribute_values_varchar (
                                                  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                                  product_id BIGINT UNSIGNED NOT NULL,
                                                  attribute_id BIGINT UNSIGNED NOT NULL,
                                                  value VARCHAR(255) NULL DEFAULT NULL,

                                                  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                                                  FOREIGN KEY (attribute_id) REFERENCES product_attributes(id) ON DELETE CASCADE,
                                                  UNIQUE KEY unique_product_attribute_varchar (product_id, attribute_id),
                                                  INDEX idx_product_varchar_product (product_id),
                                                  INDEX idx_product_varchar_attribute (attribute_id),
                                                  INDEX idx_product_varchar_value (value)
);

-- =============================
-- TABLE: product_attribute_values_text
-- =============================
CREATE TABLE product_attribute_values_text (
                                               id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                               product_id BIGINT UNSIGNED NOT NULL,
                                               attribute_id BIGINT UNSIGNED NOT NULL,
                                               value TEXT NULL DEFAULT NULL,

                                               FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                                               FOREIGN KEY (attribute_id) REFERENCES product_attributes(id) ON DELETE CASCADE,
                                               UNIQUE KEY unique_product_attribute_text (product_id, attribute_id),
                                               INDEX idx_product_text_product (product_id),
                                               INDEX idx_product_text_attribute (attribute_id),
                                               FULLTEXT idx_product_text_value (value)
);

-- =============================
-- TABLE: product_attribute_values_int
-- =============================
CREATE TABLE product_attribute_values_int (
                                              id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                              product_id BIGINT UNSIGNED NOT NULL,
                                              attribute_id BIGINT UNSIGNED NOT NULL,
                                              value INT NULL DEFAULT NULL,

                                              FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                                              FOREIGN KEY (attribute_id) REFERENCES product_attributes(id) ON DELETE CASCADE,
                                              UNIQUE KEY unique_product_attribute_int (product_id, attribute_id),
                                              INDEX idx_product_int_product (product_id),
                                              INDEX idx_product_int_attribute (attribute_id),
                                              INDEX idx_product_int_value (value)
);

-- =============================
-- TABLE: product_attribute_values_decimal
-- =============================
CREATE TABLE product_attribute_values_decimal (
                                                  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                                  product_id BIGINT UNSIGNED NOT NULL,
                                                  attribute_id BIGINT UNSIGNED NOT NULL,
                                                  value DECIMAL(12,4) NULL DEFAULT NULL,

                                                  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                                                  FOREIGN KEY (attribute_id) REFERENCES product_attributes(id) ON DELETE CASCADE,
                                                  UNIQUE KEY unique_product_attribute_decimal (product_id, attribute_id),
                                                  INDEX idx_product_decimal_product (product_id),
                                                  INDEX idx_product_decimal_attribute (attribute_id),
                                                  INDEX idx_product_decimal_value (value)
);

-- =============================
-- TABLE: product_attribute_values_datetime
-- =============================
CREATE TABLE product_attribute_values_datetime (
                                                   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                                   product_id BIGINT UNSIGNED NOT NULL,
                                                   attribute_id BIGINT UNSIGNED NOT NULL,
                                                   value DATETIME NULL DEFAULT NULL,

                                                   FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                                                   FOREIGN KEY (attribute_id) REFERENCES product_attributes(id) ON DELETE CASCADE,
                                                   UNIQUE KEY unique_product_attribute_datetime (product_id, attribute_id),
                                                   INDEX idx_product_datetime_product (product_id),
                                                   INDEX idx_product_datetime_attribute (attribute_id),
                                                   INDEX idx_product_datetime_value (value)
);

-- =============================
-- TABLE: product_attribute_values_boolean (Thêm bảng này cho boolean values)
-- =============================
CREATE TABLE product_attribute_values_boolean (
                                                  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                                  product_id BIGINT UNSIGNED NOT NULL,
                                                  attribute_id BIGINT UNSIGNED NOT NULL,
                                                  value BOOLEAN NULL DEFAULT NULL,

                                                  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                                                  FOREIGN KEY (attribute_id) REFERENCES product_attributes(id) ON DELETE CASCADE,
                                                  UNIQUE KEY unique_product_attribute_boolean (product_id, attribute_id),
                                                  INDEX idx_product_boolean_product (product_id),
                                                  INDEX idx_product_boolean_attribute (attribute_id),
                                                  INDEX idx_product_boolean_value (value)
);

-- =============================
-- TABLE: product_categories
-- =============================
CREATE TABLE product_categories (
                                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                    product_id BIGINT UNSIGNED NOT NULL,
                                    category_id BIGINT UNSIGNED NOT NULL,
                                    position INT DEFAULT 0,
                                    created_at TIMESTAMP NULL DEFAULT NULL,

                                    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                                    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
                                    UNIQUE KEY unique_product_category (product_id, category_id),
                                    INDEX idx_product_categories_product (product_id),
                                    INDEX idx_product_categories_category (category_id),
                                    INDEX idx_product_categories_position (position)
);

-- =============================
-- TABLE: product_media
-- =============================
CREATE TABLE product_media (
                               id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                               product_id BIGINT UNSIGNED NOT NULL,
                               type ENUM('image', 'video', 'document') DEFAULT 'image',
                               path VARCHAR(500) NOT NULL,
                               alt_text VARCHAR(255) NULL DEFAULT NULL,
                               title VARCHAR(255) NULL DEFAULT NULL,
                               sort_order INT DEFAULT 0,
                               is_main BOOLEAN DEFAULT FALSE,
                               created_at TIMESTAMP NULL DEFAULT NULL,
                               updated_at TIMESTAMP NULL DEFAULT NULL,

                               FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                               INDEX idx_product_media_product (product_id),
                               INDEX idx_product_media_type (type),
                               INDEX idx_product_media_sort (sort_order),
                               INDEX idx_product_media_main (is_main)
);

-- =============================
-- TABLE: inventory
-- =============================
CREATE TABLE inventory (
                           id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                           product_id BIGINT UNSIGNED NOT NULL,
                           quantity INT DEFAULT 0,
                           reserved_quantity INT DEFAULT 0,
                           min_quantity INT DEFAULT 0,
                           max_quantity INT NULL DEFAULT NULL,
                           is_in_stock BOOLEAN DEFAULT TRUE,
                           manage_stock BOOLEAN DEFAULT TRUE,
                           backorders ENUM('no', 'notify', 'yes') DEFAULT 'no',
                           updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                           FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                           UNIQUE KEY unique_product_inventory (product_id),
                           INDEX idx_inventory_stock (is_in_stock),
                           INDEX idx_inventory_quantity (quantity)
);

-- =============================
-- TABLE: order_statuses
-- =============================
CREATE TABLE order_statuses (
                                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                name VARCHAR(100) UNIQUE NOT NULL,
                                label VARCHAR(100) NOT NULL,
                                color VARCHAR(7) DEFAULT '#6B7280',
                                description TEXT NULL DEFAULT NULL,
                                is_default BOOLEAN DEFAULT FALSE,
                                sort_order INT DEFAULT 0,
                                created_at TIMESTAMP NULL DEFAULT NULL,
                                updated_at TIMESTAMP NULL DEFAULT NULL,

                                INDEX idx_order_statuses_default (is_default),
                                INDEX idx_order_statuses_sort (sort_order)
);

-- =============================
-- TABLE: shipping_methods
-- =============================
CREATE TABLE shipping_methods (
                                  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                  name VARCHAR(100) NOT NULL,
                                  code VARCHAR(50) UNIQUE NOT NULL,
                                  description TEXT NULL DEFAULT NULL,
                                  price DECIMAL(12,4) DEFAULT 0,
                                  free_shipping_threshold DECIMAL(12,4) NULL DEFAULT NULL,
                                  is_active BOOLEAN DEFAULT TRUE,
                                  sort_order INT DEFAULT 0,
                                  created_at TIMESTAMP NULL DEFAULT NULL,
                                  updated_at TIMESTAMP NULL DEFAULT NULL,

                                  INDEX idx_shipping_methods_code (code),
                                  INDEX idx_shipping_methods_active (is_active)
);

-- =============================
-- TABLE: payment_methods
-- =============================
CREATE TABLE payment_methods (
                                 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                 name VARCHAR(100) NOT NULL,
                                 code VARCHAR(50) UNIQUE NOT NULL,
                                 description TEXT NULL DEFAULT NULL,
                                 instructions TEXT NULL DEFAULT NULL,
                                 is_active BOOLEAN DEFAULT TRUE,
                                 sort_order INT DEFAULT 0,
                                 settings JSON NULL DEFAULT NULL,
                                 created_at TIMESTAMP NULL DEFAULT NULL,
                                 updated_at TIMESTAMP NULL DEFAULT NULL,

                                 INDEX idx_payment_methods_code (code),
                                 INDEX idx_payment_methods_active (is_active)
);

-- =============================
-- TABLE: taxes
-- =============================
CREATE TABLE taxes (
                       id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                       name VARCHAR(255) NOT NULL,
                       rate DECIMAL(8,4) NOT NULL,
                       country_code VARCHAR(2) NULL DEFAULT NULL,
                       state_code VARCHAR(10) NULL DEFAULT NULL,
                       city VARCHAR(100) NULL DEFAULT NULL,
                       zip_code VARCHAR(20) NULL DEFAULT NULL,
                       is_compound BOOLEAN DEFAULT FALSE,
                       priority INT DEFAULT 0,
                       is_active BOOLEAN DEFAULT TRUE,
                       created_at TIMESTAMP NULL DEFAULT NULL,
                       updated_at TIMESTAMP NULL DEFAULT NULL,

                       INDEX idx_taxes_location (country_code, state_code, city),
                       INDEX idx_taxes_active (is_active),
                       INDEX idx_taxes_priority (priority)
);

-- =============================
-- TABLE: coupons
-- =============================
CREATE TABLE coupons (
                         id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                         code VARCHAR(50) UNIQUE NOT NULL,
                         type ENUM('fixed_cart', 'percent_cart', 'fixed_product', 'percent_product') NOT NULL,
                         amount DECIMAL(12,4) NOT NULL,
                         minimum_amount DECIMAL(12,4) NULL DEFAULT NULL,
                         maximum_amount DECIMAL(12,4) NULL DEFAULT NULL,
                         usage_limit_per_coupon INT NULL DEFAULT NULL,
                         usage_limit_per_customer INT NULL DEFAULT NULL,
                         used_count INT DEFAULT 0,
                         individual_use BOOLEAN DEFAULT FALSE,
                         exclude_sale_items BOOLEAN DEFAULT FALSE,
                         starts_at TIMESTAMP NULL DEFAULT NULL,
                         expires_at TIMESTAMP NULL DEFAULT NULL,
                         is_active BOOLEAN DEFAULT TRUE,
                         created_at TIMESTAMP NULL DEFAULT NULL,
                         updated_at TIMESTAMP NULL DEFAULT NULL,

                         INDEX idx_coupons_code (code),
                         INDEX idx_coupons_active (is_active),
                         INDEX idx_coupons_dates (starts_at, expires_at)
);

-- =============================
-- TABLE: quotes (Shopping Cart)
-- =============================
CREATE TABLE quotes (
                        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        customer_id BIGINT UNSIGNED DEFAULT NULL,
                        session_id VARCHAR(255) DEFAULT NULL,
                        currency_code VARCHAR(3) DEFAULT 'USD',
                        subtotal DECIMAL(12,4) DEFAULT 0,
                        tax_amount DECIMAL(12,4) DEFAULT 0,
                        discount_amount DECIMAL(12,4) DEFAULT 0,
                        shipping_amount DECIMAL(12,4) DEFAULT 0,
                        total DECIMAL(12,4) DEFAULT 0,
                        expires_at TIMESTAMP NULL DEFAULT NULL,
                        created_at TIMESTAMP NULL DEFAULT NULL,
                        updated_at TIMESTAMP NULL DEFAULT NULL,

                        FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
                        INDEX idx_quotes_customer (customer_id),
                        INDEX idx_quotes_session (session_id),
                        INDEX idx_quotes_expires (expires_at)
);

-- =============================
-- TABLE: quote_items
-- =============================
CREATE TABLE quote_items (
                             id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                             quote_id BIGINT UNSIGNED NOT NULL,
                             product_id BIGINT UNSIGNED NOT NULL,
                             quantity INT NOT NULL DEFAULT 1,
                             price DECIMAL(12,4) NOT NULL,
                             created_at TIMESTAMP NULL DEFAULT NULL,
                             updated_at TIMESTAMP NULL DEFAULT NULL,

                             FOREIGN KEY (quote_id) REFERENCES quotes(id) ON DELETE CASCADE,
                             FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                             INDEX idx_quote_items_quote (quote_id),
                             INDEX idx_quote_items_product (product_id)
);

-- =============================
-- TABLE: orders
-- =============================
CREATE TABLE orders (
                        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        order_number VARCHAR(100) UNIQUE NOT NULL,
                        customer_id BIGINT UNSIGNED DEFAULT NULL,
                        status_id BIGINT UNSIGNED NOT NULL,
                        currency_code VARCHAR(3) DEFAULT 'USD',
                        subtotal DECIMAL(12,4) DEFAULT 0,
                        tax_amount DECIMAL(12,4) DEFAULT 0,
                        discount_amount DECIMAL(12,4) DEFAULT 0,
                        shipping_amount DECIMAL(12,4) DEFAULT 0,
                        total DECIMAL(12,4) DEFAULT 0,
                        billing_address JSON NOT NULL,
                        shipping_address JSON NULL DEFAULT NULL,
                        shipping_method_id BIGINT UNSIGNED DEFAULT NULL,
                        payment_method_id BIGINT UNSIGNED DEFAULT NULL,
                        payment_status ENUM('pending', 'paid', 'failed', 'refunded', 'partially_refunded') DEFAULT 'pending',
                        notes TEXT NULL DEFAULT NULL,
                        ordered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        created_at TIMESTAMP NULL DEFAULT NULL,
                        updated_at TIMESTAMP NULL DEFAULT NULL,

                        FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
                        FOREIGN KEY (status_id) REFERENCES order_statuses(id),
                        FOREIGN KEY (shipping_method_id) REFERENCES shipping_methods(id) ON DELETE SET NULL,
                        FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id) ON DELETE SET NULL,
                        INDEX idx_orders_number (order_number),
                        INDEX idx_orders_customer (customer_id),
                        INDEX idx_orders_status (status_id),
                        INDEX idx_orders_payment_status (payment_status),
                        INDEX idx_orders_date (ordered_at),
                        INDEX idx_orders_total (total)
);

-- =============================
-- TABLE: order_items
-- =============================
CREATE TABLE order_items (
                             id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                             order_id BIGINT UNSIGNED NOT NULL,
                             product_id BIGINT UNSIGNED NOT NULL,
                             product_sku VARCHAR(100) NOT NULL,
                             product_name VARCHAR(255) NOT NULL,
                             quantity INT NOT NULL DEFAULT 1,
                             price DECIMAL(12,4) NOT NULL,
                             total DECIMAL(12,4) NOT NULL,
                             product_options JSON NULL DEFAULT NULL,
                             created_at TIMESTAMP NULL DEFAULT NULL,
                             updated_at TIMESTAMP NULL DEFAULT NULL,

                             FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                             FOREIGN KEY (product_id) REFERENCES products(id),
                             INDEX idx_order_items_order (order_id),
                             INDEX idx_order_items_product (product_id)
);

-- =============================
-- TABLE: order_coupons
-- =============================
CREATE TABLE order_coupons (
                               id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                               order_id BIGINT UNSIGNED NOT NULL,
                               coupon_id BIGINT UNSIGNED NOT NULL,
                               code VARCHAR(50) NOT NULL,
                               discount_amount DECIMAL(12,4) NOT NULL,
                               created_at TIMESTAMP NULL DEFAULT NULL,

                               FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                               FOREIGN KEY (coupon_id) REFERENCES coupons(id),
                               INDEX idx_order_coupons_order (order_id)
);

-- =============================
-- TABLE: order_taxes
-- =============================
CREATE TABLE order_taxes (
                             id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                             order_id BIGINT UNSIGNED NOT NULL,
                             tax_id BIGINT UNSIGNED NOT NULL,
                             name VARCHAR(255) NOT NULL,
                             rate DECIMAL(8,4) NOT NULL,
                             amount DECIMAL(12,4) NOT NULL,
                             created_at TIMESTAMP NULL DEFAULT NULL,

                             FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                             FOREIGN KEY (tax_id) REFERENCES taxes(id),
                             INDEX idx_order_taxes_order (order_id)
);

-- =============================
-- TABLE: reviews
-- =============================
CREATE TABLE reviews (
                         id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                         product_id BIGINT UNSIGNED NOT NULL,
                         customer_id BIGINT UNSIGNED DEFAULT NULL,
                         order_id BIGINT UNSIGNED DEFAULT NULL,
                         rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
                         title VARCHAR(255) NULL DEFAULT NULL,
                         content TEXT NULL DEFAULT NULL,
                         status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
                         helpful_count INT DEFAULT 0,
                         created_at TIMESTAMP NULL DEFAULT NULL,
                         updated_at TIMESTAMP NULL DEFAULT NULL,

                         FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                         FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
                         FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
                         INDEX idx_reviews_product (product_id),
                         INDEX idx_reviews_customer (customer_id),
                         INDEX idx_reviews_status (status),
                         INDEX idx_reviews_rating (rating)
);

-- =============================
-- TABLE: wishlists
-- =============================
CREATE TABLE wishlists (
                           id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                           customer_id BIGINT UNSIGNED NOT NULL,
                           name VARCHAR(255) DEFAULT 'My Wishlist',
                           is_default BOOLEAN DEFAULT TRUE,
                           is_public BOOLEAN DEFAULT FALSE,
                           created_at TIMESTAMP NULL DEFAULT NULL,
                           updated_at TIMESTAMP NULL DEFAULT NULL,

                           FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
                           INDEX idx_wishlists_customer (customer_id),
                           INDEX idx_wishlists_default (is_default)
);

-- =============================
-- TABLE: wishlist_items
-- =============================
CREATE TABLE wishlist_items (
                                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                wishlist_id BIGINT UNSIGNED NOT NULL,
                                product_id BIGINT UNSIGNED NOT NULL,
                                created_at TIMESTAMP NULL DEFAULT NULL,

                                FOREIGN KEY (wishlist_id) REFERENCES wishlists(id) ON DELETE CASCADE,
                                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                                UNIQUE KEY unique_wishlist_product (wishlist_id, product_id),
                                INDEX idx_wishlist_items_wishlist (wishlist_id),
                                INDEX idx_wishlist_items_product (product_id)
);

-- =============================
-- TABLE: compare_lists
-- =============================
CREATE TABLE compare_lists (
                               id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                               customer_id BIGINT UNSIGNED DEFAULT NULL,
                               session_id VARCHAR(255) DEFAULT NULL,
                               created_at TIMESTAMP NULL DEFAULT NULL,
                               updated_at TIMESTAMP NULL DEFAULT NULL,

                               FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
                               INDEX idx_compare_lists_customer (customer_id),
                               INDEX idx_compare_lists_session (session_id)
);

-- =============================
-- TABLE: compare_list_items
-- =============================
CREATE TABLE compare_list_items (
                                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                    compare_list_id BIGINT UNSIGNED NOT NULL,
                                    product_id BIGINT UNSIGNED NOT NULL,
                                    created_at TIMESTAMP NULL DEFAULT NULL,

                                    FOREIGN KEY (compare_list_id) REFERENCES compare_lists(id) ON DELETE CASCADE,
                                    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                                    UNIQUE KEY unique_compare_product (compare_list_id, product_id),
                                    INDEX idx_compare_items_list (compare_list_id),
                                    INDEX idx_compare_items_product (product_id)
);

-- =============================
-- TABLE: settings
-- =============================
CREATE TABLE settings (
                          id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                          group_name VARCHAR(100) NOT NULL,
                          `key` VARCHAR(255) NOT NULL,  -- Đặt trong backticks vì key là từ khóa
                          value LONGTEXT NULL DEFAULT NULL,
                          type ENUM('string', 'text', 'number', 'boolean', 'json') DEFAULT 'string',
                          is_public BOOLEAN DEFAULT FALSE,
                          created_at TIMESTAMP NULL DEFAULT NULL,
                          updated_at TIMESTAMP NULL DEFAULT NULL,

                          UNIQUE KEY unique_group_key (group_name, `key`),
                          INDEX idx_settings_group (group_name),
                          INDEX idx_settings_public (is_public)
);

-- =============================
-- INSERT SAMPLE DATA
-- =============================

-- 1. Insert Users (Admin users)
INSERT INTO users (name, email, email_verified_at, password, is_active, created_at, updated_at) VALUES
                                                                                                    ('Admin User', 'admin@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE, NOW(), NOW()),
                                                                                                    ('Manager User', 'manager@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE, NOW(), NOW());

-- 2. Insert Customers
INSERT INTO customers (first_name, last_name, email, email_verified_at, password, phone, date_of_birth, gender, is_active, created_at, updated_at) VALUES
                                                                                                                                                       ('John', 'Doe', 'john.doe@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1234567890', '1990-05-15', 'male', TRUE, NOW(), NOW()),
                                                                                                                                                       ('Jane', 'Smith', 'jane.smith@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1234567891', '1988-08-22', 'female', TRUE, NOW(), NOW()),
                                                                                                                                                       ('Mike', 'Johnson', 'mike.johnson@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1234567892', '1992-12-10', 'male', TRUE, NOW(), NOW()),
                                                                                                                                                       ('Sarah', 'Wilson', 'sarah.wilson@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1234567893', '1995-03-18', 'female', TRUE, NOW(), NOW()),
                                                                                                                                                       ('David', 'Brown', 'david.brown@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1234567894', '1987-07-25', 'male', TRUE, NOW(), NOW());

-- 3. Insert Customer Addresses
INSERT INTO customer_addresses (customer_id, address_type, first_name, last_name, street_address_1, city, state_province, postal_code, country_code, phone, is_default, created_at, updated_at) VALUES
                                                                                                                                                                                                    (1, 'billing', 'John', 'Doe', '123 Main St', 'New York', 'NY', '10001', 'US', '+1234567890', TRUE, NOW(), NOW()),
                                                                                                                                                                                                    (1, 'shipping', 'John', 'Doe', '123 Main St', 'New York', 'NY', '10001', 'US', '+1234567890', TRUE, NOW(), NOW()),
                                                                                                                                                                                                    (2, 'billing', 'Jane', 'Smith', '456 Oak Ave', 'Los Angeles', 'CA', '90210', 'US', '+1234567891', TRUE, NOW(), NOW()),
                                                                                                                                                                                                    (3, 'billing', 'Mike', 'Johnson', '789 Pine Rd', 'Chicago', 'IL', '60601', 'US', '+1234567892', TRUE, NOW(), NOW()),
                                                                                                                                                                                                    (4, 'billing', 'Sarah', 'Wilson', '321 Elm St', 'Miami', 'FL', '33101', 'US', '+1234567893', TRUE, NOW(), NOW()),
                                                                                                                                                                                                    (5, 'billing', 'David', 'Brown', '654 Maple Dr', 'Seattle', 'WA', '98101', 'US', '+1234567894', TRUE, NOW(), NOW());

-- 4. Insert Product Tax Classes
INSERT INTO product_tax_classes (name, description, is_active, created_at, updated_at) VALUES
                                                                                           ('Standard Tax', 'Standard tax rate for most products', TRUE, NOW(), NOW()),
                                                                                           ('Reduced Tax', 'Reduced tax rate for specific items', TRUE, NOW(), NOW()),
                                                                                           ('No Tax', 'Tax-free products', TRUE, NOW(), NOW());

-- 5. Insert Categories
INSERT INTO categories (parent_id, name, slug, description, sort_order, is_active, created_at, updated_at) VALUES
                                                                                                               (NULL, 'Fashion', 'fashion', 'Fashion and clothing items', 1, TRUE, NOW(), NOW()),
                                                                                                               (1, 'Men\'s Clothing', 'mens-clothing', 'Clothing for men', 1, TRUE, NOW(), NOW()),
(1, 'Women\'s Clothing', 'womens-clothing', 'Clothing for women', 2, TRUE, NOW(), NOW()),
                                                                                                               (1, 'Accessories', 'accessories', 'Fashion accessories', 3, TRUE, NOW(), NOW()),
                                                                                                               (2, 'T-Shirts', 'mens-t-shirts', 'Men\'s T-shirts', 1, TRUE, NOW(), NOW()),
(2, 'Jeans', 'mens-jeans', 'Men\'s jeans', 2, TRUE, NOW(), NOW()),
                                                                                                               (3, 'Dresses', 'womens-dresses', 'Women\'s dresses', 1, TRUE, NOW(), NOW()),
(3, 'Tops', 'womens-tops', 'Women\'s tops', 2, TRUE, NOW(), NOW());

-- 6. Insert Product Attributes
INSERT INTO product_attributes (code, backend_type, frontend_input, frontend_label, is_required, is_filterable, is_searchable, is_visible_on_front, sort_order, created_at, updated_at) VALUES
                                                                                                                                                                                            ('name', 'varchar', 'text', 'Product Name', TRUE, FALSE, TRUE, TRUE, 1, NOW(), NOW()),
                                                                                                                                                                                            ('description', 'text', 'textarea', 'Description', FALSE, FALSE, TRUE, TRUE, 2, NOW(), NOW()),
                                                                                                                                                                                            ('short_description', 'text', 'textarea', 'Short Description', FALSE, FALSE, TRUE, TRUE, 3, NOW(), NOW()),
                                                                                                                                                                                            ('price', 'decimal', 'price', 'Price', TRUE, TRUE, FALSE, TRUE, 4, NOW(), NOW()),
                                                                                                                                                                                            ('special_price', 'decimal', 'price', 'Special Price', FALSE, TRUE, FALSE, TRUE, 5, NOW(), NOW()),
                                                                                                                                                                                            ('weight', 'decimal', 'weight', 'Weight', FALSE, FALSE, FALSE, TRUE, 6, NOW(), NOW()),
                                                                                                                                                                                            ('color', 'varchar', 'select', 'Color', FALSE, TRUE, TRUE, TRUE, 7, NOW(), NOW()),
                                                                                                                                                                                            ('size', 'varchar', 'select', 'Size', FALSE, TRUE, TRUE, TRUE, 8, NOW(), NOW()),
                                                                                                                                                                                            ('material', 'varchar', 'text', 'Material', FALSE, TRUE, TRUE, TRUE, 9, NOW(), NOW()),
                                                                                                                                                                                            ('brand', 'varchar', 'text', 'Brand', FALSE, TRUE, TRUE, TRUE, 10, NOW(), NOW());

-- 7. Insert Products (Simple and Configurable)
INSERT INTO products (sku, tax_class_id, status, visibility, type_id, created_at, updated_at) VALUES
-- Simple Products
('MT-001', 1, 'enabled', 'catalog_search', 'simple', NOW(), NOW()),
('MT-002', 1, 'enabled', 'catalog_search', 'simple', NOW(), NOW()),
('WD-001', 1, 'enabled', 'catalog_search', 'simple', NOW(), NOW()),
('WT-001', 1, 'enabled', 'catalog_search', 'simple', NOW(), NOW()),
('MJ-001', 1, 'enabled', 'catalog_search', 'simple', NOW(), NOW()),
-- Configurable Products
('MT-CONFIG', 1, 'enabled', 'catalog_search', 'configurable', NOW(), NOW()),
('WD-CONFIG', 1, 'enabled', 'catalog_search', 'configurable', NOW(), NOW()),
('MJ-CONFIG', 1, 'enabled', 'catalog_search', 'configurable', NOW(), NOW()),
('WT-CONFIG', 1, 'enabled', 'catalog_search', 'configurable', NOW(), NOW()),
('ACC-001', 1, 'enabled', 'catalog_search', 'simple', NOW(), NOW());

-- 8. Insert Product Attribute Values
-- Product Names
INSERT INTO product_attribute_values_varchar (product_id, attribute_id, value) VALUES
                                                                                   (1, 1, 'Classic Men\'s Cotton T-Shirt'),
(2, 1, 'Premium Men\'s Polo Shirt'),
                                                                                   (3, 1, 'Elegant Summer Dress'),
                                                                                   (4, 1, 'Casual Women\'s Blouse'),
(5, 1, 'Slim Fit Jeans'),
(6, 1, 'Men\'s T-Shirt Collection'),
                                                                                   (7, 1, 'Women\'s Dress Collection'),
(8, 1, 'Men\'s Jeans Collection'),
                                                                                   (9, 1, 'Women\'s Top Collection'),
(10, 1, 'Leather Handbag');

-- Product Descriptions
INSERT INTO product_attribute_values_text (product_id, attribute_id, value) VALUES
(1, 2, 'Comfortable cotton t-shirt perfect for everyday wear. Made from 100% organic cotton.'),
(2, 2, 'Premium quality polo shirt with modern fit. Perfect for both casual and semi-formal occasions.'),
(3, 2, 'Beautiful summer dress with floral pattern. Light and breathable fabric perfect for warm weather.'),
(4, 2, 'Versatile blouse that can be dressed up or down. Made from high-quality materials.'),
(5, 2, 'Classic slim fit jeans with stretch comfort. Perfect fit for modern lifestyle.'),
(6, 2, 'Collection of premium t-shirts available in various colors and sizes.'),
(7, 2, 'Elegant dress collection for all occasions. Available in multiple styles.'),
(8, 2, 'Premium jeans collection with different fits and washes.'),
(9, 2, 'Stylish tops collection for the modern woman.'),
(10, 2, 'Genuine leather handbag with multiple compartments. Perfect for everyday use.');

-- Product Prices
INSERT INTO product_attribute_values_decimal (product_id, attribute_id, value) VALUES
(1, 4, 29.99),
(2, 4, 49.99),
(3, 4, 79.99),
(4, 4, 39.99),
(5, 4, 89.99),
(6, 4, 35.99),
(7, 4, 89.99),
(8, 4, 99.99),
(9, 4, 45.99),
(10, 4, 129.99);

-- Product Colors
INSERT INTO product_attribute_values_varchar (product_id, attribute_id, value) VALUES
(1, 7, 'White'),
(2, 7, 'Navy Blue'),
(3, 7, 'Floral Print'),
(4, 7, 'Pink'),
(5, 7, 'Dark Blue'),
(6, 7, 'Multiple Colors'),
(7, 7, 'Multiple Colors'),
(8, 7, 'Multiple Colors'),
(9, 7, 'Multiple Colors'),
(10, 7, 'Brown');

-- Product Sizes
INSERT INTO product_attribute_values_varchar (product_id, attribute_id, value) VALUES
(1, 8, 'M'),
(2, 8, 'L'),
(3, 8, 'S'),
(4, 8, 'M'),
(5, 8, '32'),
(6, 8, 'S,M,L,XL'),
(7, 8, 'XS,S,M,L,XL'),
(8, 8, '30,32,34,36'),
(9, 8, 'S,M,L'),
(10, 8, 'One Size');

-- Product Brands
INSERT INTO product_attribute_values_varchar (product_id, attribute_id, value) VALUES
(1, 10, 'StyleCo'),
(2, 10, 'Premium Wear'),
(3, 10, 'Elegant Fashion'),
(4, 10, 'Casual Chic'),
(5, 10, 'Denim Pro'),
(6, 10, 'StyleCo'),
(7, 10, 'Elegant Fashion'),
(8, 10, 'Denim Pro'),
(9, 10, 'Casual Chic'),
(10, 10, 'Luxury Bags');

-- 9. Insert Product Categories
INSERT INTO product_categories (product_id, category_id, position, created_at) VALUES
(1, 5, 1, NOW()), -- Men's T-Shirt
(2, 5, 2, NOW()), -- Men's T-Shirt
                                                                                    (3, 7, 1, NOW()), -- Women's Dress
                                                                                    (4, 8, 1, NOW()), -- Women's Top
                                                                                    (5, 6, 1, NOW()), -- Men's Jeans
                                                                                    (6, 5, 3, NOW()), -- Men's T-Shirt
                                                                                    (7, 7, 2, NOW()), -- Women's Dress
                                                                                    (8, 6, 2, NOW()), -- Men's Jeans
                                                                                    (9, 8, 2, NOW()), -- Women's Top
                                                                                    (10, 4, 1, NOW()); -- Accessories

-- 10. Insert Inventory
INSERT INTO inventory (product_id, quantity, min_quantity, is_in_stock, manage_stock, updated_at) VALUES
                                                                                                      (1, 50, 5, TRUE, TRUE, NOW()),
                                                                                                      (2, 30, 5, TRUE, TRUE, NOW()),
                                                                                                      (3, 25, 3, TRUE, TRUE, NOW()),
                                                                                                      (4, 40, 5, TRUE, TRUE, NOW()),
                                                                                                      (5, 35, 5, TRUE, TRUE, NOW()),
                                                                                                      (6, 100, 10, TRUE, TRUE, NOW()),
                                                                                                      (7, 75, 8, TRUE, TRUE, NOW()),
                                                                                                      (8, 80, 10, TRUE, TRUE, NOW()),
                                                                                                      (9, 60, 8, TRUE, TRUE, NOW()),
                                                                                                      (10, 15, 2, TRUE, TRUE, NOW());

-- 11. Insert Order Statuses
INSERT INTO order_statuses (name, label, color, is_default, sort_order, created_at, updated_at) VALUES
                                                                                                    ('pending', 'Pending', '#FFA500', TRUE, 1, NOW(), NOW()),
                                                                                                    ('processing', 'Processing', '#0066CC', FALSE, 2, NOW(), NOW()),
                                                                                                    ('shipped', 'Shipped', '#9900CC', FALSE, 3, NOW(), NOW()),
                                                                                                    ('delivered', 'Delivered', '#009900', FALSE, 4, NOW(), NOW()),
                                                                                                    ('cancelled', 'Cancelled', '#CC0000', FALSE, 5, NOW(), NOW());

-- 12. Insert Shipping Methods
INSERT INTO shipping_methods (name, code, description, price, is_active, sort_order, created_at, updated_at) VALUES
                                                                                                                 ('Standard Shipping', 'standard', 'Standard shipping 5-7 business days', 9.99, TRUE, 1, NOW(), NOW()),
                                                                                                                 ('Express Shipping', 'express', 'Express shipping 2-3 business days', 19.99, TRUE, 2, NOW(), NOW()),
                                                                                                                 ('Free Shipping', 'free', 'Free shipping for orders over $100', 0.00, TRUE, 3, NOW(), NOW());

-- 13. Insert Payment Methods
INSERT INTO payment_methods (name, code, description, is_active, sort_order, created_at, updated_at) VALUES
                                                                                                         ('Credit Card', 'credit_card', 'Pay with credit or debit card', TRUE, 1, NOW(), NOW()),
                                                                                                         ('PayPal', 'paypal', 'Pay with PayPal account', TRUE, 2, NOW(), NOW()),
                                                                                                         ('Bank Transfer', 'bank_transfer', 'Direct bank transfer', TRUE, 3, NOW(), NOW()),
                                                                                                         ('Cash on Delivery', 'cod', 'Pay when you receive your order', TRUE, 4, NOW(), NOW());

-- 14. Insert Taxes
INSERT INTO taxes (name, rate, country_code, state_code, is_active, priority, created_at, updated_at) VALUES
                                                                                                          ('US Sales Tax', 8.25, 'US', 'CA', TRUE, 1, NOW(), NOW()),
                                                                                                          ('NY Sales Tax', 8.00, 'US', 'NY', TRUE, 1, NOW(), NOW()),
                                                                                                          ('FL Sales Tax', 6.00, 'US', 'FL', TRUE, 1, NOW(), NOW());

-- 15. Insert Coupons
INSERT INTO coupons (code, type, amount, minimum_amount, usage_limit_per_coupon, is_active, starts_at, expires_at, created_at, updated_at) VALUES
                                                                                                                                               ('WELCOME10', 'percent_cart', 10.00, 50.00, 100, TRUE, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), NOW(), NOW()),
                                                                                                                                               ('SAVE20', 'fixed_cart', 20.00, 100.00, 50, TRUE, NOW(), DATE_ADD(NOW(), INTERVAL 15 DAY), NOW(), NOW()),
                                                                                                                                               ('NEWUSER', 'percent_cart', 15.00, 75.00, 200, TRUE, NOW(), DATE_ADD(NOW(), INTERVAL 60 DAY), NOW(), NOW());

-- 16. Insert Sample Orders
INSERT INTO orders (order_number, customer_id, status_id, subtotal, tax_amount, shipping_amount, total, billing_address, shipping_address, shipping_method_id, payment_method_id, payment_status, ordered_at, created_at, updated_at) VALUES
                                                                                                                                                                                                                                          ('ORD-2024-001', 1, 2, 119.98, 9.60, 9.99, 139.57,
                                                                                                                                                                                                                                           '{"first_name":"John","last_name":"Doe","street_address_1":"123 Main St","city":"New York","state_province":"NY","postal_code":"10001","country_code":"US","phone":"+1234567890"}',
                                                                                                                                                                                                                                           '{"first_name":"John","last_name":"Doe","street_address_1":"123 Main St","city":"New York","state_province":"NY","postal_code":"10001","country_code":"US","phone":"+1234567890"}',
                                                                                                                                                                                                                                           1, 1, 'paid', NOW(), NOW(), NOW()),
                                                                                                                                                                                                                                          ('ORD-2024-002', 2, 1, 79.99, 6.40, 0.00, 86.39,
                                                                                                                                                                                                                                           '{"first_name":"Jane","last_name":"Smith","street_address_1":"456 Oak Ave","city":"Los Angeles","state_province":"CA","postal_code":"90210","country_code":"US","phone":"+1234567891"}',
                                                                                                                                                                                                                                           '{"first_name":"Jane","last_name":"Smith","street_address_1":"456 Oak Ave","city":"Los Angeles","state_province":"CA","postal_code":"90210","country_code":"US","phone":"+1234567891"}',
                                                                                                                                                                                                                                           3, 2, 'pending', NOW(), NOW(), NOW()),
                                                                                                                                                                                                                                          ('ORD-2024-003', 3, 4, 169.97, 13.60, 19.99, 203.56,
                                                                                                                                                                                                                                           '{"first_name":"Mike","last_name":"Johnson","street_address_1":"789 Pine Rd","city":"Chicago","state_province":"IL","postal_code":"60601","country_code":"US","phone":"+1234567892"}',
                                                                                                                                                                                                                                           '{"first_name":"Mike","last_name":"Johnson","street_address_1":"789 Pine Rd","city":"Chicago","state_province":"IL","postal_code":"60601","country_code":"US","phone":"+1234567892"}',
                                                                                                                                                                                                                                           2, 1, 'paid', NOW(), NOW(), NOW());

-- 17. Insert Order Items
INSERT INTO order_items (order_id, product_id, product_sku, product_name, quantity, price, total, created_at, updated_at) VALUES
                                                                                                                              (1, 1, 'MT-001', 'Classic Men\'s Cotton T-Shirt', 2, 29.99, 59.98, NOW(), NOW()),
(1, 2, 'MT-002', 'Premium Men\'s Polo Shirt', 1, 49.99, 49.99, NOW(), NOW()),
                                                                                                                              (1, 10, 'ACC-001', 'Leather Handbag', 1, 129.99, 129.99, NOW(), NOW()),
                                                                                                                              (2, 3, 'WD-001', 'Elegant Summer Dress', 1, 79.99, 79.99, NOW(), NOW()),
                                                                                                                              (3, 5, 'MJ-001', 'Slim Fit Jeans', 1, 89.99, 89.99, NOW(), NOW()),
                                                                                                                              (3, 4, 'WT-001', 'Casual Women\'s Blouse', 2, 39.99, 79.98, NOW(), NOW());

-- 18. Insert Reviews
INSERT INTO reviews (product_id, customer_id, order_id, rating, title, content, status, created_at, updated_at) VALUES
(1, 1, 1, 5, 'Great quality!', 'Love this t-shirt. Very comfortable and good quality cotton.', 'approved', NOW(), NOW()),
(2, 1, 1, 4, 'Nice polo shirt', 'Good fit and quality. Would recommend.', 'approved', NOW(), NOW()),
(3, 2, 2, 5, 'Beautiful dress', 'Perfect for summer. The fabric is light and comfortable.', 'approved', NOW(), NOW()),
(5, 3, 3, 4, 'Good jeans', 'Nice fit, good quality denim.', 'approved', NOW(), NOW());

-- 19. Insert Wishlists
INSERT INTO wishlists (customer_id, name, is_default, created_at, updated_at) VALUES
(1, 'My Wishlist', TRUE, NOW(), NOW()),
(2, 'Favorites', TRUE, NOW(), NOW()),
(4, 'Summer Collection', TRUE, NOW(), NOW());

-- 20. Insert Wishlist Items
INSERT INTO wishlist_items (wishlist_id, product_id, created_at) VALUES
(1, 3, NOW()),
(1, 4, NOW()),
(2, 1, NOW()),
(2, 5, NOW()),
(3, 7, NOW());

-- 21. Insert Settings
INSERT INTO settings (group_name, `key`, value, type, is_public, created_at, updated_at) VALUES
-- GENERAL STORE INFORMATION
('general', 'store_name', 'Fashion Store', 'string', TRUE, NOW(), NOW()),
('general', 'store_email', 'info@fashionstore.com', 'string', TRUE, NOW(), NOW()),
('general', 'store_phone', '+1-800-FASHION', 'string', TRUE, NOW(), NOW()),
('general', 'store_address', '123 Fashion Avenue, Style City, SC 12345', 'text', TRUE, NOW(), NOW()),
('general', 'admin_email', 'admin@fashionstore.com', 'string', FALSE, NOW(), NOW()),
('general', 'timezone', 'America/New_York', 'string', FALSE, NOW(), NOW()),

-- CURRENCY SETTINGS
('currency', 'default_currency', 'USD', 'string', TRUE, NOW(), NOW()),
('currency', 'currency_symbol', '$', 'string', TRUE, NOW(), NOW()),
('currency', 'currency_position', 'before', 'string', TRUE, NOW(), NOW()),
('currency', 'decimal_places', '2', 'number', TRUE, NOW(), NOW()),

-- CATALOG SETTINGS
('catalog', 'products_per_page', '12', 'number', TRUE, NOW(), NOW()),
('catalog', 'show_out_of_stock', 'true', 'boolean', TRUE, NOW(), NOW()),
('catalog', 'enable_reviews', 'true', 'boolean', TRUE, NOW(), NOW()),
('catalog', 'enable_wishlist', 'true', 'boolean', TRUE, NOW(), NOW()),
('catalog', 'low_stock_threshold', '5', 'number', FALSE, NOW(), NOW()),

-- INVENTORY SETTINGS
('inventory', 'track_inventory', 'true', 'boolean', FALSE, NOW(), NOW()),
('inventory', 'allow_backorders', 'false', 'boolean', FALSE, NOW(), NOW()),
('inventory', 'auto_reduce_stock', 'true', 'boolean', FALSE, NOW(), NOW()),

-- CART & CHECKOUT
('cart', 'enable_guest_checkout', 'true', 'boolean', TRUE, NOW(), NOW()),
('cart', 'cart_session_lifetime', '7', 'number', FALSE, NOW(), NOW()),
('cart', 'min_order_amount', '0', 'number', TRUE, NOW(), NOW()),

('checkout', 'require_phone_number', 'true', 'boolean', TRUE, NOW(), NOW()),
('checkout', 'enable_order_notes', 'true', 'boolean', TRUE, NOW(), NOW()),
('checkout', 'enable_terms_and_conditions', 'true', 'boolean', TRUE, NOW(), NOW()),

-- SHIPPING
('shipping', 'enable_shipping', 'true', 'boolean', TRUE, NOW(), NOW()),
('shipping', 'free_shipping_threshold', '100.00', 'number', TRUE, NOW(), NOW()),
('shipping', 'default_shipping_cost', '9.99', 'number', TRUE, NOW(), NOW()),

-- TAX SETTINGS
('tax', 'enable_taxes', 'true', 'boolean', TRUE, NOW(), NOW()),
('tax', 'default_tax_rate', '8.25', 'number', TRUE, NOW(), NOW()),
('tax', 'tax_included_in_price', 'false', 'boolean', TRUE, NOW(), NOW()),

-- PAYMENT METHODS
('payment', 'enable_cod', 'true', 'boolean', TRUE, NOW(), NOW()),
('payment', 'cod_fee', '5.00', 'number', TRUE, NOW(), NOW()),
('payment', 'enable_bank_transfer', 'true', 'boolean', TRUE, NOW(), NOW()),
('payment', 'bank_details', 'Bank: Fashion Bank\nAccount: 1234567890\nRouting: 123456789', 'text', TRUE, NOW(), NOW()),

-- ORDER SETTINGS
('orders', 'order_number_prefix', 'ORD-', 'string', FALSE, NOW(), NOW()),
('orders', 'default_order_status', '1', 'number', FALSE, NOW(), NOW()),
('orders', 'enable_order_tracking', 'true', 'boolean', TRUE, NOW(), NOW()),
('orders', 'allow_order_cancellation', 'true', 'boolean', TRUE, NOW(), NOW()),

-- CUSTOMER SETTINGS
('customers', 'enable_customer_registration', 'true', 'boolean', TRUE, NOW(), NOW()),
('customers', 'require_email_verification', 'false', 'boolean', FALSE, NOW(), NOW()),
('customers', 'password_min_length', '6', 'number', TRUE, NOW(), NOW()),

-- MAIL SERVER SETTINGS
('mail', 'mail_driver', 'smtp', 'string', FALSE, NOW(), NOW()),
('mail', 'mail_host', 'smtp.gmail.com', 'string', FALSE, NOW(), NOW()),
('mail', 'mail_port', '587', 'number', FALSE, NOW(), NOW()),
('mail', 'mail_username', 'your-email@gmail.com', 'string', FALSE, NOW(), NOW()),
('mail', 'mail_password', 'your-app-password', 'string', FALSE, NOW(), NOW()),
('mail', 'mail_encryption', 'tls', 'string', FALSE, NOW(), NOW()),
('mail', 'mail_from_address', 'noreply@fashionstore.com', 'string', FALSE, NOW(), NOW()),
('mail', 'mail_from_name', 'Fashion Store', 'string', FALSE, NOW(), NOW()),
('mail', 'send_order_confirmation', 'true', 'boolean', FALSE, NOW(), NOW()),
('mail', 'send_order_status_updates', 'true', 'boolean', FALSE, NOW(), NOW()),
('mail', 'send_password_reset', 'true', 'boolean', FALSE, NOW(), NOW()),
('mail', 'send_welcome_email', 'true', 'boolean', FALSE, NOW(), NOW()),

-- BASIC SEO
('seo', 'meta_title_suffix', ' - Fashion Store', 'string', TRUE, NOW(), NOW()),
('seo', 'default_meta_description', 'Shop the latest fashion trends at Fashion Store. Quality clothing and accessories.', 'text', TRUE, NOW(), NOW()),

-- SOCIAL MEDIA (Optional)
('social', 'facebook_url', 'https://facebook.com/fashionstore', 'string', TRUE, NOW(), NOW()),
('social', 'instagram_url', 'https://instagram.com/fashionstore', 'string', TRUE, NOW(), NOW()),

-- SECURITY (Basic)
('security', 'max_login_attempts', '5', 'number', FALSE, NOW(), NOW()),
('security', 'session_lifetime', '120', 'number', FALSE, NOW(), NOW()),

-- MAINTENANCE
('maintenance', 'maintenance_mode', 'false', 'boolean', FALSE, NOW(), NOW()),
('maintenance', 'maintenance_message', 'We are currently performing maintenance. Please check back soon.', 'text', TRUE, NOW(), NOW());


-- =============================
-- INSERT DATA FOR MISSING TABLES
-- =============================

-- 22. Insert Product Media
INSERT INTO product_media (product_id, type, path, alt_text, title, sort_order, is_main, created_at, updated_at) VALUES
(1, 'image', '/images/products/mt-001-main.jpg', 'Classic Men\'s Cotton T-Shirt', 'Main product image', 1, TRUE, NOW(), NOW()),
                                                                                                                              (1, 'image', '/images/products/mt-001-side.jpg', 'Classic Men\'s Cotton T-Shirt Side View', 'Side view', 2, FALSE, NOW(), NOW()),
(2, 'image', '/images/products/mt-002-main.jpg', 'Premium Men\'s Polo Shirt', 'Main product image', 1, TRUE, NOW(), NOW()),
                                                                                                                              (3, 'image', '/images/products/wd-001-main.jpg', 'Elegant Summer Dress', 'Main product image', 1, TRUE, NOW(), NOW()),
                                                                                                                              (3, 'image', '/images/products/wd-001-back.jpg', 'Elegant Summer Dress Back View', 'Back view', 2, FALSE, NOW(), NOW()),
                                                                                                                              (4, 'image', '/images/products/wt-001-main.jpg', 'Casual Women\'s Blouse', 'Main product image', 1, TRUE, NOW(), NOW()),
(5, 'image', '/images/products/mj-001-main.jpg', 'Slim Fit Jeans', 'Main product image', 1, TRUE, NOW(), NOW()),
(6, 'image', '/images/products/mt-config-main.jpg', 'Men\'s T-Shirt Collection', 'Main product image', 1, TRUE, NOW(), NOW()),
                                                                                                                              (7, 'image', '/images/products/wd-config-main.jpg', 'Women\'s Dress Collection', 'Main product image', 1, TRUE, NOW(), NOW()),
(8, 'image', '/images/products/mj-config-main.jpg', 'Men\'s Jeans Collection', 'Main product image', 1, TRUE, NOW(), NOW()),
                                                                                                                              (9, 'image', '/images/products/wt-config-main.jpg', 'Women\'s Top Collection', 'Main product image', 1, TRUE, NOW(), NOW()),
(10, 'image', '/images/products/acc-001-main.jpg', 'Leather Handbag', 'Main product image', 1, TRUE, NOW(), NOW()),
(10, 'image', '/images/products/acc-001-detail.jpg', 'Leather Handbag Detail', 'Detail view', 2, FALSE, NOW(), NOW());

-- 23. Insert Quotes (Shopping Cart) - Guest and Customer carts
INSERT INTO quotes (customer_id, session_id, currency_code, subtotal, tax_amount, discount_amount, shipping_amount, total, expires_at, created_at, updated_at) VALUES
(1, NULL, 'USD', 109.98, 8.80, 0.00, 9.99, 128.77, DATE_ADD(NOW(), INTERVAL 7 DAY), NOW(), NOW()),
(NULL, 'guest_session_123', 'USD', 79.99, 6.40, 0.00, 9.99, 96.38, DATE_ADD(NOW(), INTERVAL 3 DAY), NOW(), NOW()),
(4, NULL, 'USD', 169.98, 13.60, 10.00, 0.00, 173.58, DATE_ADD(NOW(), INTERVAL 7 DAY), NOW(), NOW()),
(NULL, 'guest_session_456', 'USD', 49.99, 4.00, 0.00, 9.99, 63.98, DATE_ADD(NOW(), INTERVAL 3 DAY), NOW(), NOW());

-- 24. Insert Quote Items
INSERT INTO quote_items (quote_id, product_id, quantity, price, created_at, updated_at) VALUES
(1, 1, 2, 29.99, NOW(), NOW()),
(1, 2, 1, 49.99, NOW(), NOW()),
(1, 4, 1, 39.99, NOW(), NOW()),
(2, 3, 1, 79.99, NOW(), NOW()),
(3, 5, 1, 89.99, NOW(), NOW()),
(3, 4, 2, 39.99, NOW(), NOW()),
(4, 2, 1, 49.99, NOW(), NOW());

-- 25. Insert Order Taxes
INSERT INTO order_taxes (order_id, tax_id, name, rate, amount, created_at) VALUES
(1, 2, 'NY Sales Tax', 8.00, 9.60, NOW()),
(2, 1, 'US Sales Tax', 8.25, 6.60, NOW()),
(3, 1, 'US Sales Tax', 8.25, 14.02, NOW());

-- 26. Insert Order Coupons
INSERT INTO order_coupons (order_id, coupon_id, code, discount_amount, created_at) VALUES
(3, 2, 'SAVE20', 20.00, NOW());

-- 27. Insert Compare Lists
INSERT INTO compare_lists (customer_id, session_id, created_at, updated_at) VALUES
(1, NULL, NOW(), NOW()),
(2, NULL, NOW(), NOW()),
(NULL, 'guest_compare_123', NOW(), NOW());

-- 28. Insert Compare List Items
INSERT INTO compare_list_items (compare_list_id, product_id, created_at) VALUES
(1, 1, NOW()),
(1, 2, NOW()),
(1, 6, NOW()),
(2, 3, NOW()),
(2, 7, NOW()),
(3, 5, NOW()),
(3, 8, NOW());

-- 29. Insert additional Product Attribute Values for missing attributes

-- Short Descriptions
INSERT INTO product_attribute_values_text (product_id, attribute_id, value) VALUES
(1, 3, 'Comfortable cotton t-shirt for everyday wear'),
(2, 3, 'Premium polo shirt with modern fit'),
(3, 3, 'Beautiful summer dress with floral pattern'),
(4, 3, 'Versatile blouse for any occasion'),
(5, 3, 'Classic slim fit jeans with stretch'),
(6, 3, 'Premium t-shirt collection'),
(7, 3, 'Elegant dress collection'),
(8, 3, 'Premium jeans collection'),
(9, 3, 'Stylish tops collection'),
(10, 3, 'Genuine leather handbag');

-- Special Prices (Sale prices)
INSERT INTO product_attribute_values_decimal (product_id, attribute_id, value) VALUES
(1, 5, 24.99),
(3, 5, 69.99),
(5, 5, 79.99),
(10, 5, 99.99);

-- Weights
INSERT INTO product_attribute_values_decimal (product_id, attribute_id, value) VALUES
(1, 6, 0.25),
(2, 6, 0.30),
(3, 6, 0.40),
(4, 6, 0.28),
(5, 6, 0.80),
(6, 6, 0.25),
(7, 6, 0.40),
(8, 6, 0.80),
(9, 6, 0.28),
(10, 6, 1.20);

-- Materials
INSERT INTO product_attribute_values_varchar (product_id, attribute_id, value) VALUES
(1, 9, '100% Organic Cotton'),
(2, 9, 'Cotton Blend'),
(3, 9, 'Polyester Chiffon'),
(4, 9, 'Cotton Silk Blend'),
(5, 9, 'Denim Cotton'),
(6, 9, '100% Cotton'),
(7, 9, 'Mixed Materials'),
(8, 9, 'Premium Denim'),
(9, 9, 'Cotton Blend'),
(10, 9, 'Genuine Leather');

-- 30. Update some inventory with reserved quantities
UPDATE inventory SET reserved_quantity = 5 WHERE product_id IN (1, 3, 5);
UPDATE inventory SET reserved_quantity = 2 WHERE product_id IN (2, 4);

-- 31. Insert additional customer addresses for shipping
INSERT INTO customer_addresses (customer_id, address_type, first_name, last_name, company, street_address_1, street_address_2, city, state_province, postal_code, country_code, phone, is_default, created_at, updated_at) VALUES
(2, 'shipping', 'Jane', 'Smith', NULL, '456 Oak Ave', 'Apt 2B', 'Los Angeles', 'CA', '90210', 'US', '+1234567891', TRUE, NOW(), NOW()),
(3, 'shipping', 'Mike', 'Johnson', 'Johnson Corp', '789 Pine Rd', 'Suite 100', 'Chicago', 'IL', '60601', 'US', '+1234567892', TRUE, NOW(), NOW()),
(4, 'shipping', 'Sarah', 'Wilson', NULL, '321 Elm St', NULL, 'Miami', 'FL', '33101', 'US', '+1234567893', TRUE, NOW(), NOW()),
(5, 'shipping', 'David', 'Brown', NULL, '654 Maple Dr', 'Unit 5', 'Seattle', 'WA', '98101', 'US', '+1234567894', TRUE, NOW(), NOW());

-- 32. Add more reviews for better data coverage
INSERT INTO reviews (product_id, customer_id, order_id, rating, title, content, status, helpful_count, created_at, updated_at) VALUES
(4, 3, 3, 5, 'Love this blouse!', 'Perfect fit and great quality. Highly recommend!', 'approved', 3, NOW(), NOW()),
(10, 1, 1, 4, 'Nice handbag', 'Good quality leather but a bit smaller than expected.', 'approved', 1, NOW(), NOW()),
(1, 4, NULL, 5, 'Excellent t-shirt', 'Very comfortable and great value for money.', 'pending', 0, NOW(), NOW()),
(2, 5, NULL, 3, 'Average quality', 'It\'s okay but not as premium as described.', 'approved', 0, NOW(), NOW());

