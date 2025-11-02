<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<!-- External POS CSS -->
<style>
    /* === Mazer Theme Color Override - Change from #435EBE to #3772F0 === */
    :root {
        --bs-primary: #3772F0 !important;
        --bs-primary-rgb: 55, 114, 240 !important;
    }

    /* Buttons */
    .btn-primary {
        background-color: #3772F0 !important;
        border-color: #3772F0 !important;
    }

    .btn-primary:hover,
    .btn-primary:focus,
    .btn-primary:active {
        background-color: #2563eb !important;
        border-color: #2563eb !important;
    }

    .btn-outline-primary {
        color: #3772F0 !important;
        border-color: #3772F0 !important;
    }

    .btn-outline-primary:hover,
    .btn-outline-primary:focus,
    .btn-outline-primary:active {
        background-color: #3772F0 !important;
        border-color: #3772F0 !important;
        color: #fff !important;
    }

    /* Text & Background */
    .text-primary {
        color: #3772F0 !important;
    }

    .bg-primary {
        background-color: #3772F0 !important;
    }

    .border-primary {
        border-color: #3772F0 !important;
    }

    /* Badges */
    .badge-primary,
    .badge.bg-primary {
        background-color: #3772F0 !important;
    }

    /* Links */
    a.text-primary:hover,
    a.text-primary:focus {
        color: #2563eb !important;
    }

    /* Sidebar Active Menu */
    .sidebar-wrapper .sidebar-item.active>a {
        background-color: #3772F0 !important;
    }

    .sidebar-wrapper .sidebar-link:hover {
        background-color: rgba(55, 114, 240, 0.1) !important;
    }

    /* Form Controls Focus */
    .form-control:focus,
    .form-select:focus {
        border-color: #3772F0 !important;
        box-shadow: 0 0 0 0.25rem rgba(55, 114, 240, 0.25) !important;
    }

    .form-check-input:checked {
        background-color: #3772F0 !important;
        border-color: #3772F0 !important;
    }

    /* Alerts */
    .alert-primary {
        background-color: rgba(55, 114, 240, 0.1) !important;
        border-color: #3772F0 !important;
        color: #2563eb !important;
    }

    /* Progress Bar */
    .progress-bar {
        background-color: #3772F0 !important;
    }

    /* Pagination */
    .page-link {
        color: #3772F0 !important;
    }

    .page-item.active .page-link {
        background-color: #3772F0 !important;
        border-color: #3772F0 !important;
    }

    /* Dropdown */
    .dropdown-item.active,
    .dropdown-item:active {
        background-color: #3772F0 !important;
    }

    /* Nav Tabs/Pills */
    .nav-pills .nav-link.active {
        background-color: #3772F0 !important;
    }

    .nav-tabs .nav-link.active {
        color: #3772F0 !important;
        border-bottom-color: #3772F0 !important;
    }

    /* List Group */
    .list-group-item.active {
        background-color: #3772F0 !important;
        border-color: #3772F0 !important;
    }

    /* Spinners */
    .spinner-border.text-primary,
    .spinner-grow.text-primary {
        color: #3772F0 !important;
    }
</style>

<link rel="stylesheet" href="<?= base_url('assets/css/pos.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Top Header -->
<div class="pos-top-header">
    <div class="user-profile">
        <img src="https://ui-avatars.com/api/?name=<?= urlencode(auth()->user()->username ?? 'User') ?>&background=3772F0&color=fff"
            alt="User" class="user-avatar">
        <div class="user-info">
            <h5><?= esc(auth()->user()->username ?? 'Cashier') ?></h5>
            <p><?= esc($outlet['name'] ?? 'Outlet') ?></p>
        </div>
    </div>

    <a href="<?= url_to('logout') ?>" class="btn-logout">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
    </a>
</div>

