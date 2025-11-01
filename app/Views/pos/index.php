<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<style>
    /* Reset Mazer */
    #main {
        padding: 0 !important;
        margin-left: 0 !important;
    }
    
    #sidebar {
        display: none !important;
    }
    
    /* Container */
    .pos-container {
        display: flex;
        height: 100vh;
        background: #f5f5f5;
    }
    
    /* Header */
    .pos-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 60px;
        background: white;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.5rem;
        z-index: 100;
    }
    
    .pos-header .logo {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2563eb;
    }
    
    .pos-header .user-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .pos-header .user-name {
        font-size: 0.9rem;
        font-weight: 600;
    }
    
    .btn-logout {
        padding: 0.5rem 1rem;
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.9rem;
    }
    
    /* Main Layout */
    .pos-wrapper {
        display: flex;
        width: 100%;
        margin-top: 60px;
        height: calc(100vh - 60px);
    }
    
    /* Products Section */
    .pos-products {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    .search-bar {
        padding: 1rem 1.5rem;
        background: white;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .search-bar input {
        width: 100%;
        max-width: 400px;
        padding: 0.6rem 1rem;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 0.9rem;
    }
    
    .search-bar input:focus {
        outline: none;
        border-color: #2563eb;
    }
    
    .products-grid {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1rem;
        align-content: start;
    }
    
    .product-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
    }
    
    .product-card:hover {
        border-color: #2563eb;
        transform: translateY(-2px);
    }
    
    .product-card img {
        width: 100%;
        height: 120px;
        object-fit: cover;
    }
    
    .product-info {
        padding: 0.75rem;
    }
    
    .product-name {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .product-price {
        color: #2563eb;
        font-weight: 700;
        font-size: 1rem;
    }
    
    /* Cart Section */
    .pos-cart {
        width: 350px;
        background: white;
        border-left: 1px solid #e0e0e0;
        display: flex;
        flex-direction: column;
    }
    
    .cart-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .cart-header h5 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    .cart-items {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
    }
    
    .cart-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #9ca3af;
    }
    
    .cart-empty i {
        font-size: 3rem;
        margin-bottom: 0.5rem;
    }
    
    .cart-item {
        display: flex;
        gap: 0.75rem;
        padding: 0.75rem;
        background: #f9fafb;
        border-radius: 6px;
        margin-bottom: 0.75rem;
    }
    
    .cart-item img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
    }
    
    .cart-item-info {
        flex: 1;
    }
    
    .cart-item-name {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }
    
    .cart-item-price {
        color: #2563eb;
        font-weight: 600;
    }
    
    .cart-item-qty {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    
    .cart-item-qty button {
        width: 24px;
        height: 24px;
        border: none;
        background: #2563eb;
        color: white;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }
    
    .cart-item-qty span {
        min-width: 30px;
        text-align: center;
        font-weight: 600;
    }
    
    .cart-summary {
        padding: 1.5rem;
        border-top: 1px solid #e0e0e0;
        background: #f9fafb;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        font-size: 0.9rem;
    }
    
    .summary-row.total {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2563eb;
        padding-top: 0.75rem;
        border-top: 2px solid #e0e0e0;
        margin-top: 0.5rem;
    }
    
    .btn-checkout {
        width: 100%;
        padding: 0.85rem;
        background: #2563eb;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        margin-top: 1rem;
    }
    
    .btn-checkout:hover {
        background: #1d4ed8;
    }
    
    .btn-checkout:disabled {
        background: #d1d5db;
        cursor: not-allowed;
    }
    
    /* Mobile */
    @media (max-width: 768px) {
        .pos-header .user-name {
            display: none;
        }
        
        .pos-cart {
            position: fixed;
            top: 60px;
            right: -100%;
            width: 100%;
            max-width: 350px;
            height: calc(100vh - 60px);
            z-index: 99;
            transition: right 0.3s;
            box-shadow: -4px 0 12px rgba(0,0,0,0.1);
        }
        
        .pos-cart.show {
            right: 0;
        }
        
        .products-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    .mobile-cart-btn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 56px;
        height: 56px;
        background: #2563eb;
        color: white;
        border: none;
        border-radius: 50%;
        font-size: 1.5rem;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(37,99,235,0.4);
        z-index: 98;
    }
    
    .mobile-cart-btn .badge {
        position: absolute;
        top: 0;
        right: 0;
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
    
    @media (max-width: 768px) {
        .mobile-cart-btn {
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="pos-container">
    <!-- Header -->
    <div class="pos-header">
        <div class="logo">🏪 POS System</div>
        <div class="user-info">
            <span class="user-name"><?= esc($user->username) ?></span>
            <a href="<?= url_to('logout') ?>" class="btn-logout">Logout</a>
        </div>
    </div>
    
    <div class="pos-wrapper">
        <!-- Products -->
        <div class="pos-products">
            <div class="search-bar">
                <input type="text" placeholder="Search products..." id="searchInput">
            </div>
            
            <div class="products-grid" id="productsGrid">
                <div class="product-card" data-product='{"id":1,"name":"Pasta Bolognese","price":50.5,"image":"https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=400"}'>
                    <img src="https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=400" alt="">
                    <div class="product-info">
                        <div class="product-name">Pasta Bolognese</div>
                        <div class="product-price">$50.50</div>
                    </div>
                </div>
                
                <div class="product-card" data-product='{"id":2,"name":"Fried Chicken","price":45.7,"image":"https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=400"}'>
                    <img src="https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=400" alt="">
                    <div class="product-info">
                        <div class="product-name">Fried Chicken</div>
                        <div class="product-price">$45.70</div>
                    </div>
                </div>
                
                <div class="product-card" data-product='{"id":3,"name":"Grilled Steak","price":80.0,"image":"https://images.unsplash.com/photo-1600891964092-4316c288032e?w=400"}'>
                    <img src="https://images.unsplash.com/photo-1600891964092-4316c288032e?w=400" alt="">
                    <div class="product-info">
                        <div class="product-name">Grilled Steak</div>
                        <div class="product-price">$80.00</div>
                    </div>
                </div>
                
                <div class="product-card" data-product='{"id":4,"name":"Fish And Chips","price":90.4,"image":"https://images.unsplash.com/photo-1579208575657-c595a05383b7?w=400"}'>
                    <img src="https://images.unsplash.com/photo-1579208575657-c595a05383b7?w=400" alt="">
                    <div class="product-info">
                        <div class="product-name">Fish And Chips</div>
                        <div class="product-price">$90.40</div>
                    </div>
                </div>
                
                <div class="product-card" data-product='{"id":5,"name":"Beef Bourguignon","price":75.5,"image":"https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?w=400"}'>
                    <img src="https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?w=400" alt="">
                    <div class="product-info">
                        <div class="product-name">Beef Bourguignon</div>
                        <div class="product-price">$75.50</div>
                    </div>
                </div>
                
                <div class="product-card" data-product='{"id":6,"name":"Carbonara","price":35.3,"image":"https://images.unsplash.com/photo-1612874742237-6526221588e3?w=400"}'>
                    <img src="https://images.unsplash.com/photo-1612874742237-6526221588e3?w=400" alt="">
                    <div class="product-info">
                        <div class="product-name">Carbonara</div>
                        <div class="product-price">$35.30</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Cart -->
        <div class="pos-cart" id="posCart">
            <div class="cart-header">
                <h5>Cart</h5>
            </div>
            
            <div class="cart-items" id="cartItems">
                <div class="cart-empty">
                    <i class="bi bi-cart-x"></i>
                    <p>Cart is empty</p>
                </div>
            </div>
            
            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="subtotal">$0.00</span>
                </div>
                <div class="summary-row">
                    <span>Tax (7%)</span>
                    <span id="tax">$0.00</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span id="total">$0.00</span>
                </div>
                <button class="btn-checkout" id="btnCheckout" disabled>Checkout</button>
            </div>
        </div>
    </div>
    
    <!-- Mobile Cart Button -->
    <button class="mobile-cart-btn" onclick="toggleCart()">
        <i class="bi bi-cart3"></i>
        <span class="badge" id="cartBadge" style="display:none;">0</span>
    </button>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let cart = [];

function toggleCart() {
    document.getElementById('posCart').classList.toggle('show');
}

function updateCart() {
    const container = document.getElementById('cartItems');
    
    if (cart.length === 0) {
        container.innerHTML = '<div class="cart-empty"><i class="bi bi-cart-x"></i><p>Cart is empty</p></div>';
        document.getElementById('btnCheckout').disabled = true;
        return;
    }
    
    document.getElementById('btnCheckout').disabled = false;
    
    container.innerHTML = cart.map(item => `
        <div class="cart-item">
            <img src="${item.image}" alt="">
            <div class="cart-item-info">
                <div class="cart-item-name">${item.name}</div>
                <div class="cart-item-price">$${(item.price * item.qty).toFixed(2)}</div>
                <div class="cart-item-qty">
                    <button onclick="updateQty(${item.id}, -1)">-</button>
                    <span>${item.qty}</span>
                    <button onclick="updateQty(${item.id}, 1)">+</button>
                </div>
            </div>
        </div>
    `).join('');
    
    updateTotals();
}

function updateQty(id, change) {
    const item = cart.find(i => i.id === id);
    if (item) {
        item.qty += change;
        if (item.qty <= 0) {
            cart = cart.filter(i => i.id !== id);
        }
        updateCart();
        updateBadge();
    }
}

function updateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    const tax = subtotal * 0.07;
    const total = subtotal + tax;
    
    document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('tax').textContent = `$${tax.toFixed(2)}`;
    document.getElementById('total').textContent = `$${total.toFixed(2)}`;
}

function updateBadge() {
    const badge = document.getElementById('cartBadge');
    const total = cart.reduce((sum, item) => sum + item.qty, 0);
    if (total > 0) {
        badge.textContent = total;
        badge.style.display = 'flex';
    } else {
        badge.style.display = 'none';
    }
}

// Add to cart
document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('click', function() {
        const product = JSON.parse(this.dataset.product);
        const existing = cart.find(i => i.id === product.id);
        
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({...product, qty: 1});
        }
        
        updateCart();
        updateBadge();
        
        if (window.innerWidth <= 768) {
            document.getElementById('posCart').classList.add('show');
        }
    });
});

// Search
document.getElementById('searchInput').addEventListener('input', function(e) {
    const search = e.target.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
        const product = JSON.parse(card.dataset.product);
        card.style.display = product.name.toLowerCase().includes(search) ? 'block' : 'none';
    });
});

// Checkout
document.getElementById('btnCheckout').addEventListener('click', function() {
    if (cart.length === 0) return;
    
    const total = document.getElementById('total').textContent;
    if (confirm(`Confirm checkout?\nTotal: ${total}`)) {
        alert('Order placed successfully!');
        cart = [];
        updateCart();
        updateBadge();
        
        if (window.innerWidth <= 768) {
            document.getElementById('posCart').classList.remove('show');
        }
    }
});

updateCart();
</script>
<?= $this->endSection() ?>