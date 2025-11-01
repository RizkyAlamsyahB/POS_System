<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Kelola Stok Produk</h3>
                <p class="text-subtitle text-muted">
                    <?php if ($outlet): ?>
                        <i class="bi bi-shop"></i> <strong><?= esc($outlet['name']) ?></strong> (<?= esc($outlet['code']) ?>)
                    <?php else: ?>
                        Outlet tidak ditemukan
                    <?php endif ?>
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/manager/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Kelola Stok</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Daftar Produk & Stok</h5>
                    <div class="badge bg-info">
                        <i class="bi bi-info-circle"></i> Klik "Update Stok" untuk mengubah stok produk
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="productsTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>SKU</th>
                                <th>Barcode</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
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
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
let table;
let updateStockModal;

$(document).ready(function() {
    // Initialize DataTable
    table = $('#productsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/manager/products/datatable',
            type: 'GET'
        },
        columns: [
            { data: 0, orderable: false },                    // No
            { data: 1 },                                       // SKU
            { data: 2 },                                       // Barcode
            { data: 3 },                                       // Nama Produk
            { data: 4 },                                       // Kategori
            { data: 5 },                                       // Harga
            { data: 6, orderable: true },                     // Stok
            { data: 7, orderable: false, searchable: false }, // Aksi
        ],
        order: [[3, 'asc']]  // Default sort by nama produk
    });

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
                    table.ajax.reload(null, false); // Reload table without resetting pagination
                    
                    // Show success message
                    showAlert('success', response.message);
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

// Helper function to show alert
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('.page-heading').prepend(alertHtml);
    
    // Auto dismiss after 3 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 3000);
}
</script>
<?= $this->endSection() ?>

