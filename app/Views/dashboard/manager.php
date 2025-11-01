<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <?php if (isset($outletInactive) && $outletInactive): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <strong>Perhatian!</strong> Outlet Anda sedang dalam status <strong>NONAKTIF</strong>. 
        Anda tidak dapat melakukan transaksi POS atau mengubah data. 
        Silakan hubungi administrator untuk mengaktifkan kembali outlet.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="bi bi-speedometer2"></i> Manager Dashboard</h3>
                <p class="text-subtitle text-muted">
                    <?php if ($outlet): ?>
                        <i class="bi bi-shop"></i> <strong><?= esc($outlet['name']) ?></strong> (<?= esc($outlet['code']) ?>)
                    <?php else: ?>
                        Selamat datang, <?= esc($user->username) ?>!
                    <?php endif ?>
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <!-- Statistics Cards -->
        <div class="row">
            <!-- Today's Sales -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted font-semibold">Penjualan Hari Ini</h6>
                                <h3 class="font-extrabold mb-0">Rp 0</h3>
                                <p class="text-sm text-muted mb-0">0 transaksi</p>
                            </div>
                            <div class="avatar avatar-xl bg-success">
                                <i class="bi bi-cash-stack text-white" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted font-semibold">Total Produk</h6>
                                <h3 class="font-extrabold mb-0">-</h3>
                                <p class="text-sm text-muted mb-0">di outlet Anda</p>
                            </div>
                            <div class="avatar avatar-xl bg-info">
                                <i class="bi bi-box-seam text-white" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Items -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted font-semibold">Stok Menipis</h6>
                                <h3 class="font-extrabold mb-0">-</h3>
                                <p class="text-sm text-muted mb-0">produk &lt; 10</p>
                            </div>
                            <div class="avatar avatar-xl bg-warning">
                                <i class="bi bi-exclamation-triangle text-white" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Info -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-lightning-charge"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/manager/products" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-seam"></i> Kelola Stok Produk
                            </a>
                            <a href="/pos" class="btn btn-success btn-lg">
                                <i class="bi bi-cart"></i> Buka POS
                            </a>
                            <a href="/manager/reports" class="btn btn-info btn-lg" disabled>
                                <i class="bi bi-bar-chart"></i> Lihat Laporan (Coming Soon)
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-info-circle"></i> Informasi Akun</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="40%">Username:</td>
                                <td><strong><?= esc($user->username) ?></strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Role:</td>
                                <td><span class="badge bg-primary">Manager</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Outlet:</td>
                                <td>
                                    <?php if ($outlet): ?>
                                        <?= esc($outlet['name']) ?> 
                                        <?php if ($outlet['is_active']): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Access Level:</td>
                                <td>
                                    <small class="text-muted">
                                        Kelola stok, laporan outlet, dan akses POS
                                    </small>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>