<!-- Main Container -->
<div class="pos-container">
    <!-- Menu Panel -->
    <div class="menu-panel">
        <!-- Category Tabs -->
        <div class="category-tabs">
            <!-- All Menu Tab -->
            <div class="category-tab active" data-category="all" data-category-id="0">
                <i class="bi bi-grid-3x3-gap-fill"></i>
                <span>All Menu</span>
                <span class="badge"><?= count($products) ?></span>
            </div>

            <!-- Dynamic Category Tabs -->
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <div class="category-tab"
                        data-category="<?= esc(strtolower(str_replace(' ', '-', $category['name']))) ?>"
                        data-category-id="<?= $category['id'] ?>">
                        <i class="<?= $category['icon'] ?>"></i>
                        <span><?= esc($category['name']) ?></span>
                        <span class="badge"><?= $category['product_count'] ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Menu Grid -->
        <div class="menu-grid-wrapper">
            <div class="menu-grid" id="menuGrid">
                <?php if (empty($products)): ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 4rem 2rem;">
                        <i class="bi bi-inbox" style="font-size: 4rem; opacity: 0.3; color: #adb5bd;"></i>
                        <p style="margin-top: 1rem; color: #6c757d;">No products available</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <?php
                        $stock = (int)$product['stock'];

                        // Handle image path - check if it already contains 'uploads/'
                        $imagePath = '';
                        if (!empty($product["image"])) {
                            // If image path already starts with 'uploads/', use it directly
                            if (strpos($product["image"], 'uploads/') === 0) {
                                $imagePath = base_url($product["image"]);
                            } else {
                                // Otherwise, prepend 'uploads/products/'
                                $imagePath = base_url('uploads/products/' . $product["image"]);
                            }
                        } else {
                            $imagePath = 'https://via.placeholder.com/400x300/667eea/ffffff?text=' . urlencode(substr($product["name"], 0, 1));
                        }

                        $productData = [
                            "id" => $product["id"],
                            "name" => $product["name"],
                            "sku" => $product["sku"],
                            "price" => (float)$product["price"],
                            "cost_price" => (float)$product["cost_price"],
                            "tax_type" => $product["tax_type"],
                            "tax_rate" => (float)$product["tax_rate"],
                            "tax_included" => (bool)$product["tax_included"],
                            "stock" => $stock,
                            "image" => $imagePath
                        ];

                        // Check if product has active promotion
                        $hasPromo = false;
                        $promoLabel = '';
                        $promoData = null;
                        foreach ($promotions as $promo) {
                            if (empty($promo['product_ids']) || in_array($product['id'], $promo['product_ids'])) {
                                $hasPromo = true;
                                $promoData = $promo; // Store promo data
                                if ($promo['discount_type'] === 'percentage') {
                                    $promoLabel = $promo['discount_value'] . '%';
                                } else {
                                    $promoLabel = 'Rp ' . number_format($promo['discount_value'], 0, ',', '.');
                                }
                                break;
                            }
                        }
                        ?>
                        <div class="menu-card"
                            data-product='<?= json_encode($productData) ?>'
                            data-product-id="<?= $product['id'] ?>"
                            data-stock="<?= $stock ?>"
                            data-category-id="<?= $product['category_id'] ?>">
                            <div class="menu-card-image">
                                <?php if ($hasPromo): ?>
                                    <span class="promo-badge"
                                        style="cursor: pointer;"
                                        onclick="showPromoDetails(<?= htmlspecialchars(json_encode($promoData)) ?>)"
                                        title="Click to see promotion details">
                                        <i class="bi bi-tag-fill"></i> <?= $promoLabel ?> <i class="bi bi-info-circle-fill ms-1" style="font-size: 0.7rem; opacity: 0.9;"></i>
                                    </span>
                                <?php endif; ?>
                                <img src="<?= $productData['image'] ?>"
                                    alt="<?= esc($product['name']) ?>"
                                    style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="menu-card-body">
                                <div class="menu-card-name"><?= esc($product['name']) ?></div>
                                <div class="menu-card-desc">
                                    <?= esc(!empty($product['description']) ? $product['description'] : $product['category_name'] ?? 'Produk berkualitas') ?>
                                </div>

  <!-- Stock Badge -->
