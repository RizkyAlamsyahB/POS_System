<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit User</h3>
                <p class="text-subtitle text-muted">Edit informasi user</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="/admin/users">Users</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Form Edit User</h5>
            </div>
            <div class="card-body">
                <?php if (session('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="/admin/users/update/<?= $user->id ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= session('errors.username') ? 'is-invalid' : '' ?>" 
                                       id="username" name="username" value="<?= old('username', $user->username) ?>" required>
                                <?php if (session('errors.username')): ?>
                                    <div class="invalid-feedback"><?= session('errors.username') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                                       id="email" name="email" value="<?= old('email', $user->secret) ?>">
                                <?php if (session('errors.email')): ?>
                                    <div class="invalid-feedback"><?= session('errors.email') ?></div>
                                <?php endif; ?>
                                <small class="text-muted">Opsional - untuk reset password</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <input type="password" class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>" 
                                       id="password" name="password">
                                <?php if (session('errors.password')): ?>
                                    <div class="invalid-feedback"><?= session('errors.password') ?></div>
                                <?php endif; ?>
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password_confirm" class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control <?= session('errors.password_confirm') ? 'is-invalid' : '' ?>" 
                                       id="password_confirm" name="password_confirm">
                                <?php if (session('errors.password_confirm')): ?>
                                    <div class="invalid-feedback"><?= session('errors.password_confirm') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select <?= session('errors.role') ? 'is-invalid' : '' ?>" 
                                        id="role" name="role" required onchange="toggleOutletField()" <?= $currentRole === 'admin' ? 'disabled' : '' ?>>
                                    <option value="">-- Pilih Role --</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role ?>" <?= old('role', $currentRole) == $role ? 'selected' : '' ?>>
                                            <?= ucfirst($role) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($currentRole === 'admin'): ?>
                                    <input type="hidden" name="role" value="admin">
                                    <small class="text-muted">Role Admin tidak dapat diubah</small>
                                <?php else: ?>
                                    <small class="text-muted">Role hanya bisa Manager atau Cashier</small>
                                <?php endif; ?>
                                <?php if (session('errors.role')): ?>
                                    <div class="invalid-feedback"><?= session('errors.role') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3" id="outletField">
                                <label for="outlet_id" class="form-label">
                                    Outlet <span class="text-danger outlet-required" style="display:none;">*</span>
                                </label>
                                <select class="form-select <?= session('errors.outlet_id') ? 'is-invalid' : '' ?>" 
                                        id="outlet_id" name="outlet_id">
                                    <option value="">-- Pilih Outlet --</option>
                                    <?php foreach ($outlets as $outlet): ?>
                                        <option value="<?= $outlet->id ?>" <?= old('outlet_id', $user->outlet_id) == $outlet->id ? 'selected' : '' ?>>
                                            <?= esc($outlet->code) ?> - <?= esc($outlet->name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.outlet_id')): ?>
                                    <div class="invalid-feedback"><?= session('errors.outlet_id') ?></div>
                                <?php endif; ?>
                                <small class="text-muted outlet-hint">Admin memiliki akses ke semua outlet</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="/admin/users" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function toggleOutletField() {
    const role = document.getElementById('role').value;
    const outletField = document.getElementById('outlet_id');
    const outletRequired = document.querySelector('.outlet-required');
    const outletHint = document.querySelector('.outlet-hint');
    
    if (role === 'admin') {
        outletField.disabled = true;
        outletField.value = '';
        outletRequired.style.display = 'none';
        outletHint.textContent = 'Admin memiliki akses ke semua outlet';
    } else if (role === 'manager' || role === 'cashier') {
        outletField.disabled = false;
        outletRequired.style.display = 'inline';
        outletHint.textContent = role === 'manager' ? 'Manager harus di-assign ke satu outlet' : 'Cashier harus di-assign ke satu outlet';
    } else {
        outletField.disabled = false;
        outletRequired.style.display = 'none';
        outletHint.textContent = '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleOutletField();
});
</script>
<?= $this->endSection() ?>
