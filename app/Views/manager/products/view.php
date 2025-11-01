<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Produk</h3>
                <p class="text-subtitle text-muted">Informasi lengkap produk</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/manager/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="/manager/products">Kelola Stok</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <!-- Product Details -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informasi Produk</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <strong>SKU:</strong>
                            </div>
                            <div class="col-md-9">
                                <code><?= esc($product['sku']) ?></code>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <strong>Barcode:</strong>
                            </div>
                            <div class="col-md-9">
                                <code><?= esc($product['barcode']) ?></code>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <strong>Nama Produk:</strong>
                            </div>
                            <div class="col-md-9">
                                <?= esc($product['name']) ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <strong>Kategori:</strong>
                            </div>
                            <div class="col-md-9">
                                <span class="badge bg-info"><?= esc($category['name']) ?></span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <strong>Deskripsi:</strong>
                            </div>
                            <div class="col-md-9">
                                <?= nl2br(esc($product['description'] ?? '-')) ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <strong>Harga Beli:</strong>
                            </div>
                            <div class="col-md-9">
                                Rp <?= number_format($product['cost_price'], 0, ',', '.') ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <strong>Harga Jual:</strong>
                            </div>
                            <div class="col-md-9">
                                <strong class="text-success">Rp <?= number_format($product['price'], 0, ',', '.') ?></strong>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <strong>Margin:</strong>
                            </div>
                            <div class="col-md-9">
                                <?php 
                                $margin = $product['cost_price'] > 0 ? (($product['price'] - $product['cost_price']) / $product['cost_price']) * 100 : 0;
                                ?>
                                <span class="badge <?= $margin > 0 ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= number_format($margin, 2) ?>%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Information -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Stok - <?= esc($outlet['name']) ?></h5>
                            <button class="btn btn-sm btn-primary" onclick="updateStock(<?= $product['id'] ?>, '<?= esc($product['name']) ?>', <?= $stock['stock'] ?? 0 ?>)">
                                <i class="bi bi-pencil-square"></i> Update
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-4">
                            <?php 
                            $stockValue = $stock['stock'] ?? 0;
                            $badgeClass = 'bg-danger';
                            $statusText = 'Habis';
                            
                            if ($stockValue > 0) {
                                if ($stockValue < 10) {
                                    $badgeClass = 'bg-warning';
                                    $statusText = 'Stok Menipis';
                                } else {
                                    $badgeClass = 'bg-success';
                                    $statusText = 'Tersedia';
                                }
                            }
                            ?>
                            <h1 class="display-3 mb-3"><?= number_format($stockValue, 0, ',', '.') ?></h1>
                            <span class="badge <?= $badgeClass ?> fs-6"><?= $statusText ?></span>
                        </div>

                        <hr>

                        <div class="mb-2">
                            <small class="text-muted">Nilai Stok (Harga Beli):</small><br>
                            <strong>Rp <?= number_format($stockValue * $product['cost_price'], 0, ',', '.') ?></strong>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted">Nilai Stok (Harga Jual):</small><br>
                            <strong class="text-success">Rp <?= number_format($stockValue * $product['price'], 0, ',', '.') ?></strong>
                        </div>

                        <?php if ($stock && isset($stock['updated_at'])): ?>
                            <hr>
                            <small class="text-muted">
                                <i class="bi bi-clock-history"></i> Terakhir update:<br>
                                <?= date('d/m/Y H:i', strtotime($stock['updated_at'])) ?>
                            </small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="/manager/products" class="btn btn-secondary w-100">
                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Produk
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Update Stock -->
<div class="modal fade" id="updateStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Stok Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="updateStockForm">
                    <input type="hidden" id="product_id" name="product_id">
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Produk:</strong></label>
                        <p id="product_name" class="text-muted"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="stock" class="form-label">Stok Baru <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-lg" id="stock" name="stock" 
                               min="0" required autofocus>
                        <small class="text-muted">Masukkan jumlah stok yang tersedia</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveStockBtn">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let updateStockModal;

$(document).ready(function() {
    // Initialize modal
    updateStockModal = new bootstrap.Modal(document.getElementById('updateStockModal'));

    // Save stock button
    $('#saveStockBtn').on('click', function() {
        const formData = {
            product_id: $('#product_id').val(),
            stock: $('#stock').val()
        };

        // Validate
        if (!formData.stock || formData.stock < 0) {
            alert('Stok harus diisi dan tidak boleh negatif!');
            return;
        }

        // Disable button
        $(this).prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Menyimpan...');

        // AJAX request
        $.ajax({
            url: '/manager/products/update-stock',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    updateStockModal.hide();
                    // Reload page to show updated stock
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            },
            complete: function() {
                $('#saveStockBtn').prop('disabled', false).html('<i class="bi bi-save"></i> Simpan');
            }
        });
    });

    // Reset form when modal is hidden
    $('#updateStockModal').on('hidden.bs.modal', function() {
        $('#updateStockForm')[0].reset();
    });
});

// Function to open update stock modal
function updateStock(productId, productName, currentStock) {
    $('#product_id').val(productId);
    $('#product_name').text(productName);
    $('#stock').val(currentStock);
    updateStockModal.show();
    
    // Focus on stock input after modal shown
    setTimeout(function() {
        $('#stock').focus().select();
    }, 500);
}
</script>
<?= $this->endSection() ?>