<div style="margin-bottom: 0.5rem;">
    <?php if ($stock > 10): ?>
        <span class="stock-badge bg-success text-white" style="background: #198754; color: #fff; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
            <i class="bi bi-box-seam"></i> Stok: <?= $stock ?>
        </span>
    <?php elseif ($stock > 0): ?>
        <span class="stock-badge bg-warning text-white" style="background: #ffc107; color: #fff; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
            <i class="bi bi-exclamation-triangle"></i> Stok: <?= $stock ?>
        </span>
    <?php else: ?>
        <span class="stock-badge bg-danger text-white" style="background: #dc3545; color: #fff; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
            <i class="bi bi-x-circle"></i> Habis
        </span>
    <?php endif; ?>
</div>


                                <div class="menu-card-footer">
                                    <div class="menu-price">
                                        Rp <?= number_format($product['price'], 0, ',', '.') ?>
                                    </div>
                                    <div class="menu-actions">
                                        <button class="add-btn" onclick="addToCart(<?= htmlspecialchars(json_encode($productData)) ?>)" <?= $stock <= 0 ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '' ?>>
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Invoice Panel -->
    <div class="invoice-panel" id="invoicePanel">
        <button class="invoice-close-btn" onclick="toggleInvoice()">
            <i class="bi bi-x-lg"></i>
        </button>

        <div class="invoice-header">
            <h3>Keranjang</h3>
        </div>

        <div class="invoice-items" id="invoiceItems">
            <div class="invoice-empty">
                <i class="bi bi-receipt"></i>
                <p>Belum ada item</p>
            </div>
        </div>

        <div class="payment-summary">
            <h4>Ringkasan Pembayaran</h4>

            <div class="summary-row subtotal">
                <span>Sub Total</span>
                <span id="subTotal">Rp 0</span>
            </div>
            <div class="summary-row discount">
                <span>Diskon <i class="bi bi-tag-fill" style="font-size: 0.75rem; color: #28a745;"></i></span>
                <span id="discountAmount" style="color: #28a745; font-weight: 600;">Rp 0</span>
            </div>

            <!-- Tax yang ditambahkan (dari produk tax_included = false) -->
            <div class="summary-row tax" id="taxAddedRow" style="display: none;">
                <span>Pajak (PPN) <i class="bi bi-receipt" style="font-size: 0.75rem; opacity: 0.6;"></i></span>
                <span id="taxAmount">Rp 0</span>
            </div>

            <!-- Info: Tax yang sudah termasuk (dari produk tax_included = true) -->
            <div class="summary-row tax-info" id="taxIncludedRow" style="display: none; font-size: 0.875rem; opacity: 0.8;">
                <span>
                    <i class="bi bi-info-circle" style="font-size: 0.75rem;"></i>
                    PPN termasuk
                </span>
                <span id="taxIncludedAmount" style="font-style: italic;">Rp 0</span>
            </div>

            <hr style="border-top: 2px solid #e9ecef; margin: 0.75rem 0;">
            <div class="summary-row total">
                <span>Total Pembayaran</span>
                <span class="amount" id="totalPayment">Rp 0</span>
            </div>

            <!-- Order Type Selection -->
            <div style="margin: 1rem 0;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #495057; margin-bottom: 0.5rem;">
                    <i class="bi bi-shop"></i> Tipe Pesanan
                </label>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem;">
                    <button type="button" class="order-type-btn active" data-type="dine_in" style="padding: 0.5rem; background: #3772F0; color: white; border: none; border-radius: 6px; font-size: 0.75rem; cursor: pointer; transition: all 0.2s;">
                        <i class="bi bi-shop"></i><br>Dine In
                    </button>
                    <button type="button" class="order-type-btn" data-type="take_away" style="padding: 0.5rem; background: #e9ecef; color: #495057; border: none; border-radius: 6px; font-size: 0.75rem; cursor: pointer; transition: all 0.2s;">
                        <i class="bi bi-bag"></i><br>Take Away
                    </button>
                    <button type="button" class="order-type-btn" data-type="delivery" style="padding: 0.5rem; background: #e9ecef; color: #495057; border: none; border-radius: 6px; font-size: 0.75rem; cursor: pointer; transition: all 0.2s;">
                        <i class="bi bi-truck"></i><br>Delivery
                    </button>
                </div>
            </div>

            <!-- Dynamic Fields (Table Number / Customer Name) -->
            <div id="orderDetailsFields">
                <div id="tableNumberField" style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #495057; margin-bottom: 0.5rem;">
                        <i class="bi bi-table"></i> Nomor Meja
                    </label>
                    <input type="text" id="tableNumber" placeholder="Contoh: A1, B5, 12" style="width: 100%; padding: 0.625rem; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.875rem;">
                </div>
                <div id="customerNameField" style="margin-bottom: 1rem; display: none;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #495057; margin-bottom: 0.5rem;">
                        <i class="bi bi-person"></i> Nama Pelanggan
                    </label>
                    <input type="text" id="customerName" placeholder="Masukkan nama pelanggan" style="width: 100%; padding: 0.625rem; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.875rem;">
                </div>
            </div>

            <div class="payment-methods">
                <div class="payment-method active" data-method="credit">
                    <i class="bi bi-credit-card-fill"></i>
                    <span>Kartu Kredit</span>
                </div>
                <div class="payment-method" data-method="e-wallet">
                    <i class="bi bi-wallet2"></i>
                    <span>E-Wallet</span>
                </div>
                <div class="payment-method" data-method="cash">
                    <i class="bi bi-cash-stack"></i>
                    <span>Tunai</span>
                </div>
            </div>

            <button class="btn-place-order" id="btnPlaceOrder" disabled>
                Proses Pesanan
            </button>
        </div>
    </div>
