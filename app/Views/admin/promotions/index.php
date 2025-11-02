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
                <h3>Kelola Promosi</h3>
                <p class="text-subtitle text-muted">Manajemen promosi dan diskon produk</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Promosi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Daftar Promosi</h5>
                    <a href="/admin/promotions/create" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Tambah Promosi
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="promotionsTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Promosi</th>
                                <th>Diskon</th>
                                <th>Periode</th>
                                <th>Outlet</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Delete Form (hidden) -->
<form id="deleteForm" method="POST" style="display: none;">
    <?= csrf_field() ?>
</form>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="bi bi-trash text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5>Apakah Anda yakin?</h5>
                <p class="text-muted mb-3">
                    Promosi <strong id="deletePromotionName" class="text-dark"></strong> beserta semua item promosi akan dihapus secara permanen.
                </p>
                <div class="alert alert-warning text-start small">
                    <i class="bi bi-info-circle"></i> 
                    <strong>Perhatian:</strong> Data yang sudah dihapus tidak dapat dikembalikan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    Hapus
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
// Store delete promotion ID temporarily
let deletePromotionId = null;

function confirmDelete(id) {
    const row = event.target.closest('tr');
    const promotionName = row.cells[2].textContent.trim(); // Nama promosi di column ke-2
    
    // Set promotion name in modal
    document.getElementById('deletePromotionName').textContent = promotionName;
    
    // Store ID for later use
    deletePromotionId = id;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Handle confirm delete button
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (deletePromotionId) {
            const form = document.getElementById('deleteForm');
            form.action = '/admin/promotions/delete/' + deletePromotionId;
            
            // Hide modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
            modal.hide();
            
            // Submit form
            form.submit();
        }
    });
});

$(document).ready(function() {
    const table = $('#promotionsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/admin/promotions/datatable',
            type: 'GET'
        },
        columns: [
            { data: 0, orderable: false },                    // No
            { data: 1 },                                       // Kode
            { data: 2 },                                       // Nama Promosi
            { data: 3 },                                       // Diskon
            { data: 4 },                                       // Periode
            { data: 5 },                                       // Outlet
            { data: 6 },                                       // Status
            { data: 7, orderable: false, searchable: false }, // Aksi
        ],
        order: [[4, 'desc']]  // Default sort by periode (newest first)
    });
    
    // Auto reload DataTable after CRUD operations
    <?php if (session()->has('message') || session()->has('error')): ?>
        setTimeout(function() {
            table.ajax.reload(null, false);
        }, 100);
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
