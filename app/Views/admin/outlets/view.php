<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Outlet</h3>
                <p class="text-subtitle text-muted"><?= esc($outlet->name) ?></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="/admin/outlets">Outlets</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
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
                            <tr>
                                <th>Dibuat</th>
                                <td><?= date('d M Y H:i', strtotime($outlet->created_at)) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <a href="/admin/outlets/edit/<?= $outlet->id ?>" class="btn btn-warning">
                        Edit
                    </a>
                    <a href="/admin/outlets" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Stock Summary -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Ringkasan Stok</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <h6 class="text-muted font-semibold">Total Produk</h6>
                                <h6 class="font-extrabold mb-0"><?= $stockSummary['total_products'] ?? 0 ?></h6>
                            </div>
                            <div class="col-6">
                                <h6 class="text-muted font-semibold">Total Stok</h6>
                                <h6 class="font-extrabold mb-0"><?= number_format($stockSummary['total_stock'] ?? 0) ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Pengguna</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="text-muted font-semibold">Total Pengguna</h6>
                        <h6 class="font-extrabold mb-0"><?= count($users) ?> orang</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users List -->
        <?php if (!empty($users)): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">Daftar Pengguna di Outlet Ini</h5>
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