</div>

<!-- Backdrop Overlay (for mobile/tablet) -->
<div class="invoice-backdrop" id="invoiceBackdrop" onclick="toggleInvoice()"></div>

<!-- Mobile Cart Button -->
<button class="mobile-cart-btn" id="mobileCartBtn" onclick="toggleInvoice()">
    <i class="bi bi-cart3"></i>
    <span class="mobile-cart-badge" id="cartBadge" style="display: none;">0</span>
</button>

<!-- Success Order Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: none; box-shadow: 0 8px 32px rgba(55, 114, 240, 0.2); border-radius: 16px; overflow: hidden;">
            <div class="modal-body text-center" style="padding: 2.5rem 2rem;">
                <!-- Success Icon Circle -->
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="white" viewBox="0 0 16 16" style="display: block;">
                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
                    </svg>
                </div>

                <!-- Success Title -->
                <h3 style="color: #28a745; font-weight: 700; margin-bottom: 1rem; font-size: 1.75rem;">Pesanan Berhasil!</h3>

                <!-- Order Details Box -->
                <div id="successModalContent" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; padding: 1.5rem; text-align: left; margin-bottom: 1.5rem; border: 1px solid #dee2e6;">
                    <!-- Content will be injected here via JavaScript -->
                </div>

                <!-- OK Button -->
                <button type="button" class="btn btn-primary" style="min-width: 150px; padding: 0.75rem 2rem; background: linear-gradient(135deg, #3772F0 0%, #2563eb 100%); border: none; font-weight: 600; border-radius: 8px; box-shadow: 0 4px 12px rgba(55, 114, 240, 0.3); transition: all 0.3s ease;"
                    onclick="bootstrap.Modal.getInstance(document.getElementById('successModal')).hide();"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(55, 114, 240, 0.4)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(55, 114, 240, 0.3)'">
                    <i class="bi bi-check-circle me-2"></i> OK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Promotion Details Modal -->
