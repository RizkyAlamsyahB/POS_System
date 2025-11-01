<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Outlet Saya</h3>
                <p class="text-subtitle text-muted"><?= esc($outlet->name) ?></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/manager/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Outlet</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <!-- Outlet Information -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Informasi Outlet</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Kode Outlet</th>
                                <td><code><?= esc($outlet->code) ?></code></td>
                            </tr>
                            <tr>
                                <th>Nama Outlet</th>
                                <td><strong><?= esc($outlet->name) ?></strong></td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td><?= esc($outlet->address) ?: '-' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Telepon</th>
                                <td><?= esc($outlet->phone) ?: '-' ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <?php if ($outlet->is_active): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Summary -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon blue mb-2">
                                    <i class="iconly-boldShow"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Total Produk</h6>
                                <h6 class="font-extrabold mb-0"><?= $stockSummary['total_products'] ?? 0 ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon green mb-2">
                                    <i class="iconly-boldBag-2"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Total Stok</h6>
                                <h6 class="font-extrabold mb-0"><?= number_format($stockSummary['total_stock'] ?? 0) ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon red mb-2">
                                    <i class="iconly-boldDanger"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Stok Menipis</h6>
                                <h6 class="font-extrabold mb-0"><?= count($lowStockProducts) ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <?php if (!empty($lowStockProducts)): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">⚠️ Produk Stok Menipis (≤ 10)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Nama Produk</th>
                                <th>Stok Tersisa</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lowStockProducts as $item): ?>
                                <tr>
                                    <td><code><?= esc($item->sku) ?></code></td>
                                    <td><?= esc($item->product_name) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $item->stock <= 5 ? 'danger' : 'warning' ?>">
                                            <?= $item->stock ?> unit
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/manager/products/stock" class="btn btn-sm btn-primary">
                                            <i class="bi bi-plus-circle"></i> Update Stok
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Team Members -->
        <?php if (!empty($users)): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">Tim Outlet</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><strong><?= esc($u->username) ?></strong></td>
                                    <td><?= esc($u->secret) ?: '-' ?></td>
                                    <td>
                                        <?php if ($u->active): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </section>
</div>
<?= $this->endSection() ?>
