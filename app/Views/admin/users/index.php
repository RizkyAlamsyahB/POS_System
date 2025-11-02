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
                <h3>Manajemen User</h3>
                <p class="text-subtitle text-muted">Kelola user dan assign outlet/role</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Users</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Daftar User</h5>
                    <a href="/admin/users/create" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Tambah User
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="usersTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>User Info</th>
                                <th>Role</th>
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

<!-- Toggle Status Form (hidden) -->
<form id="toggleForm" method="POST" style="display: none;">
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
                    User <strong id="deleteUsername" class="text-dark"></strong> akan dihapus secara permanen.
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
// Store delete user ID temporarily
let deleteUserId = null;

function confirmDelete(id) {
    // Get username from table row
    const row = event.target.closest('tr');
    const usernameCell = row.cells[1].textContent.trim().split('\n')[0];
    
    // Set username in modal
    document.getElementById('deleteUsername').textContent = usernameCell;
    
    // Store ID for later use
    deleteUserId = id;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Handle confirm delete button
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (deleteUserId) {
            const form = document.getElementById('deleteForm');
            form.action = '/admin/users/delete/' + deleteUserId;
            
            // Hide modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
            modal.hide();
            
            // Submit form
            form.submit();
        }
    });
});

function toggleStatus(id) {
    const form = document.getElementById('toggleForm');
    form.action = '/admin/users/toggle-status/' + id;
    form.submit();
}

$(document).ready(function() {
    const table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/admin/users/datatable',
            type: 'GET'
        },
        columns: [
            { data: 0, orderable: false },                    // No
            { data: 1 },                                       // User Info (username + email)
            { data: 2 },                                       // Role
            { data: 3 },                                       // Outlet
            { data: 4 },                                       // Status
            { data: 5, orderable: false, searchable: false }, // Aksi
        ],
        order: [[1, 'asc']]  // Default sort by username
    });
    
    // Auto reload DataTable after CRUD operations
    <?php if (session()->has('message') || session()->has('error')): ?>
        // Reload table after successful operation
        setTimeout(function() {
            table.ajax.reload(null, false); // false = stay on current page
        }, 100);
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>