<div class="modal fade" id="promoModal" tabindex="-1" aria-labelledby="promoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border: none;">
                <h5 class="modal-title text-white" id="promoModalLabel">
                    <i class="bi bi-tag-fill me-2"></i>Detail Promo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="promoModalBody">
                <!-- Content will be injected here -->
            </div>
        </div>
    </div>
</div>

<!-- Cash Payment Modal -->
<div class="modal fade" id="cashModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white" style="background-color: #3772F0 !important;">
                <h5 class="modal-title text-white">
                    <i class="bi bi-cash-stack me-2"></i> Pembayaran Tunai
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body bg-white" style="padding: 1.75rem;">
                <div class="text-center mb-4">
                    <h6 class="text-secondary mb-1">Total Pembayaran</h6>
                    <h1 class="fw-bold text-primary" id="cashModalTotal" style="font-size: 2.5rem;">Rp 0</h1>
                </div>

                <div class="mb-4">
                    <label for="cashInput" class="form-label fw-semibold text-primary">
                        <i class="bi bi-wallet2"></i> Masukkan Jumlah Uang
                    </label>
                    <input type="number" class="form-control form-control-lg text-end" id="cashInput"
                        placeholder="0" min="0" step="1000" autofocus
                        style="font-size: 1.5rem; font-weight: 600;">
                    <div class="form-text">Masukkan jumlah uang yang diterima dari pelanggan</div>
                </div>

                <!-- Quick Amount Buttons -->
                <div class="mb-4">
                    <label class="form-label fw-semibold text-primary mb-2">
                        <i class="bi bi-lightning-charge-fill"></i> Nominal Cepat
                    </label>
                    <div class="d-grid" style="grid-template-columns: repeat(3, 1fr); gap: 0.5rem;">
                        <button type="button" class="btn btn-outline-primary quick-amount-btn fw-semibold py-2" data-amount="10000">10K</button>
                        <button type="button" class="btn btn-outline-primary quick-amount-btn fw-semibold py-2" data-amount="20000">20K</button>
                        <button type="button" class="btn btn-outline-primary quick-amount-btn fw-semibold py-2" data-amount="50000">50K</button>
                        <button type="button" class="btn btn-outline-primary quick-amount-btn fw-semibold py-2" data-amount="100000">100K</button>
                        <button type="button" class="btn btn-outline-primary quick-amount-btn fw-semibold py-2" data-amount="200000">200K</button>
                        <button type="button" class="btn btn-outline-primary quick-amount-btn fw-semibold py-2" data-amount="500000">500K</button>
                    </div>

                    <button type="button" class="btn btn-primary w-100 mt-3 fw-semibold" id="btnExactAmount">
                        <i class="bi bi-check2-square"></i> Uang Pas
                    </button>
                </div>

                <div id="changeDisplay" class="p-3 rounded-3 text-center" style="display: none; background-color: rgba(55, 114, 240, 0.08);">
                    <span class="text-secondary d-block mb-1"><i class="bi bi-arrow-return-left"></i> Kembalian</span>
                    <h2 class="fw-bold text-primary" id="changeAmount">Rp 0</h2>
                </div>

                <div id="insufficientAlert" class="p-3 rounded-3 text-center" style="display: none; background-color: rgba(220, 53, 69, 0.1);">
                    <span class="text-danger fw-semibold"><i class="bi bi-exclamation-triangle"></i> Uang tidak cukup!</span>
                </div>
            </div>

            <div class="modal-footer bg-light border-0 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary fw-semibold" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Batal
                </button>
                <button type="button" class="btn btn-primary fw-semibold px-4" id="btnConfirmCash" disabled>
                    <i class="bi bi-check-circle"></i> Konfirmasi Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Print Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-receipt"></i> Struk Belanja
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="receiptContent" style="background: white; padding: 1rem;">
                    <!-- Receipt will be generated here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="printReceipt()">
                    <i class="bi bi-printer"></i> Cetak Struk
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Bootstrap 5 JS for Modal -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Pusher JS Client -->
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>

