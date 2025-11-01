<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Master Produk</h3>
                <p class="text-subtitle text-muted">Kelola data produk</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Produk</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Daftar Produk</h5>
                    <a href="/admin/products/create" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Tambah Produk
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="productsTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>SKU</th>
                                <th>Barcode</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga Jual</th>
                                <th>HPP</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="9" class="text-center">Belum ada data produk</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $index => $product): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><code><?= esc($product['sku']) ?></code></td>
                                        <td><code><?= esc($product['barcode']) ?></code></td>
                                        <td><strong><?= esc($product['name']) ?></strong></td>
                                        <td><?= esc($product['category_name']) ?></td>
                                        <td>Rp <?= number_format($product['price'], 0, ',', '.') ?></td>
                                        <td>Rp <?= number_format($product['cost_price'], 0, ',', '.') ?></td>
                                        <td>
                                            <?php if ($product['is_active']): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="/admin/products/stock/<?= $product['id'] ?>" class="btn btn-sm btn-info" title="Kelola Stok">
                                                    <i class="bi bi-box-seam"></i>
                                                </a>
                                                <a href="/admin/products/edit/<?= $product['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $product['id'] ?>)" title="Hapus">
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
    if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
        const form = document.getElementById('deleteForm');
        form.action = '/admin/products/delete/' + id;
        form.submit();
    }
}
</script>
<?= $this->endSection() ?>
