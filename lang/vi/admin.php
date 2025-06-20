<?php

return [
    'auth' => [
        'login' => 'Đăng nhập quản trị',
        'email' => 'Email',
        'password' => 'Mật khẩu',
        'remember_me' => 'Ghi nhớ đăng nhập',
        'login_button' => 'Đăng nhập',
        'logout' => 'Đăng xuất',
        'failed' => 'Thông tin đăng nhập không chính xác.',
    ],
    'dashboard' => [
        'title' => 'Bảng điều khiển',
        'welcome' => 'Chào mừng đến với trang quản trị',
        'stats' => [
            'total_customers' => 'Tổng số khách hàng',
            'total_orders' => 'Tổng số đơn hàng',
            'total_products' => 'Tổng số sản phẩm',
            'recent_orders' => 'Đơn hàng gần đây',
        ],
    ],
    'nav' => [
        'dashboard' => 'Bảng điều khiển',
        'products' => 'Sản phẩm',
        'categories' => 'Danh mục',
        'orders' => 'Đơn hàng',
        'customers' => 'Khách hàng',
        'settings' => 'Cài đặt',
    ],
    'categories' => [
        'title' => 'Danh mục',
        'create' => 'Tạo danh mục',
        'edit' => 'Chỉnh sửa danh mục',
        'index' => 'Danh sách danh mục',
        'form' => [
            'name' => 'Tên',
            'slug' => 'Slug',
            'description' => 'Mô tả',
            'parent_category' => 'Danh mục cha',
            'no_parent' => 'Không có danh mục cha (Danh mục gốc)',
            'image' => 'Hình ảnh',
            'meta_title' => 'Meta title',
            'meta_description' => 'Meta description',
            'sort_order' => 'Thứ tự sắp xếp',
            'is_active' => 'Trạng thái',
            'active' => 'Hoạt động',
            'inactive' => 'Không hoạt động',
        ],
        'table' => [
            'id' => 'ID',
            'name' => 'Tên',
            'slug' => 'Slug',
            'parent' => 'Danh mục cha',
            'products' => 'Sản phẩm',
            'status' => 'Trạng thái',
            'sort_order' => 'Thứ tự',
            'created_at' => 'Ngày tạo',
            'actions' => 'Thao tác',
        ],
        'actions' => [
            'create' => 'Tạo mới',
            'edit' => 'Sửa',
            'delete' => 'Xóa',
            'save' => 'Lưu',
            'cancel' => 'Hủy',
            'back' => 'Quay lại danh sách',
        ],
        'messages' => [
            'created' => 'Đã tạo danh mục thành công.',
            'updated' => 'Đã cập nhật danh mục thành công.',
            'deleted' => 'Đã xóa danh mục thành công.',
            'delete_confirm' => 'Bạn có chắc chắn muốn xóa danh mục này?',
            'delete_error' => 'Không thể xóa danh mục.',
            'loading' => 'Đang tải danh mục...',
        ],
        'placeholders' => [
            'search' => 'Tìm kiếm danh mục...',
            'name' => 'Nhập tên danh mục',
            'slug' => 'Nhập slug hoặc để trống để tự động tạo',
            'description' => 'Nhập mô tả danh mục',
            'select_parent' => 'Chọn danh mục cha',
            'image' => 'Nhập URL hình ảnh',
            'meta_title' => 'Nhập meta title',
            'meta_description' => 'Nhập meta description',
            'sort_order' => 'Nhập thứ tự sắp xếp',
        ],
        'filters' => [
            'all' => 'Tất cả danh mục',
            'active' => 'Danh mục đang hoạt động',
            'inactive' => 'Danh mục không hoạt động',
            'parent' => 'Lọc theo danh mục cha',
        ],
    ],
    'products' => [
        'title' => 'Sản phẩm',
        'create' => 'Tạo sản phẩm',
        'edit' => 'Chỉnh sửa sản phẩm',
        'index' => 'Danh sách sản phẩm',
        'form' => [
            'name' => 'Tên',
            'slug' => 'Slug',
            'description' => 'Mô tả',
            'short_description' => 'Mô tả ngắn',
            'price' => 'Giá',
            'sale_price' => 'Giá khuyến mãi',
            'cost_price' => 'Giá nhập',
            'category' => 'Danh mục',
            'categories' => 'Danh mục',
            'image' => 'Hình ảnh chính',
            'images' => 'Hình ảnh bổ sung',
            'sku' => 'Mã SKU',
            'barcode' => 'Mã vạch',
            'stock' => 'Số lượng tồn kho',
            'weight' => 'Trọng lượng (kg)',
            'dimensions' => 'Kích thước (DxRxC)',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'meta_keywords' => 'Meta Keywords',
            'sort_order' => 'Thứ tự sắp xếp',
            'is_active' => 'Trạng thái',
            'is_featured' => 'Nổi bật',
            'active' => 'Hoạt động',
            'inactive' => 'Không hoạt động',
            'featured' => 'Nổi bật',
            'not_featured' => 'Không nổi bật',
        ],
        'table' => [
            'id' => 'ID',
            'name' => 'Tên',
            'slug' => 'Slug',
            'price' => 'Giá',
            'category' => 'Danh mục',
            'status' => 'Trạng thái',
            'featured' => 'Nổi bật',
            'stock' => 'Tồn kho',
            'created_at' => 'Ngày tạo',
            'actions' => 'Thao tác',
        ],
        'actions' => [
            'create' => 'Tạo mới',
            'edit' => 'Sửa',
            'delete' => 'Xóa',
            'save' => 'Lưu',
            'cancel' => 'Hủy',
            'back' => 'Quay lại danh sách',
        ],
        'messages' => [
            'created' => 'Đã tạo sản phẩm thành công.',
            'updated' => 'Đã cập nhật sản phẩm thành công.',
            'deleted' => 'Đã xóa sản phẩm thành công.',
            'delete_confirm' => 'Bạn có chắc chắn muốn xóa sản phẩm này?',
            'delete_error' => 'Không thể xóa sản phẩm.',
        ],
        'placeholders' => [
            'search' => 'Tìm kiếm sản phẩm...',
            'name' => 'Nhập tên sản phẩm',
            'slug' => 'Nhập slug hoặc để trống để tự động tạo',
            'description' => 'Nhập mô tả sản phẩm',
            'short_description' => 'Nhập mô tả ngắn',
            'price' => 'Nhập giá',
            'sale_price' => 'Nhập giá khuyến mãi (tùy chọn)',
            'cost_price' => 'Nhập giá nhập',
            'select_category' => 'Chọn danh mục',
            'image' => 'Nhập URL hình ảnh hoặc tải lên',
            'sku' => 'Nhập mã SKU',
            'barcode' => 'Nhập mã vạch',
            'stock' => 'Nhập số lượng tồn kho',
            'weight' => 'Nhập trọng lượng (kg)',
            'dimensions' => 'Nhập kích thước (DxRxC)',
            'meta_title' => 'Nhập meta title',
            'meta_description' => 'Nhập meta description',
            'meta_keywords' => 'Nhập meta keywords',
            'sort_order' => 'Nhập thứ tự sắp xếp',
        ],
        'filters' => [
            'all' => 'Tất cả sản phẩm',
            'active' => 'Sản phẩm đang hoạt động',
            'inactive' => 'Sản phẩm không hoạt động',
            'featured' => 'Sản phẩm nổi bật',
            'out_of_stock' => 'Hết hàng',
            'category' => 'Lọc theo danh mục',
        ],
    ],
    'settings' => [
        'title' => 'Cài đặt hệ thống',
        'groups' => 'Nhóm cài đặt',
        'save' => 'Lưu cài đặt',
        'no_settings' => 'Không có cài đặt nào trong nhóm này.',
        'group_labels' => [
            'general' => 'Thông tin chung',
            'currency' => 'Tiền tệ',
            'catalog' => 'Danh mục sản phẩm',
            'inventory' => 'Kho hàng',
            'cart' => 'Giỏ hàng',
            'checkout' => 'Thanh toán',
            'shipping' => 'Vận chuyển',
            'tax' => 'Thuế',
            'payment' => 'Phương thức thanh toán',
            'orders' => 'Đơn hàng',
            'customers' => 'Khách hàng',
            'mail' => 'Email',
            'seo' => 'SEO',
            'social' => 'Mạng xã hội',
            'security' => 'Bảo mật',
            'maintenance' => 'Bảo trì'
        ],
        'boolean_options' => [
            'enabled' => 'Bật',
            'disabled' => 'Tắt'
        ],
        'select_placeholder' => 'Chọn một tùy chọn',
        'updated' => 'Cài đặt đã được cập nhật thành công.',
        'update_error' => 'Đã xảy ra lỗi khi cập nhật cài đặt.',
        'validation_error' => 'Vui lòng kiểm tra lại biểu mẫu để sửa lỗi.',
        'alert' => [
            'success' => 'Thành công',
            'error' => 'Lỗi',
            'warning' => 'Cảnh báo',
            'info' => 'Thông tin',
        ],
    ],
]; 