<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Daftar Produk</h3>
                <p class="text-subtitle text-muted">View produk (read-only)</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/manager/dashboard">Dashboard</a></li>
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
                    <a href="/manager/products/stock" class="btn btn-primary">
                        <i class="bi bi-box-seam"></i> Kelola Stok Outlet
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>SKU</th>
                                <th>Barcode</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga Jual</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada data produk</td>
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
                                        <td>
                                            <?php if ($product['is_active']): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="/manager/products/view/<?= $product['id'] ?>" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
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
<?= $this->endSection() ?>
