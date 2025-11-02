<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Admin Dashboard</h3>
                <p class="text-subtitle text-muted">Welcome back, <?= esc($user->username) ?>!</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Admin</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <section class="row">
        <div class="col-12 col-lg-12">
            <div class="row">
                <!-- Total Outlets -->
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon purple mb-2">
                                        <i class="iconly-boldHome"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Outlets</h6>
                                    <h6 class="font-extrabold mb-0"><?= number_format($totalOutlets, 0) ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Total Users -->
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon blue mb-2">
                                        <i class="iconly-boldProfile"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Users</h6>
                                    <h6 class="font-extrabold mb-0"><?= number_format($totalUsers, 0) ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Total Products -->
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon green mb-2">
                                        <i class="iconly-boldBag-2"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Products</h6>
                                    <h6 class="font-extrabold mb-0"><?= number_format($totalProducts, 0) ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Today's Sales -->
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon red mb-2">
                                        <i class="iconly-boldTicket-Star"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Today's Sales</h6>
                                    <h6 class="font-extrabold mb-0">Rp <?= number_format($todaySales, 0, ',', '.') ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Recent Activity & Quick Actions -->
    <section class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4>Transaksi Terbaru</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Kode Transaksi</th>
                                    <th>Outlet</th>
                                    <th>Kasir</th>
                                    <th>Total</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentTransactions)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                            <p class="mt-2">Belum ada transaksi</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recentTransactions as $trx): ?>
                                        <tr class="clickable-row" style="cursor: pointer;" onclick="viewTransactionDetail(<?= $trx['id'] ?>)">
                                            <td><code><?= esc($trx['transaction_code']) ?></code></td>
                                            <td><?= esc($trx['outlet_name']) ?></td>
                                            <td><?= esc($trx['cashier_name']) ?></td>
                                            <td><strong>Rp <?= number_format($trx['grand_total'], 0, ',', '.') ?></strong></td>
                                            <td><small><?= date('d/m/Y H:i', strtotime($trx['created_at'])) ?></small></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4>Aksi Cepat</h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/admin/outlets" class="btn btn-primary icon icon-left">
                            <i class="bi bi-shop"></i> Kelola Outlet
                        </a>
                        <a href="/admin/users" class="btn btn-success icon icon-left">
                            <i class="bi bi-people"></i> Kelola Pengguna
                        </a>
                        <a href="/admin/products" class="btn btn-info icon icon-left">
                            <i class="bi bi-box"></i> Kelola Produk
                        </a>
                        <a href="/admin/reports" class="btn btn-warning icon icon-left">
                            <i class="bi bi-bar-chart"></i> Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h4>Informasi Sistem</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Login sebagai:</small>
                        <p class="mb-0"><strong><?= esc($user->username) ?></strong></p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Peran:</small>
                        <p class="mb-0"><span class="badge bg-primary">Administrator</span></p>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted">Tingkat Akses:</small>
                        <p class="mb-0">Akses Penuh (Semua Outlet)</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Detail Transaksi -->
