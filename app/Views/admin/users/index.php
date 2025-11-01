<?= $this->extend('layouts/app') ?>

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
                <?php if (session('message')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session('message') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Outlet</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada data user</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $index => $user): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <strong><?= esc($user->username) ?></strong>
                                            <?php if ($user->id == auth()->id()): ?>
                                                <span class="badge bg-info">You</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($user->secret) ?: '-' ?></td>
                                        <td>
                                            <?php
                                            $role = $user->groups[0] ?? 'No Role';
                                            $badgeClass = match($role) {
                                                'admin' => 'bg-danger',
                                                'manager' => 'bg-warning',
                                                'cashier' => 'bg-info',
                                                default => 'bg-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= ucfirst($role) ?></span>
                                        </td>
                                        <td>
                                            <?php if (isset($user->outlet)): ?>
                                                <code><?= esc($user->outlet->code) ?></code> - <?= esc($user->outlet->name) ?>
                                            <?php else: ?>
                                                <span class="text-muted">All Outlets</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($user->active): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="/admin/users/edit/<?= $user->id ?>" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <?php if ($user->id != auth()->id()): ?>
                                                    <button type="button" class="btn btn-sm btn-<?= $user->active ? 'secondary' : 'success' ?>" 
                                                            onclick="toggleStatus(<?= $user->id ?>)" 
                                                            title="<?= $user->active ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                                        <i class="bi bi-<?= $user->active ? 'pause' : 'play' ?>-circle"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $user->id ?>)" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                <?php endif; ?>
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

<!-- Toggle Status Form (hidden) -->
<form id="toggleForm" method="POST" style="display: none;">
    <?= csrf_field() ?>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
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
</script>
<?= $this->endSection() ?>
