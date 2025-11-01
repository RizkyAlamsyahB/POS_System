<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Kelola Stok Produk</h3>
                <p class="text-subtitle text-muted"><?= esc($product['name']) ?> (<?= esc($product['sku']) ?>)</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="/admin/products">Produk</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Stok</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <!-- Product Info Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Informasi Produk</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <?php if ($product['image']): ?>
                            <img src="/<?= esc($product['image']) ?>" alt="Product Image" class="img-thumbnail">
                        <?php else: ?>
                            <div class="bg-light p-5 text-center rounded">
                                <i class="bi bi-image" style="font-size: 3rem;"></i>
                                <p class="mb-0 mt-2">No Image</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-9">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">SKU</th>
                                <td><code><?= esc($product['sku']) ?></code></td>
                                <th width="150">Barcode</th>
                                <td><code><?= esc($product['barcode']) ?></code></td>
                            </tr>
                            <tr>
                                <th>Nama Produk</th>
                                <td colspan="3"><strong><?= esc($product['name']) ?></strong></td>
                            </tr>
                            <tr>
                                <th>Harga Jual</th>
                                <td>Rp <?= number_format($product['price'], 0, ',', '.') ?></td>
                                <th>Satuan</th>
                                <td><?= esc($product['unit']) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Management -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Stok per Outlet</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStockModal">
                        <i class="bi bi-plus-circle"></i> Update Stok
                    </button>
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
                                <th>Kode Outlet</th>
                                <th>Nama Outlet</th>
                                <th>Stok Tersedia</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($stocks)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada data stok. Klik "Update Stok" untuk menambahkan.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($stocks as $index => $stock): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><code><?= esc($stock['outlet_code']) ?></code></td>
                                        <td><?= esc($stock['outlet_name']) ?></td>
                                        <td>
                                            <strong><?= number_format($stock['stock'], 0, ',', '.') ?></strong> <?= esc($product['unit']) ?>
                                        </td>
                                        <td>
                                            <?php if ($stock['stock'] <= 0): ?>
                                                <span class="badge bg-danger">Habis</span>
                                            <?php elseif ($stock['stock'] <= 10): ?>
                                                <span class="badge bg-warning">Menipis</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Tersedia</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    onclick="editStock(<?= $stock['outlet_id'] ?>, '<?= esc($stock['outlet_name']) ?>', <?= $stock['stock'] ?>)">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-3">
            <a href="/admin/products" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Produk
            </a>
        </div>
    </section>
</div>

<!-- Add/Edit Stock Modal -->
<div class="modal fade" id="addStockModal" tabindex="-1" aria-labelledby="addStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/products/stock/update/<?= $product['id'] ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="addStockModalLabel">Update Stok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="outlet_id">Outlet <span class="text-danger">*</span></label>
                        <select class="form-select" id="outlet_id" name="outlet_id" required>
                            <option value="">-- Pilih Outlet --</option>
                            <?php foreach ($outlets as $outlet): ?>
                                <option value="<?= is_array($outlet) ? $outlet['id'] : $outlet->id ?>">
                                    <?= esc(is_array($outlet) ? $outlet['code'] : $outlet->code) ?> - <?= esc(is_array($outlet) ? $outlet['name'] : $outlet->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="stock">Jumlah Stok <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="stock" name="stock" min="0" required>
                        <small class="text-muted">Masukkan jumlah stok yang tersedia</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function editStock(outletId, outletName, currentStock) {
    document.getElementById('outlet_id').value = outletId;
    document.getElementById('stock').value = currentStock;
    document.getElementById('addStockModalLabel').textContent = 'Edit Stok - ' + outletName;
    
    const modal = new bootstrap.Modal(document.getElementById('addStockModal'));
    modal.show();
}
</script>
<?= $this->endSection() ?>