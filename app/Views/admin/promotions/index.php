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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus promosi ini? Semua item promosi juga akan terhapus.')) {
        const form = document.getElementById('deleteForm');
        form.action = '/admin/promotions/delete/' + id;
        form.submit();
    }
}

$(document).ready(function() {
    $('#promotionsTable').DataTable({
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
});
</script>
<?= $this->endSection() ?>
