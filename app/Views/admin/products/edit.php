<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Produk</h3>
                <p class="text-subtitle text-muted">Edit informasi produk</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="/admin/products">Produk</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Form Edit Produk</h5>
            </div>
            <div class="card-body">
                <form action="/admin/products/update/<?= $product['id'] ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="row">
                        <!-- Informasi Dasar -->
                        <div class="col-md-6">
                            <h6 class="mb-3">Informasi Dasar</h6>
                            
                            <div class="form-group mb-3">
                                <label for="category_id">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select <?= session('errors.category_id') ? 'is-invalid' : '' ?>" 
                                        id="category_id" name="category_id" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach ($categories as $category): ?>
                                        <?php 
                                            $catId = is_array($category) ? $category['id'] : $category->id;
                                            $catName = is_array($category) ? $category['name'] : $category->name;
                                        ?>
                                        <option value="<?= $catId ?>" <?= old('category_id', $product['category_id']) == $catId ? 'selected' : '' ?>>
                                            <?= esc($catName) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.category_id')): ?>
                                    <div class="invalid-feedback"><?= session('errors.category_id') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group mb-3">
                                <label for="sku">SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= session('errors.sku') ? 'is-invalid' : '' ?>" 
                                       id="sku" name="sku" value="<?= old('sku', $product['sku']) ?>" required>
                                <small class="text-muted">Stock Keeping Unit (akan otomatis uppercase)</small>
                                <?php if (session('errors.sku')): ?>
                                    <div class="invalid-feedback"><?= session('errors.sku') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group mb-3">
                                <label for="barcode">Barcode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= session('errors.barcode') ? 'is-invalid' : '' ?>" 
                                       id="barcode" name="barcode" value="<?= old('barcode', $product['barcode']) ?>" required>
                                <?php if (session('errors.barcode')): ?>
                                    <div class="invalid-feedback"><?= session('errors.barcode') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group mb-3">
                                <label for="barcode_alt">Barcode Alternatif</label>
                                <input type="text" class="form-control" id="barcode_alt" name="barcode_alt" value="<?= old('barcode_alt', $product['barcode_alt']) ?>">
                            </div>

                            <div class="form-group mb-3">
                                <label for="name">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>" 
                                       id="name" name="name" value="<?= old('name', $product['name']) ?>" required>
                                <?php if (session('errors.name')): ?>
                                    <div class="invalid-feedback"><?= session('errors.name') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group mb-3">
                                <label for="unit">Satuan <span class="text-danger">*</span></label>
                                <select class="form-select" id="unit" name="unit" required>
                                    <option value="PCS" <?= old('unit', $product['unit']) == 'PCS' ? 'selected' : '' ?>>PCS</option>
                                    <option value="BOX" <?= old('unit', $product['unit']) == 'BOX' ? 'selected' : '' ?>>BOX</option>
                                    <option value="KG" <?= old('unit', $product['unit']) == 'KG' ? 'selected' : '' ?>>KG</option>
                                    <option value="LUSIN" <?= old('unit', $product['unit']) == 'LUSIN' ? 'selected' : '' ?>>LUSIN</option>
                                </select>
                            </div>
                        </div>

                        <!-- Harga & Pajak -->
                        <div class="col-md-6">
                            <h6 class="mb-3">Harga & Pajak</h6>
                            
                            <div class="form-group mb-3">
                                <label for="cost_price">Harga Pokok (HPP) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control <?= session('errors.cost_price') ? 'is-invalid' : '' ?>" 
                                       id="cost_price" name="cost_price" value="<?= old('cost_price', $product['cost_price']) ?>" required>
                                <?php if (session('errors.cost_price')): ?>
                                    <div class="invalid-feedback"><?= session('errors.cost_price') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group mb-3">
                                <label for="price">Harga Jual <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control <?= session('errors.price') ? 'is-invalid' : '' ?>" 
                                       id="price" name="price" value="<?= old('price', $product['price']) ?>" required>
                                <?php if (session('errors.price')): ?>
                                    <div class="invalid-feedback"><?= session('errors.price') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group mb-3">
                                <label for="tax_type">Jenis Pajak</label>
                                <select class="form-select" id="tax_type" name="tax_type">
                                    <option value="NONE" <?= old('tax_type', $product['tax_type']) == 'NONE' ? 'selected' : '' ?>>Tidak ada pajak</option>
                                    <option value="PPN" <?= old('tax_type', $product['tax_type']) == 'PPN' ? 'selected' : '' ?>>PPN</option>
                                    <option value="PB1" <?= old('tax_type', $product['tax_type']) == 'PB1' ? 'selected' : '' ?>>PB1</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="tax_rate">Persentase Pajak (%)</label>
                                <input type="number" step="0.01" class="form-control" id="tax_rate" name="tax_rate" value="<?= old('tax_rate', $product['tax_rate']) ?>">
                                <small class="text-muted">Contoh: 11.00 untuk PPN 11%</small>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="tax_included" name="tax_included" value="1" <?= old('tax_included', $product['tax_included']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="tax_included">
                                    Harga sudah termasuk pajak
                                </label>
                            </div>

                            <div class="form-group mb-3">
                                <label for="image">Gambar Produk</label>
                                <?php if ($product['image']): ?>
                                    <div class="mb-2">
                                        <img src="/<?= esc($product['image']) ?>" alt="Current Image" class="img-thumbnail" style="max-width: 200px;">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="delete_image" name="delete_image" value="1">
                                            <label class="form-check-label" for="delete_image">
                                                Hapus gambar saat ini
                                            </label>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG, max 2MB. Kosongkan jika tidak ingin mengubah gambar.</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="/admin/products" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>