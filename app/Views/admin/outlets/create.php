<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tambah Outlet</h3>
                <p class="text-subtitle text-muted">Tambah outlet/toko baru</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="/admin/outlets">Outlets</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Form Tambah Outlet</h5>
            </div>
            <div class="card-body">
                <form action="/admin/outlets/store" method="POST">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="code" class="form-label">Kode Outlet <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= session('errors.code') ? 'is-invalid' : '' ?>" 
                                       id="code" name="code" value="<?= old('code') ?>" placeholder="OUT001" required>
                                <small class="text-muted">Format: OUT001, OUT002, dst (akan otomatis uppercase)</small>
                                <?php if (session('errors.code')): ?>
                                    <div class="invalid-feedback"><?= session('errors.code') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Nama Outlet <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>" 
                                       id="name" name="name" value="<?= old('name') ?>" placeholder="Outlet Jakarta Pusat" required>
                                <?php if (session('errors.name')): ?>
                                    <div class="invalid-feedback"><?= session('errors.name') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea class="form-control <?= session('errors.address') ? 'is-invalid' : '' ?>" 
                                  id="address" name="address" rows="3" placeholder="Jl. Sudirman No. 123"><?= old('address') ?></textarea>
                        <?php if (session('errors.address')): ?>
                            <div class="invalid-feedback"><?= session('errors.address') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="phone" class="form-label">Telepon</label>
                                <input type="text" class="form-control <?= session('errors.phone') ? 'is-invalid' : '' ?>" 
                                       id="phone" name="phone" value="<?= old('phone') ?>" placeholder="021-12345678">
                                <?php if (session('errors.phone')): ?>
                                    <div class="invalid-feedback"><?= session('errors.phone') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="is_active" class="form-label">Status</label>
                                <select class="form-select" id="is_active" name="is_active">
                                    <option value="1" selected>Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="/admin/outlets" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Outlet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
