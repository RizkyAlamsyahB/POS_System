<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tambah Promosi</h3>
                <p class="text-subtitle text-muted">Buat promosi baru</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="/admin/promotions">Promosi</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Form Tambah Promosi</h5>
            </div>
            <div class="card-body">
                <form action="/admin/promotions/store" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label">Kode Promosi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($errors['code']) ? 'is-invalid' : '' ?>" 
                                       id="code" name="code" value="<?= old('code') ?>" required>
                                <?php if (isset($errors['code'])): ?>
                                    <div class="invalid-feedback"><?= $errors['code'] ?></div>
                                <?php endif ?>
                                <small class="text-muted">Contoh: PROMO2025, DISCOUNT50</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Promosi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                       id="name" name="name" value="<?= old('name') ?>" required>
                                <?php if (isset($errors['name'])): ?>
                                    <div class="invalid-feedback"><?= $errors['name'] ?></div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= old('description') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="discount_type" class="form-label">Tipe Diskon <span class="text-danger">*</span></label>
                                <select class="form-select <?= isset($errors['discount_type']) ? 'is-invalid' : '' ?>" 
                                        id="discount_type" name="discount_type" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="percentage" <?= old('discount_type') == 'percentage' ? 'selected' : '' ?>>Persentase (%)</option>
                                    <option value="fixed_amount" <?= old('discount_type') == 'fixed_amount' ? 'selected' : '' ?>>Nominal (Rp)</option>
                                </select>
                                <?php if (isset($errors['discount_type'])): ?>
                                    <div class="invalid-feedback"><?= $errors['discount_type'] ?></div>
                                <?php endif ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="discount_value" class="form-label">Nilai Diskon <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control <?= isset($errors['discount_value']) ? 'is-invalid' : '' ?>" 
                                       id="discount_value" name="discount_value" value="<?= old('discount_value') ?>" required>
                                <?php if (isset($errors['discount_value'])): ?>
                                    <div class="invalid-feedback"><?= $errors['discount_value'] ?></div>
                                <?php endif ?>
                                <small class="text-muted" id="discount_hint">Masukkan nilai diskon</small>
                            </div>
                        </div>

                        <div class="col-md-4" id="max_discount_field" style="display: none;">
                            <div class="mb-3">
                                <label for="max_discount" class="form-label">Maksimal Diskon (Rp)</label>
                                <input type="number" step="0.01" class="form-control" 
                                       id="max_discount" name="max_discount" value="<?= old('max_discount') ?>">
                                <small class="text-muted">Untuk diskon persentase</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="min_purchase" class="form-label">Minimal Pembelian (Rp)</label>
                                <input type="number" step="0.01" class="form-control" 
                                       id="min_purchase" name="min_purchase" value="<?= old('min_purchase') ?>">
                                <small class="text-muted">Opsional - kosongkan jika tidak ada minimal</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="outlet_id" class="form-label">Berlaku di Outlet</label>
                                <select class="form-select" id="outlet_id" name="outlet_id">
                                    <option value="">Semua Outlet</option>
                                    <?php foreach ($outlets as $outlet): ?>
                                        <option value="<?= $outlet['id'] ?>" <?= old('outlet_id') == $outlet['id'] ? 'selected' : '' ?>>
                                            <?= esc($outlet['code']) ?> - <?= esc($outlet['name']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control <?= isset($errors['start_date']) ? 'is-invalid' : '' ?>" 
                                       id="start_date" name="start_date" value="<?= old('start_date') ?>" required>
                                <?php if (isset($errors['start_date'])): ?>
                                    <div class="invalid-feedback"><?= $errors['start_date'] ?></div>
                                <?php endif ?>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control <?= isset($errors['end_date']) ? 'is-invalid' : '' ?>" 
                                       id="end_date" name="end_date" value="<?= old('end_date') ?>" required>
                                <?php if (isset($errors['end_date'])): ?>
                                    <div class="invalid-feedback"><?= $errors['end_date'] ?></div>
                                <?php endif ?>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Jam Mulai</label>
                                <input type="time" class="form-control" id="start_time" name="start_time" value="<?= old('start_time') ?>">
                                <small class="text-muted">Opsional</small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">Jam Selesai</label>
                                <input type="time" class="form-control" id="end_time" name="end_time" value="<?= old('end_time') ?>">
                                <small class="text-muted">Opsional</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <!-- Hidden field untuk memastikan value 0 terkirim jika unchecked -->
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">
                                Aktifkan promosi
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="/admin/promotions" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan
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
// Toggle max_discount field based on discount_type
document.getElementById('discount_type').addEventListener('change', function() {
    const maxDiscountField = document.getElementById('max_discount_field');
    const discountHint = document.getElementById('discount_hint');
    
    if (this.value === 'percentage') {
        maxDiscountField.style.display = 'block';
        discountHint.textContent = 'Masukkan nilai persentase (0-100)';
    } else if (this.value === 'fixed_amount') {
        maxDiscountField.style.display = 'none';
        discountHint.textContent = 'Masukkan nilai nominal dalam Rupiah';
    } else {
        maxDiscountField.style.display = 'none';
        discountHint.textContent = 'Masukkan nilai diskon';
    }
});

// Trigger on page load if value exists
if (document.getElementById('discount_type').value) {
    document.getElementById('discount_type').dispatchEvent(new Event('change'));
}

// Auto uppercase code
document.getElementById('code').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>
<?= $this->endSection() ?>
