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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
        const form = document.getElementById('deleteForm');
        form.action = '/admin/users/delete/' + id;
        form.submit();
    }
}

function toggleStatus(id) {
    const form = document.getElementById('toggleForm');
    form.action = '/admin/users/toggle-status/' + id;
    form.submit();
}

$(document).ready(function() {
    $('#usersTable').DataTable({
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
});
</script>
<?= $this->endSection() ?>
