<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Kelola Outlet</h3>
                <p class="text-subtitle text-muted">Manajemen outlet/toko</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Outlets</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Daftar Outlet</h5>
                    <a href="/admin/outlets/create" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Tambah Outlet
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Outlet</th>
                                <th>Alamat</th>
                                <th>Telepon</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($outlets)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada data outlet</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($outlets as $index => $outlet): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><code><?= esc($outlet->code) ?></code></td>
                                        <td><strong><?= esc($outlet->name) ?></strong></td>
                                        <td><?= esc($outlet->address) ?: '-' ?></td>
                                        <td><?= esc($outlet->phone) ?: '-' ?></td>
                                        <td>
                                            <?php if ($outlet->is_active): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="/admin/outlets/view/<?= $outlet->id ?>" class="btn btn-sm btn-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="/admin/outlets/edit/<?= $outlet->id ?>" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $outlet->id ?>)" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
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
<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus outlet ini?')) {
        const form = document.getElementById('deleteForm');
        form.action = '/admin/outlets/delete/' + id;
        form.submit();
    }
}
</script>
<?= $this->endSection() ?>
