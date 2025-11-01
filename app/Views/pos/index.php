<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    body {
        background-color: #f8f9fa;
    }
    
    .pos-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .product-grid {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }
    
    .product-card {
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    }
    
    .cart-section {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 1rem;
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }
    
    .total-section {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        position: sticky;
        bottom: 0;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="pos-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-0">
                    <i class="bi bi-cart"></i> Point of Sale
                </h3>
            </div>
            <div class="d-flex align-items-center">
                <span class="me-3">
                    <i class="bi bi-person-circle"></i> <?= esc($user->username) ?>
                </span>
                <a href="/logout" class="btn btn-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main POS Interface -->
<div class="container-fluid mt-3">
    <div class="row">
        <!-- Products Section (Left) -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" placeholder="Cari produk atau scan barcode...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <select class="form-select">
                                <option selected>Semua Kategori</option>
                                <option>Makanan</option>
                                <option>Minuman</option>
                                <option>Snack</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body product-grid">
                    <div class="row g-3">
                        <!-- Sample Product Cards -->
                        <div class="col-md-3">
                            <div class="card product-card">
                                <div class="card-body text-center">
                                    <i class="bi bi-box" style="font-size: 3rem; color: #667eea;"></i>
                                    <h6 class="mt-2">Product Name</h6>
                                    <p class="text-muted mb-0">Rp 10,000</p>
                                    <small class="text-success">Stock: 50</small>
                                </div>
                            </div>
                        </div>
                        <!-- More products will be loaded here -->
                        <div class="col-12 text-center text-muted py-5">
                            <i class="bi bi-inbox" style="font-size: 4rem;"></i>
                            <p class="mt-3">Produk akan ditampilkan di sini</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart Section (Right) -->
        <div class="col-md-4">
            <div class="cart-section mb-3">
                <h5 class="mb-3">
                    <i class="bi bi-cart3"></i> Cart
                </h5>
                
                <div class="text-center text-muted py-5">
                    <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                    <p class="mt-3">Keranjang masih kosong</p>
                </div>

                <!-- Cart items will be displayed here -->
            </div>

            <div class="total-section">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <strong>Rp 0</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Diskon:</span>
                    <strong class="text-success">Rp 0</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Pajak:</span>
                    <strong>Rp 0</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <h5>Total:</h5>
                    <h5 class="text-primary">Rp 0</h5>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-success btn-lg" disabled>
                        <i class="bi bi-cash"></i> Process Payment
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Clear Cart
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
