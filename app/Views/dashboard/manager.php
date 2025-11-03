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
                                <h3 class="font-extrabold mb-0">Rp <?= number_format($todaySales, 0, ',', '.') ?></h3>
                                <p class="text-sm text-muted mb-0"><?= number_format($todayTransactions, 0) ?> transaksi</p>
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
                                <h3 class="font-extrabold mb-0"><?= number_format($totalProducts, 0) ?></h3>
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
                                <h3 class="font-extrabold mb-0"><?= number_format($lowStock, 0) ?></h3>
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

        <!-- Recent Transactions & Quick Actions -->
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-clock-history"></i> Transaksi Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="transactionsTable" class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Kode Transaksi</th>
                                        <th>Kasir</th>
                                        <th>Customer</th>
                                        <th class="text-end">Total</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
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
                            <a href="/manager/reports" class="btn btn-info btn-lg">
                                <i class="bi bi-bar-chart"></i> Lihat Laporan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Info (moved below) -->
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-info-circle"></i> Informasi Akun</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="20%">Username:</td>
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

<!-- Modal Detail Transaksi -->
<div class="modal fade" id="modalTransactionDetail" tabindex="-1" aria-labelledby="modalTransactionDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl modal-fullscreen-md-down">
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
<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Pusher JS Library -->
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>

<script>
    let transactionsTable;

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    function viewTransactionDetail(transactionId) {
        const modal = new bootstrap.Modal(document.getElementById('modalTransactionDetail'));
        modal.show();
        
        // Load transaction detail via AJAX
        fetch(`/manager/transactions/detail/${transactionId}`)
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
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="140"><strong>Kode Transaksi</strong></td>
                                        <td><code class="fs-6">${trx.transaction_code}</code></td>
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
                            <div class="col-12 col-md-6">
                                <table class="table table-sm table-borderless mb-0">
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
                        <table class="table table-sm table-bordered mb-0" style="min-width: 600px;">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center" style="min-width: 60px;">Qty</th>
                                    <th class="text-end" style="min-width: 100px;">Harga Jual</th>
                                    <th class="text-end" style="min-width: 100px;">HPP</th>
                                    <th class="text-end" style="min-width: 80px;">Diskon</th>
                                    <th class="text-end" style="min-width: 120px;">Profit (Margin)</th>
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
                    
                    <h6 class="mb-3 mt-4">Ringkasan Pembayaran</h6>
                    <div class="row">
                        <div class="col-12 col-md-8 offset-md-4 col-lg-6 offset-lg-6">
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
        // Initialize DataTable
        transactionsTable = $('#transactionsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/manager/dashboard/transactions-datatable',
                type: 'GET'
            },
            columns: [
                {
                    data: 'transaction_code',
                    render: function(data) {
                        return `<code>${data}</code>`;
                    }
                },
                { data: 'cashier_name' },
                {
                    data: 'customer_name',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: 'grand_total',
                    className: 'text-end',
                    render: function(data) {
                        return `<strong>Rp ${formatRupiah(data)}</strong>`;
                    }
                },
                {
                    data: 'created_at',
                    render: function(data) {
                        const date = new Date(data);
                        return `<small>${date.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}</small>`;
                    }
                }
            ],
            order: [[4, 'desc']], // Sort by created_at DESC
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            createdRow: function(row, data) {
                $(row).css('cursor', 'pointer');
                $(row).on('click', function() {
                    viewTransactionDetail(data.id);
                });
            }
        });

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

    // ========== PUSHER REAL-TIME TRANSACTION UPDATES ==========
    (function() {
        // Initialize Pusher
        const pusher = new Pusher('<?= env('pusher.appKey', '16c9b2af70ac324000d9') ?>', {
            cluster: '<?= env('pusher.appCluster', 'ap1') ?>',
            encrypted: true
        });

        // Manager subscribes to outlet-specific transactions channel
        const outletId = <?= $outlet['id'] ?? 0 ?>;
        const channel = pusher.subscribe(`transactions-${outletId}`);

        // Listen for new transaction event
        channel.bind('transaction-created', function(data) {
            // Reload DataTable to show new transaction
            if (transactionsTable) {
                transactionsTable.ajax.reload(null, false); // false = stay on current page
            }
            
            // Show notification
            showTransactionNotification(data);
        });

        /**
         * Show toast notification for new transaction
         */
        function showTransactionNotification(data) {
            const toastHtml = `
                <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; top: 80px; right: 20px; z-index: 9999;">
                    <div class="d-flex">
                        <div class="toast-body">
                            <strong>🎉 Transaksi Baru!</strong><br>
                            ${escapeHtml(data.transaction_number)}<br>
                            Total: <strong>Rp ${formatNumber(data.total)}</strong><br>
                            <small>Kasir: ${escapeHtml(data.cashier_name)}</small>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            
            const toastContainer = document.createElement('div');
            toastContainer.innerHTML = toastHtml;
            document.body.appendChild(toastContainer);
            
            const toastElement = toastContainer.querySelector('.toast');
            const toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: 5000
            });
            toast.show();
            
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastContainer.remove();
            });
        }

        /**
         * Helper: Format number with thousands separator
         */
        function formatNumber(num) {
            return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        /**
         * Helper: Escape HTML to prevent XSS
         */
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    })();
</script>

<?= $this->endSection() ?>