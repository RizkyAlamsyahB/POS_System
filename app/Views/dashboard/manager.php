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
                            <table class="table table-hover mb-0">
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
                                    <?php if (empty($recentTransactions)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox" style="font-size: 2.5rem;"></i>
                                                <p class="mt-2 mb-0">Belum ada transaksi</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentTransactions as $trx): ?>
                                            <tr class="clickable-row" style="cursor: pointer;" onclick="viewTransactionDetail(<?= $trx['id'] ?>)">
                                                <td><code><?= esc($trx['transaction_code']) ?></code></td>
                                                <td><?= esc($trx['cashier_name']) ?></td>
                                                <td><?= esc($trx['customer_name']) ?: '-' ?></td>
                                                <td class="text-end"><strong>Rp <?= number_format($trx['grand_total'], 0, ',', '.') ?></strong></td>
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
<!-- Pusher JS Library - LOAD FIRST -->
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>

<script>
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
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
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
            // Update dashboard stats
            updateDashboardStats(data);
            
            // Add transaction to recent transactions table
            addTransactionToTable(data);
            
            // Show notification
            showTransactionNotification(data);
        });

        /**
         * Update dashboard statistics
         */
        function updateDashboardStats(data) {
            // Update total penjualan hari ini
            const totalSalesEl = document.querySelector('.stats-icon.purple');
            if (totalSalesEl) {
                const amountEl = totalSalesEl.closest('.col-6').querySelector('h3');
                if (amountEl) {
                    const currentAmount = parseFloat(amountEl.textContent.replace(/[^0-9]/g, '')) || 0;
                    const newAmount = currentAmount + data.total;
                    amountEl.textContent = 'Rp ' + formatNumber(newAmount);
                }
            }

            // Update jumlah transaksi
            const countEl = document.querySelector('.col-6:nth-child(2) h3');
            if (countEl) {
                const currentCount = parseInt(countEl.textContent) || 0;
                countEl.textContent = currentCount + 1;
            }
        }

        /**
         * Add new transaction to the table
         */
        function addTransactionToTable(data) {
            const tbody = document.querySelector('.table-hover tbody');
            if (!tbody) return;

            // Remove "Belum ada transaksi" message if exists
            const emptyRow = tbody.querySelector('td[colspan="5"]');
            if (emptyRow) {
                emptyRow.closest('tr').remove();
            }

            // Format date
            const date = new Date(data.timestamp);
            const formattedDate = date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).replace(',', '');

            // Create new row
            const newRow = document.createElement('tr');
            newRow.className = 'clickable-row new-transaction-highlight';
            newRow.style.cursor = 'pointer';
            newRow.onclick = function() { viewTransactionDetail(data.transaction_id); };
            newRow.innerHTML = `
                <td><code>${escapeHtml(data.transaction_number)}</code></td>
                <td>${escapeHtml(data.cashier_name)}</td>
                <td>${escapeHtml(data.customer_name) || '-'}</td>
                <td class="text-end"><strong>Rp ${formatNumber(data.total)}</strong></td>
                <td><small>${formattedDate}</small></td>
            `;

            // Prepend to table (newest first)
            tbody.insertBefore(newRow, tbody.firstChild);

            // Add highlight animation
            setTimeout(() => {
                newRow.classList.remove('new-transaction-highlight');
            }, 3000);

            // Limit table to 10 rows
            const rows = tbody.querySelectorAll('tr');
            if (rows.length > 10) {
                rows[rows.length - 1].remove();
            }
        }

        /**
         * Show toast notification for new transaction
         */
        function showTransactionNotification(data) {
            const toastHtml = `
                <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; top: 80px; right: 20px; z-index: 9999;">
                    <div class="d-flex">
                        <div class="toast-body">
                            <strong>🎉 Transaksi Baru!</strong><br>
                            ${data.transaction_number}<br>
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

<!-- CSS for new transaction highlight animation -->
<style>
    @keyframes highlightTransaction {
        0% {
            background-color: #d4edda;
        }
        100% {
            background-color: transparent;
        }
    }

    .new-transaction-highlight {
        animation: highlightTransaction 3s ease-in-out;
    }
</style>

<?= $this->endSection() ?>