<!-- External POS JavaScript -->
<script src="<?= base_url('assets/js/pos.js') ?>"></script>

<!-- Initialize POS System with server data -->
<script>
    // Pass PHP data to JavaScript
    initializePOS(
        <?= json_encode($promotions) ?>,
        '<?= base_url('pos/checkout') ?>', {
            name: '<?= esc($outlet['name'] ?? 'OUTLET') ?>',
            address: '<?= esc($outlet['address'] ?? '') ?>',
            phone: '<?= esc($outlet['phone'] ?? '') ?>'
        }, {
            username: '<?= esc(auth()->user()->username ?? 'System') ?>'
        }
    );

    // Initialize Pusher for real-time stock updates
    (function() {
        // Enable pusher logging for development (disable in production)
        <?php if (ENVIRONMENT === 'development'): ?>
        Pusher.logToConsole = true;
        <?php endif; ?>

        // Initialize Pusher
        const pusher = new Pusher('<?= env('pusher.appKey', '16c9b2af70ac324000d9') ?>', {
            cluster: '<?= env('pusher.appCluster', 'ap1') ?>',
            encrypted: true
        });

        // Subscribe to outlet-specific stock updates channel
        const outletId = <?= auth()->user()->outlet_id ?? 0 ?>;
        const channel = pusher.subscribe(`stock-updates-${outletId}`);

        // Listen for stock-updated event
        channel.bind('stock-updated', function(data) {
            console.log('Stock update received:', data);
            
            // Update stock display in POS
            updateProductStock(data.product_id, data.new_stock);
            
            // Show notification
            showStockUpdateNotification(data);
        });

        /**
         * Update product stock in POS interface
         */
        function updateProductStock(productId, newStock) {
            // Find product card by data-product-id attribute
            const productCard = document.querySelector(`[data-product-id="${productId}"]`);
            
            if (!productCard) {
                console.log(`Product ${productId} not found in current view`);
                return;
            }

            // Update stock badge
            const stockBadge = productCard.querySelector('.stock-badge');
            if (stockBadge) {
                stockBadge.textContent = `Stok: ${newStock}`;
                
                // Update badge color based on stock level
                stockBadge.classList.remove('bg-danger', 'bg-warning', 'bg-success');
                if (newStock <= 0) {
                    stockBadge.classList.add('bg-danger');
                } else if (newStock < 10) {
                    stockBadge.classList.add('bg-warning');
                } else {
                    stockBadge.classList.add('bg-success');
                }
            }

            // Update data attribute for JavaScript access
            productCard.dataset.stock = newStock;

            // If stock is 0, disable the product card
            if (newStock <= 0) {
                productCard.classList.add('opacity-50');
                productCard.style.pointerEvents = 'none';
            } else {
                productCard.classList.remove('opacity-50');
                productCard.style.pointerEvents = 'auto';
            }
        }

        /**
         * Show notification when stock is updated
         */
        function showStockUpdateNotification(data) {
            // Create toast notification
            const toastHtml = `
                <div class="toast align-items-center text-white bg-info border-0" role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; top: 80px; right: 20px; z-index: 9999;">
                    <div class="d-flex">
                        <div class="toast-body">
                            <strong>${data.product_name}</strong><br>
                            Stok diupdate menjadi: <strong>${data.new_stock}</strong>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            
            // Append to body
            const toastContainer = document.createElement('div');
            toastContainer.innerHTML = toastHtml;
            document.body.appendChild(toastContainer);
            
            // Show toast
            const toastElement = toastContainer.querySelector('.toast');
            const toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: 5000
            });
            toast.show();
            
            // Remove from DOM after hidden
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastContainer.remove();
            });
        }

        console.log('Pusher initialized and listening on channel: stock-updates-' + outletId);
    })();
</script>
<?= $this->endSection() ?>