<div class="modal fade" id="modalTransactionDetail" tabindex="-1" aria-labelledby="modalTransactionDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTransactionDetailLabel">Detail Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="transactionDetailContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    function viewTransactionDetail(transactionId) {
        const modal = new bootstrap.Modal(document.getElementById('modalTransactionDetail'));
        modal.show();
        
        // Load transaction detail via AJAX
        fetch(`/admin/transactions/detail/${transactionId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                
                const trx = data.transaction;
                const items = data.items;
                
                // Calculate totals
                let totalRevenue = 0;
                let totalCost = 0;
                let totalProfit = 0;
                
                let itemsHtml = '';
                items.forEach(item => {
                    const revenue = item.price * item.qty;
                    const cost = item.cost_price * item.qty;
                    const profit = revenue - cost;
                    const margin = revenue > 0 ? (profit / revenue * 100) : 0;
                    
                    totalRevenue += revenue;
                    totalCost += cost;
                    totalProfit += profit;
                    
                    itemsHtml += `
                        <tr>
                            <td>${item.product_name}</td>
                            <td class="text-center">${item.qty}</td>
                            <td class="text-end">Rp ${formatRupiah(item.price)}</td>
                            <td class="text-end text-danger">Rp ${formatRupiah(item.cost_price)}</td>
                            <td class="text-end">${item.discount > 0 ? 'Rp ' + formatRupiah(item.discount) : '-'}</td>
                            <td class="text-end text-success">
                                Rp ${formatRupiah(profit)}<br>
                                <small>(${margin.toFixed(2)}%)</small>
                            </td>
                        </tr>
                    `;
                });
                
                const totalMargin = totalRevenue > 0 ? (totalProfit / totalRevenue * 100) : 0;
                
                const html = `
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="140"><strong>Kode Transaksi</strong></td>
                                        <td><code class="fs-6">${trx.transaction_code}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Outlet</strong></td>
                                        <td>${trx.outlet_name}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kasir</strong></td>
                                        <td>${trx.cashier_name}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Customer</strong></td>
                                        <td>${trx.customer_name || '-'}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="140"><strong>Tanggal</strong></td>
                                        <td>${new Date(trx.created_at).toLocaleString('id-ID')}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Metode Bayar</strong></td>
                                        <td><span class="badge bg-info">${trx.payment_method.toUpperCase()}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status</strong></td>
                                        <td><span class="badge bg-success">LUNAS</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Detail Item & Analisis Profit</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Harga Jual</th>
                                    <th class="text-end">HPP</th>
                                    <th class="text-end">Diskon</th>
                                    <th class="text-end">Profit (Margin)</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHtml}
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2" class="fw-bold">TOTAL</td>
                                    <td class="text-end fw-bold">Rp ${formatRupiah(totalRevenue)}</td>
                                    <td class="text-end fw-bold text-danger">Rp ${formatRupiah(totalCost)}</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end fw-bold text-success">
                                        Rp ${formatRupiah(totalProfit)}<br>
                                        <small>(${totalMargin.toFixed(2)}%)</small>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <h6 class="mb-3">Ringkasan Pembayaran</h6>
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <td>Total Sebelum Diskon</td>
                                    <td class="text-end">Rp ${formatRupiah(trx.total_amount)}</td>
                                </tr>
                                <tr>
                                    <td>Total Diskon</td>
                                    <td class="text-end text-danger">- Rp ${formatRupiah(trx.total_discount)}</td>
                                </tr>
                                <tr>
                                    <td>Subtotal</td>
                                    <td class="text-end">Rp ${formatRupiah(trx.subtotal_before_tax)}</td>
                                </tr>
                                <tr>
                                    <td>Pajak (PPN 11%)</td>
                                    <td class="text-end">Rp ${formatRupiah(trx.total_tax)}</td>
                                </tr>
                                <tr class="table-primary">
                                    <td><strong>Grand Total</strong></td>
                                    <td class="text-end"><strong>Rp ${formatRupiah(trx.grand_total)}</strong></td>
                                </tr>
                                ${trx.payment_method === 'cash' ? `
                                <tr>
                                    <td>Uang Diterima</td>
                                    <td class="text-end">Rp ${formatRupiah(trx.cash_amount)}</td>
                                </tr>
                                <tr>
                                    <td>Kembalian</td>
                                    <td class="text-end">Rp ${formatRupiah(trx.change_amount)}</td>
                                </tr>
                                ` : ''}
                            </table>
                        </div>
                    </div>
                `;
                
                document.getElementById('transactionDetailContent').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('transactionDetailContent').innerHTML = `
                    <div class="alert alert-danger">Gagal memuat data: ${error.message}</div>
                `;
            });
    }

    // Clean up modal when hidden
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('modalTransactionDetail');
        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', function () {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });
        }
    });
</script>
<?= $this->endSection() ?>
