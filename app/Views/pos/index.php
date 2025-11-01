<?= $this->extend('layouts/mazer') ?>

<?= $this->section('styles') ?>
<style>
    /* POS Custom Styles */
    #main {
        padding: 0 !important;
    }
    
    /* Hide Mazer Sidebar for POS */
    #sidebar {
        display: none !important;
    }
    
    #main {
        margin-left: 0 !important;
    }
    
    .pos-container {
        display: flex;
        height: 100vh;
        overflow: hidden;
    }
    
    /* Top Header - Fixed */
    .pos-top-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 60px;
        background: white;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.5rem;
        z-index: 100;
    }
    
    .pos-top-header .brand {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .pos-top-header .brand i {
        width: 40px;
        height: 40px;
        background: #3772F0;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 1.2rem;
    }
    
    .pos-top-header .header-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .pos-top-header .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .pos-top-header .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
    }
    
    .pos-top-header .user-details {
        display: flex;
        flex-direction: column;
    }
    
    .pos-top-header .user-name {
        font-weight: 600;
        font-size: 0.9rem;
        line-height: 1.2;
    }
    
    .pos-top-header .user-role {
        font-size: 0.75rem;
        color: #6b7280;
    }
    
    .btn-logout {
        padding: 0.5rem 1rem;
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-logout:hover {
        background: #dc2626;
    }
    
    /* Main Layout */
    .pos-wrapper {
        display: flex;
        width: 100%;
        margin-top: 60px;
        height: calc(100vh - 60px);
    }
    
    /* Left Sidebar - Categories */
    .pos-sidebar {
        width: 80px;
        background: #3772F0;
        display: flex;
        flex-direction: column;
        padding: 1rem 0;
        gap: 0.5rem;
        overflow-y: auto;
        flex-shrink: 0;
    }
    
    .pos-sidebar .category-item {
        width: 100%;
        height: 70px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        font-size: 0.7rem;
        text-align: center;
        position: relative;
    }
    
    .pos-sidebar .category-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 0;
        background: white;
        transition: height 0.3s;
    }
    
    .pos-sidebar .category-item:hover,
    .pos-sidebar .category-item.active {
        color: white;
        background: rgba(255, 255, 255, 0.1);
    }
    
    .pos-sidebar .category-item.active::before {
        height: 40px;
    }
    
    .pos-sidebar .category-item i {
        font-size: 1.5rem;
        margin-bottom: 0.25rem;
    }
    
    /* Main Content */
    .pos-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #f8f9fa;
        overflow: hidden;
        min-width: 0;
    }
    
    .pos-search-header {
        background: white;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .search-box {
        max-width: 500px;
    }
    
    .search-box .form-control {
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        padding: 0.65rem 1rem;
    }
    
    .search-box .form-control:focus {
        border-color: #3772F0;
        box-shadow: 0 0 0 3px rgba(55, 114, 240, 0.1);
    }
    
    .category-tabs {
        background: white;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        gap: 0.75rem;
        overflow-x: auto;
        white-space: nowrap;
    }
    
    .category-tabs::-webkit-scrollbar {
        height: 4px;
    }
    
    .category-tabs::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 2px;
    }
    
    .category-tabs .tab-item {
        padding: 0.5rem 1.25rem;
        border-radius: 8px;
        background: #f3f4f6;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 500;
        font-size: 0.9rem;
        color: #4b5563;
    }
    
    .category-tabs .tab-item:hover {
        background: #e5e7eb;
    }
    
    .category-tabs .tab-item.active {
        background: #3772F0;
        color: white;
    }
    
    .products-grid {
        padding: 1.5rem;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        overflow-y: auto;
        height: 100%;
    }
    
    .product-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid transparent;
        display: flex;
        flex-direction: column;
        height: 240px;
    }
    
    .product-card:hover {
        border-color: #3772F0;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }
    
    .product-card .product-image {
        width: 100%;
        height: 140px;
        overflow: hidden;
    }
    
    .product-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .product-card .product-info {
        padding: 0.75rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    
    .product-card .product-name {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: #1f2937;
    }
    
    .product-card .product-desc {
        font-size: 0.75rem;
        color: #9ca3af;
        margin-bottom: 0.5rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        flex: 1;
    }
    
    .product-card .product-price {
        font-weight: 700;
        color: #3772F0;
        font-size: 1.1rem;
    }
    
    /* Invoice Sidebar */
    .pos-invoice {
        width: 380px;
        background: white;
        display: flex;
        flex-direction: column;
        border-left: 1px solid #e5e7eb;
        flex-shrink: 0;
    }
    
    .invoice-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .invoice-header h5 {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    .invoice-items {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
    }
    
    .invoice-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #9ca3af;
    }
    
    .invoice-empty i {
        font-size: 3.5rem;
        margin-bottom: 1rem;
    }
    
    .invoice-item {
        display: flex;
        gap: 0.75rem;
        padding: 0.75rem;
        background: #f9fafb;
        border-radius: 8px;
        margin-bottom: 0.75rem;
    }
    
    .invoice-item img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
        flex-shrink: 0;
    }
    
    .invoice-item-details {
        flex: 1;
        min-width: 0;
    }
    
    .invoice-item-name {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .invoice-item-meta {
        font-size: 0.75rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }
    
    .invoice-item-price {
        font-weight: 600;
        color: #3772F0;
        font-size: 0.95rem;
    }
    
    .invoice-item-actions {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-end;
    }
    
    .invoice-item-qty {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .invoice-item-qty button {
        width: 26px;
        height: 26px;
        padding: 0;
        border-radius: 6px;
        border: none;
        background: #3772F0;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.85rem;
    }
    
    .invoice-item-qty button:hover {
        background: #2557d6;
    }
    
    .invoice-item-qty .qty-display {
        width: 35px;
        text-align: center;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .invoice-summary {
        padding: 1.5rem;
        border-top: 1px solid #e5e7eb;
        background: #f9fafb;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
        color: #4b5563;
    }
    
    .summary-row.total {
        font-size: 1.3rem;
        font-weight: 700;
        color: #3772F0;
        padding-top: 0.75rem;
        border-top: 2px solid #e5e7eb;
        margin-top: 0.5rem;
    }
    
    .payment-methods {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
        margin: 1rem 0;
    }
    
    .payment-method {
        padding: 0.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        background: white;
        cursor: pointer;
        text-align: center;
        transition: all 0.3s;
    }
    
    .payment-method:hover {
        border-color: #3772F0;
    }
    
    .payment-method.active {
        border-color: #3772F0;
        background: #eff6ff;
    }
    
    .payment-method i {
        font-size: 1.5rem;
        display: block;
        margin-bottom: 0.25rem;
        color: #3772F0;
    }
    
    .payment-method small {
        font-size: 0.75rem;
        color: #6b7280;
    }
    
    .btn-order {
        width: 100%;
        padding: 0.85rem;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 8px;
        background: #3772F0;
        border: none;
        color: white;
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .btn-order:hover {
        background: #2557d6;
        transform: translateY(-1px);
    }
    
    .btn-order:disabled {
        background: #d1d5db;
        cursor: not-allowed;
        transform: none;
    }
    
    /* Mobile Cart Toggle */
    .mobile-cart-toggle {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #3772F0;
        color: white;
        border: none;
        box-shadow: 0 4px 12px rgba(55, 114, 240, 0.4);
        z-index: 999;
        font-size: 1.5rem;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .mobile-cart-toggle:hover {
        transform: scale(1.1);
    }
    
    .mobile-cart-toggle .cart-badge {
        position: absolute;
        top: 5px;
        right: 5px;
        background: #ef4444;
        color: white;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }
    
    .invoice-close-btn {
        display: none;
    }
    
    /* Tablet Responsive */
    @media (max-width: 1024px) {
        .pos-invoice {
            width: 340px;
        }
        
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
        }
        
        .pos-top-header .user-details {
            display: none;
        }
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .pos-top-header {
            padding: 0 1rem;
        }
        
        .pos-top-header .brand span {
            display: none;
        }
        
        .btn-logout span {
            display: none;
        }
        
        .btn-logout {
            padding: 0.5rem;
            width: 36px;
            height: 36px;
            justify-content: center;
        }
        
        .pos-sidebar {
            width: 60px;
            padding: 0.5rem 0;
        }
        
        .pos-sidebar .category-item {
            height: 60px;
            font-size: 0.65rem;
        }
        
        .pos-sidebar .category-item i {
            font-size: 1.3rem;
        }
        
        .pos-search-header {
            padding: 0.75rem 1rem;
        }
        
        .category-tabs {
            padding: 0.75rem 1rem;
            gap: 0.5rem;
        }
        
        .category-tabs .tab-item {
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
        }
        
        .products-grid {
            padding: 1rem;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 0.75rem;
        }
        
        .product-card {
            height: 220px;
        }
        
        .product-card .product-image {
            height: 120px;
        }
        
        .pos-invoice {
            position: fixed;
            top: 60px;
            right: -100%;
            height: calc(100vh - 60px);
            width: 100%;
            max-width: 380px;
            z-index: 998;
            transition: right 0.3s;
            box-shadow: -4px 0 12px rgba(0, 0, 0, 0.1);
        }
        
        .pos-invoice.show {
            right: 0;
        }
        
        .invoice-close-btn {
            display: block;
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.5rem;
            background: #f3f4f6;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            z-index: 10;
        }
        
        .mobile-cart-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }
    
    /* Small Mobile */
    @media (max-width: 480px) {
        .pos-sidebar {
            display: none;
        }
        
        .products-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
            padding: 0.75rem;
        }
        
        .product-card {
            height: 200px;
        }
        
        .product-card .product-image {
            height: 110px;
        }
        
        .product-card .product-info {
            padding: 0.5rem;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="pos-container">
    <!-- Top Header -->
    <div class="pos-top-header">
        <div class="brand">
            <i class="bi bi-shop"></i>
            <span>POS System</span>
        </div>
        <div class="header-actions">
            <div class="user-info">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($user->username) ?>&background=3772F0&color=fff" 
                     class="user-avatar" alt="User">
                <div class="user-details">
                    <div class="user-name"><?= esc($user->username) ?></div>
                    <div class="user-role">Cashier</div>
                </div>
            </div>
            <a href="<?= url_to('logout') ?>" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
    
    <!-- Main Wrapper -->
    <div class="pos-wrapper">
        <!-- Left Sidebar - Categories -->
        <div class="pos-sidebar">
            <a href="#" class="category-item active" data-category="all">
                <i class="bi bi-grid-fill"></i>
                <span>All</span>
            </a>
            <a href="#" class="category-item" data-category="breakfast">
                <i class="bi bi-cup-hot"></i>
                <span>Breakfast</span>
            </a>
            <a href="#" class="category-item" data-category="lunch">
                <i class="bi bi-bowl-fill"></i>
                <span>Lunch</span>
            </a>
            <a href="#" class="category-item" data-category="dinner">
                <i class="bi bi-moon-stars"></i>
                <span>Dinner</span>
            </a>
            <a href="#" class="category-item" data-category="desserts">
                <i class="bi bi-cake2"></i>
                <span>Desserts</span>
            </a>
            <a href="#" class="category-item" data-category="appetizer">
                <i class="bi bi-egg-fried"></i>
                <span>Appetizer</span>
            </a>
            <a href="#" class="category-item" data-category="soup">
                <i class="bi bi-moisture"></i>
                <span>Soup</span>
            </a>
            <a href="#" class="category-item" data-category="beverages">
                <i class="bi bi-cup-straw"></i>
                <span>Beverages</span>
            </a>
        </div>
        
        <!-- Main Content -->
        <div class="pos-main">
            <!-- Search Header -->
            <div class="pos-search-header">
                <div class="search-box">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" 
                               placeholder="Search menu..." id="searchProduct">
                    </div>
                </div>
            </div>
            
            <!-- Category Tabs -->
            <div class="category-tabs">
                <button class="tab-item active" data-category="all">All Menu</button>
                <button class="tab-item" data-category="lunch">Lunch</button>
                <button class="tab-item" data-category="dinner">Dinner</button>
                <button class="tab-item" data-category="desserts">Desserts</button>
                <button class="tab-item" data-category="appetizer">Appetizer</button>
                <button class="tab-item" data-category="soup">Soup</button>
                <button class="tab-item" data-category="beverages">Beverages</button>
            </div>
            
            <!-- Products Grid -->
            <div class="products-grid" id="productsGrid">
                <div class="product-card" data-product='{"id":1,"name":"Pasta Bolognese","price":50.5,"image":"https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=400"}'>
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=400" alt="Pasta Bolognese">
                    </div>
                    <div class="product-info">
                        <div class="product-name">Pasta Bolognese</div>
                        <div class="product-desc">Delicious Italian pasta</div>
                        <div class="product-price">$50.5</div>
                    </div>
                </div>
                
                <div class="product-card" data-product='{"id":2,"name":"Spicy Fried Chicken","price":45.7,"image":"https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=400"}'>
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=400" alt="Spicy Fried Chicken">
                    </div>
                    <div class="product-info">
                        <div class="product-name">Spicy Fried Chicken</div>
                        <div class="product-desc">Crispy and spicy</div>
                        <div class="product-price">$45.7</div>
                    </div>
                </div>
                
                <div class="product-card" data-product='{"id":3,"name":"Grilled Steak","price":80.0,"image":"https://images.unsplash.com/photo-1600891964092-4316c288032e?w=400"}'>
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1600891964092-4316c288032e?w=400" alt="Grilled Steak">
                    </div>
                    <div class="product-info">
                        <div class="product-name">Grilled Steak</div>
                        <div class="product-desc">Premium beef steak</div>
                        <div class="product-price">$80.0</div>
                    </div>
                </div>
                
                <div class="product-card" data-product='{"id":4,"name":"Fish And Chips","price":90.4,"image":"https://images.unsplash.com/photo-1579208575657-c595a05383b7?w=400"}'>
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1579208575657-c595a05383b7?w=400" alt="Fish And Chips">
                    </div>
                    <div class="product-info">
                        <div class="product-name">Fish And Chips</div>
                        <div class="product-desc">Classic British dish</div>
                        <div class="product-price">$90.4</div>
                    </div>
                </div>
                
                <div class="product-card" data-product='{"id":5,"name":"Beef Bourguignon","price":75.5,"image":"https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?w=400"}'>
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?w=400" alt="Beef Bourguignon">
                    </div>
                    <div class="product-info">
                        <div class="product-name">Beef Bourguignon</div>
                        <div class="product-desc">French beef stew</div>
                        <div class="product-price">$75.5</div>
                    </div>
                </div>
                
                <div class="product-card" data-product='{"id":6,"name":"Spaghetti Carbonara","price":35.3,"image":"https://images.unsplash.com/photo-1612874742237-6526221588e3?w=400"}'>
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1612874742237-6526221588e3?w=400" alt="Spaghetti Carbonara">
                    </div>
                    <div class="product-info">
                        <div class="product-name">Spaghetti Carbonara</div>
                        <div class="product-desc">Creamy pasta</div>
                        <div class="product-price">$35.3</div>
                    </div>
                </div>
                
                <div class="product-card" data-product='{"id":7,"name":"Ratatouille","price":26.7,"image":"https://images.unsplash.com/photo-1572453800999-e8d2d1589b7c?w=400"}'>
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1572453800999-e8d2d1589b7c?w=400" alt="Ratatouille">
                    </div>
                    <div class="product-info">
                        <div class="product-name">Ratatouille</div>
                        <div class="product-desc">Vegetable medley</div>
                        <div class="product-price">$26.7</div>
                    </div>
                </div>
                
                <div class="product-card" data-product='{"id":8,"name":"Kimchi Jigae","price":45.7,"image":"https://images.unsplash.com/photo-1582169296194-e4d644c48063?w=400"}'>
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1582169296194-e4d644c48063?w=400" alt="Kimchi Jigae">
                    </div>
                    <div class="product-info">
                        <div class="product-name">Kimchi Jigae</div>
                        <div class="product-desc">Korean stew</div>
                        <div class="product-price">$45.7</div>
                    </div>
                </div>
                
                <div class="product-card" data-product='{"id":9,"name":"Tofu Scramble","price":85.6,"image":"https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400"}'>
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400" alt="Tofu Scramble">
                    </div>
                    <div class="product-info">
                        <div class="product-name">Tofu Scramble</div>
                        <div class="product-desc">Healthy breakfast</div>
                        <div class="product-price">$85.6</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Sidebar - Invoice -->
        <div class="pos-invoice" id="posInvoice">
            <div class="invoice-header">
                <button class="invoice-close-btn" onclick="toggleInvoice()">
                    <i class="bi bi-x-lg"></i>
                </button>
                <h5>
                    <i class="bi bi-receipt"></i>
                    Invoice
                </h5>
            </div>
            
            <div class="invoice-items" id="invoiceItems">
                <div class="invoice-empty">
                    <i class="bi bi-cart-x"></i>
                    <p>Cart is empty</p>
                </div>
            </div>
            
            <div class="invoice-summary">
                <div class="summary-row">
                    <span>Sub Total</span>
                    <span id="subTotal">$0.0</span>
                </div>
                <div class="summary-row">
                    <span>Tax (7%)</span>
                    <span id="tax">$0.0</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span id="totalPayment">$0.0</span>
                </div>
                
                <div class="payment-methods">
                    <div class="payment-method active" data-method="credit">
                        <i class="bi bi-credit-card"></i>
                        <small>Card</small>
                    </div>
                    <div class="payment-method" data-method="paypal">
                        <i class="bi bi-paypal"></i>
                        <small>Paypal</small>
                    </div>
                    <div class="payment-method" data-method="cash">
                        <i class="bi bi-cash"></i>
                        <small>Cash</small>
                    </div>
                </div>
                
                <button class="btn-order" id="btnOrder">
                    <i class="bi bi-check-circle"></i> Place Order
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile Cart Toggle -->
    <button class="mobile-cart-toggle" onclick="toggleInvoice()">
        <i class="bi bi-cart3"></i>
        <span class="cart-badge" id="cartBadge" style="display: none;">0</span>
    </button>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let cart = [];
    
    function toggleInvoice() {
        document.getElementById('posInvoice').classList.toggle('show');
    }
    
    function updateCartBadge() {
        const badge = document.getElementById('cartBadge');
        const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
        if (totalItems > 0) {
            badge.textContent = totalItems;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
    
    function updateQty(productId, change) {
        const item = cart.find(i => i.id === productId);
        if (item) {
            item.qty += change;
            if (item.qty <= 0) {
                cart = cart.filter(i => i.id !== productId);
            }
            renderCart();
            updateTotals();
            updateCartBadge();
        }
    }
    
    function addToCart(product) {
        const existingItem = cart.find(i => i.id === product.id);
        if (existingItem) {
            existingItem.qty += 1;
        } else {
            cart.push({...product, qty: 1});
        }
        renderCart();
        updateTotals();
        updateCartBadge();
        
        if (window.innerWidth <= 768) {
            document.getElementById('posInvoice').classList.add('show');
        }
    }
    
    function renderCart() {
        const container = document.getElementById('invoiceItems');
        if (cart.length === 0) {
            container.innerHTML = `
                <div class="invoice-empty">
                    <i class="bi bi-cart-x"></i>
                    <p>Cart is empty</p>
                </div>
            `;
            document.getElementById('btnOrder').disabled = true;
            return;
        }
        
        document.getElementById('btnOrder').disabled = false;
        container.innerHTML = cart.map(item => `
            <div class="invoice-item">
                <img src="${item.image}" alt="${item.name}">
                <div class="invoice-item-details">
                    <div class="invoice-item-name">${item.name}</div>
                    <div class="invoice-item-meta">${item.qty}x item</div>
                    <div class="invoice-item-price">${(item.price * item.qty).toFixed(1)}</div>
                </div>
                <div class="invoice-item-actions">
                    <div class="invoice-item-qty">
                        <button onclick="updateQty(${item.id}, -1)">
                            <i class="bi bi-dash"></i>
                        </button>
                        <span class="qty-display">${item.qty}</span>
                        <button onclick="updateQty(${item.id}, 1)">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    }
    
    function updateTotals() {
        const subTotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        const tax = subTotal * 0.07;
        const total = subTotal + tax;
        
        document.getElementById('subTotal').textContent = ' + subTotal.toFixed(1);
        document.getElementById('tax').textContent = ' + tax.toFixed(1);
        document.getElementById('totalPayment').textContent = ' + total.toFixed(1);
    }
    
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function() {
            const productData = JSON.parse(this.dataset.product);
            addToCart(productData);
        });
    });
    
    document.querySelectorAll('.payment-method').forEach(method => {
        method.addEventListener('click', function() {
            document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    document.querySelectorAll('.tab-item, .category-item').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const category = this.dataset.category;
            
            const parentClass = this.classList.contains('tab-item') ? '.tab-item' : '.category-item';
            document.querySelectorAll(parentClass).forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            console.log('Filter by category:', category);
        });
    });
    
    document.getElementById('searchProduct').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(card => {
            const productData = JSON.parse(card.dataset.product);
            const isVisible = productData.name.toLowerCase().includes(searchTerm);
            card.style.display = isVisible ? 'flex' : 'none';
        });
    });
    
    document.getElementById('btnOrder').addEventListener('click', function() {
        if (cart.length === 0) return;
        
        const paymentMethod = document.querySelector('.payment-method.active').dataset.method;
        const total = document.getElementById('totalPayment').textContent;
        
        if (confirm(`Confirm order with ${paymentMethod}?\nTotal: ${total}`)) {
            alert('Order placed successfully!');
            cart = [];
            renderCart();
            updateTotals();
            updateCartBadge();
            
            if (window.innerWidth <= 768) {
                document.getElementById('posInvoice').classList.remove('show');
            }
        }
    });
    
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            const invoice = document.getElementById('posInvoice');
            const toggleBtn = document.querySelector('.mobile-cart-toggle');
            const closeBtn = document.querySelector('.invoice-close-btn');
            
            if (invoice.classList.contains('show') && 
                !invoice.contains(e.target) && 
                !toggleBtn.contains(e.target) && 
                !e.target.closest('.product-card')) {
                invoice.classList.remove('show');
            }
        }
    });
    
    renderCart();
    updateTotals();
    updateCartBadge();
</script>
<?= $this->endSection() ?>