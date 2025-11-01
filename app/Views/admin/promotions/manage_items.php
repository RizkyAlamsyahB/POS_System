<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<style>
.product-grid {
    max-height: 500px;
    overflow-y: auto;
}
.product-item {
    cursor: pointer;
    transition: all 0.3s;
}
.product-item:hover {
    background-color: #f8f9fa;
}
.product-item.selected {
    background-color: #e7f3ff;
    border-color: #0d6efd !important;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Kelola Item Promosi</h3>
                <p class="text-subtitle text-muted"><?= esc($promotion['name']) ?> (<?= esc($promotion['code']) ?>)</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="/admin/promotions">Promosi</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Kelola Item</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <!-- Promotion Info -->
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Periode:</strong><br>
                                <?= date('d/m/Y', strtotime($promotion['start_date'])) ?> - <?= date('d/m/Y', strtotime($promotion['end_date'])) ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Diskon:</strong><br>
                                <?php if ($promotion['discount_type'] === 'percentage'): ?>
                                    <?= number_format($promotion['discount_value'], 0) ?>%
                                    <?php if ($promotion['max_discount']): ?>
                                        (Max Rp <?= number_format($promotion['max_discount'], 0, ',', '.') ?>)
                                    <?php endif ?>
                                <?php else: ?>
                                    Rp <?= number_format($promotion['discount_value'], 0, ',', '.') ?>
                                <?php endif ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Outlet:</strong><br>
                                <?= $promotion['outlet_name'] ? esc($promotion['outlet_name']) : '<span class="text-muted">Semua Outlet</span>' ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Status:</strong><br>
                                <?php if ($promotion['is_active']): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Nonaktif</span>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products in Promotion -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Produk dalam Promosi (<?= count($promotion['items']) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($promotion['items'])): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Belum ada produk yang ditambahkan ke promosi ini.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>SKU</th>
                                            <th>Nama Produk</th>
                                            <th>Harga</th>
                                            <th width="50"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($promotion['items'] as $item): ?>
                                            <tr>
                                                <td><code><?= esc($item['sku']) ?></code></td>
                                                <td><?= esc($item['product_name']) ?></td>
                                                <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                                <td>
                                                    <form action="/admin/promotions/remove-item/<?= $promotion['id'] ?>/<?= $item['product_id'] ?>" method="POST" style="display: inline;">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus produk ini dari promosi?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>

            <!-- Available Products -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Tambah Produk</h5>
                    </div>
                    <div class="card-body">
                        <form id="addProductsForm" method="POST" action="/admin/promotions/add-items/<?= $promotion['id'] ?>">
                            <?= csrf_field() ?>
                            
                            <div class="mb-3">
                                <input type="text" class="form-control" id="searchProduct" placeholder="Cari produk...">
                            </div>

                            <div class="product-grid">
                                <?php 
                                // Filter out products already in promotion
                                $existingProductIds = array_column($promotion['items'], 'product_id');
                                $availableProducts = array_filter($products, function($product) use ($existingProductIds) {
                                    return !in_array($product['id'], $existingProductIds);
                                });
                                ?>
                                
                                <?php if (empty($availableProducts)): ?>
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle"></i> Semua produk sudah ditambahkan ke promosi.
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($availableProducts as $product): ?>
                                        <div class="product-item border rounded p-2 mb-2" data-product-id="<?= $product['id'] ?>" 
                                             data-product-name="<?= strtolower(esc($product['name'])) ?>"
                                             data-product-sku="<?= strtolower(esc($product['sku'])) ?>">
                                            <div class="form-check">
                                                <input class="form-check-input product-checkbox" type="checkbox" name="product_ids[]" 
                                                       value="<?= $product['id'] ?>" id="product_<?= $product['id'] ?>">
                                                <label class="form-check-label w-100" for="product_<?= $product['id'] ?>">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <strong><?= esc($product['name']) ?></strong><br>
                                                            <small class="text-muted">
                                                                <code><?= esc($product['sku']) ?></code> | 
                                                                <?= esc($product['category_name']) ?>
                                                            </small>
                                                        </div>
                                                        <div class="text-end">
                                                            <span class="badge bg-success">Rp <?= number_format($product['price'], 0, ',', '.') ?></span>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </div>

                            <?php if (!empty($availableProducts)): ?>
                                <div class="mt-3 d-flex justify-content-between">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllBtn">
                                        <i class="bi bi-check-all"></i> Pilih Semua
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="addSelectedBtn" disabled>
                                        <i class="bi bi-plus-circle"></i> Tambah Produk Terpilih
                                    </button>
                                </div>
                            <?php endif ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="/admin/promotions" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Promosi
            </a>
        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Search products
    $('#searchProduct').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        $('.product-item').each(function() {
            const productName = $(this).data('product-name');
            const productSku = $(this).data('product-sku');
            
            if (productName.includes(searchTerm) || productSku.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Toggle product selection on click
    $('.product-item').on('click', function(e) {
        if (e.target.type !== 'checkbox') {
            const checkbox = $(this).find('.product-checkbox');
            checkbox.prop('checked', !checkbox.prop('checked'));
            checkbox.trigger('change');
        }
    });

    // Update UI on checkbox change
    $('.product-checkbox').on('change', function() {
        const item = $(this).closest('.product-item');
        if ($(this).is(':checked')) {
            item.addClass('selected');
        } else {
            item.removeClass('selected');
        }
        updateAddButton();
    });

    // Select all button
    $('#selectAllBtn').on('click', function() {
        const visibleCheckboxes = $('.product-item:visible .product-checkbox');
        const allChecked = visibleCheckboxes.filter(':checked').length === visibleCheckboxes.length;
        
        visibleCheckboxes.prop('checked', !allChecked).trigger('change');
        $(this).html(allChecked 
            ? '<i class="bi bi-check-all"></i> Pilih Semua' 
            : '<i class="bi bi-x-circle"></i> Batal Pilih'
        );
    });

    // Enable/disable add button based on selection
    function updateAddButton() {
        const checkedCount = $('.product-checkbox:checked').length;
        $('#addSelectedBtn').prop('disabled', checkedCount === 0);
        
        if (checkedCount > 0) {
            $('#addSelectedBtn').html('<i class="bi bi-plus-circle"></i> Tambah ' + checkedCount + ' Produk');
        } else {
            $('#addSelectedBtn').html('<i class="bi bi-plus-circle"></i> Tambah Produk Terpilih');
        }
    }

    // Form submission with AJAX
    $('#addProductsForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const url = $(this).attr('action');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
