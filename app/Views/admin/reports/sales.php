<?= $this->extend('layouts/app') ?>
<?= $this->section('styles') ?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
/* Tables */
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}
.clickable-row {
    cursor: pointer;
}
.clickable-row:hover {
    background-color: #f8f9fa;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem !important;
    }
    h3 {
        font-size: 1.5rem;
    }
    h5 {
        font-size: 1.1rem;
    }
}
</style>
<?= $this->endSection() ?>



<?= $this->section('content') ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Laporan Penjualan - Admin</h3>
                <p class="text-subtitle text-muted">Ringkasan penjualan semua outlet</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Laporan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <section class="section">
        <div class="card">
            <div class="card-body">
                <!-- Quick Filter Buttons -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Filter Cepat</label>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="?filter=today<?= $selectedOutlet ? '&outlet_id=' . $selectedOutlet : '' ?>" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-calendar-day"></i> Hari Ini
                        </a>
                        <a href="?filter=week<?= $selectedOutlet ? '&outlet_id=' . $selectedOutlet : '' ?>" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-calendar-week"></i> Minggu Ini
                        </a>
                        <a href="?filter=month<?= $selectedOutlet ? '&outlet_id=' . $selectedOutlet : '' ?>" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-calendar-month"></i> Bulan Ini
                        </a>
                        <a href="?filter=year<?= $selectedOutlet ? '&outlet_id=' . $selectedOutlet : '' ?>" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-calendar-range"></i> Tahun Ini
                        </a>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="toggleCustomFilter">
                            <i class="bi bi-calendar3"></i> Custom
                        </button>
                    </div>
                </div>

                <hr>

                <!-- Custom Date Filter Form -->
                <form method="GET" action="/admin/reports" id="customFilterForm" style="<?= isset($filterType) && $filterType !== 'custom' ? 'display:none;' : '' ?>">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" value="<?= esc($startDate) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control" value="<?= esc($endDate) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pilih Outlet (Opsional)</label>
                            <select name="outlet_id" class="form-select">
                                <option value="">-- Semua Outlet --</option>
                                <?php foreach ($outlets as $outlet): ?>
                                    <option value="<?= $outlet['id'] ?>" <?= $selectedOutlet == $outlet['id'] ? 'selected' : '' ?>>
                                        <?= esc($outlet['code']) ?> - <?= esc($outlet['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- Summary Cards -->
    <section class="section mb-4">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <p class="text-muted mb-2">Total Penjualan</p>
                        <h3 class="fw-bold mb-2" style="color: #3772F0;">
                            Rp <?= number_format($grandTotal, 0, ',', '.') ?>
                        </h3>
                        <small class="text-muted">
                            Periode <?= date('d/m/Y', strtotime($startDate)) ?> - <?= date('d/m/Y', strtotime($endDate)) ?>
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <p class="text-muted mb-2">Total Transaksi</p>
                        <h3 class="fw-bold mb-2" style="color: #10b981;">
                            <?= number_format($grandTransactions, 0, ',', '.') ?>
                        </h3>
                        <small class="text-muted">Dari semua outlet</small>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <?php if ($selectedOutlet && $outletInfo): ?>
        <!-- Outlet Detail Section -->
        <section class="section">
            <div class="card border-0 shadow-sm">
                <div class="card-header" style="background-color: #3772F0; color: white; border: none;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 text-white">
                                <i class="bi bi-shop"></i> Detail Transaksi - <?= esc($outletInfo['name']) ?>
                            </h5>
                            <small><?= esc($outletInfo['code']) ?> | <?= esc($outletInfo['address']) ?></small>
                        </div>
                        <a href="/admin/reports?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>" class="btn btn-sm btn-light">
                            <i class="bi bi-x-circle"></i> Reset Filter
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Profit Cards -->
                    <?php if ($outletProfit): ?>
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-3">
                                        <p class="text-muted mb-2 small">Total Revenue</p>
                                        <h5 class="mb-0 fw-bold" style="color: #10b981;">Rp <?= number_format($outletProfit['total_revenue'], 0, ',', '.') ?></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-3">
                                        <p class="text-muted mb-2 small">Total HPP (Cost)</p>
                                        <h5 class="mb-0 fw-bold" style="color: #ef4444;">Rp <?= number_format($outletProfit['total_cost'], 0, ',', '.') ?></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-3">
                                        <p class="text-muted mb-2 small">Gross Profit</p>
                                        <h5 class="mb-0 fw-bold" style="color: #f59e0b;">Rp <?= number_format($outletProfit['gross_profit'], 0, ',', '.') ?></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-3">
                                        <p class="text-muted mb-2 small">Profit Margin</p>
                                        <h5 class="mb-0 fw-bold" style="color: #8b5cf6;"><?= number_format($outletProfit['profit_margin'], 2) ?>%</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Transaction List -->
                    <div class="table-responsive">
                        <table id="transactionsTable" class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode Transaksi</th>
                                    <th>Tanggal & Waktu</th>
                                    <th>Kasir</th>
                                    <th>Customer</th>
                                    <th>Payment</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data loaded via DataTables AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Summary Per Outlet -->
    <section class="section mt-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Ringkasan Per Outlet</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Kode Outlet</th>
                                <th>Nama Outlet</th>
                                <th class="text-end">Total Transaksi</th>
                                <th class="text-end">Total Penjualan</th>
                                <th class="text-end">Total HPP</th>
                                <th class="text-end">Gross Profit</th>
                                <th class="text-end">Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($outletSummary)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        Tidak ada data transaksi pada periode ini
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($outletSummary as $outlet): ?>
                                    <tr>
                                        <td><span class="badge bg-light-primary"><?= esc($outlet['outlet_code']) ?></span></td>
                                        <td><strong><?= esc($outlet['outlet_name']) ?></strong></td>
                                        <td class="text-end"><?= number_format($outlet['total_transactions'], 0, ',', '.') ?></td>
                                        <td class="text-end fw-bold text-success">Rp <?= number_format($outlet['total_revenue'] ?? 0, 0, ',', '.') ?></td>
                                        <td class="text-end text-danger">Rp <?= number_format($outlet['total_cost'] ?? 0, 0, ',', '.') ?></td>
                                        <td class="text-end fw-bold text-warning">Rp <?= number_format($outlet['gross_profit'] ?? 0, 0, ',', '.') ?></td>
                                        <td class="text-end">
                                            <span class="badge bg-info">
                                                <?= number_format($outlet['profit_margin'] ?? 0, 1) ?>%
                                            </span>
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

    <!-- Top Products -->
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Top 10 Produk Terlaris</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>SKU</th>
                                <th>Nama Produk</th>
                                <th class="text-end">Qty Terjual</th>
                                <th class="text-end">Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topProducts)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Tidak ada data produk</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($topProducts as $index => $product): ?>
                                    <tr>
                                        <td>
                                            <?php if ($index < 3): ?>
                                                <span class="badge bg-warning">
                                                    <i class="bi bi-trophy-fill"></i> <?= $index + 1 ?>
                                                </span>
                                            <?php else: ?>
                                                <?= $index + 1 ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><code><?= esc($product['sku']) ?></code></td>
                                        <td><?= esc($product['product_name']) ?></td>
                                        <td class="text-end fw-bold"><?= number_format($product['total_qty'], 0, ',', '.') ?></td>
                                        <td class="text-end text-success fw-bold">Rp <?= number_format($product['total_revenue'], 0, ',', '.') ?></td>
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

<!-- Modal Detail Transaksi -->
<div class="modal fade" id="modalTransactionDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Transaksi & Analisis Profit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    // Auto submit on date change
    document.querySelectorAll('input[type="date"]').forEach(input => {
        input.addEventListener('change', function() {
            this.form.submit();
        });
    });

    <?php if ($selectedOutlet && $outletInfo): ?>
    // Initialize DataTables for transactions
    $(document).ready(function() {
        var table = $('#transactionsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/admin/reports/transactions-datatable',
                type: 'GET',
                data: {
                    outlet_id: <?= $selectedOutlet ?>,
                    start_date: '<?= $startDate ?>',
                    end_date: '<?= $endDate ?>'
                }
            },
            columns: [
                {
                    data: 'transaction_code',
                    render: function(data, type, row) {
                        return '<code class="text-primary fw-bold">' + data + '</code>';
                    }
                },
                {
                    data: 'created_at',
                    render: function(data) {
                        const date = new Date(data);
                        return '<small>' + date.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }) + '</small>';
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
                    data: 'payment_method',
                    render: function(data) {
                        const badges = {
                            'cash': 'bg-success',
                            'debit': 'bg-primary',
                            'credit': 'bg-warning',
                            'ewallet': 'bg-info'
                        };
                        const badgeClass = badges[data] || 'bg-secondary';
                        return '<span class="badge ' + badgeClass + '">' + data.toUpperCase() + '</span>';
                    }
                },
                {
                    data: 'grand_total',
                    className: 'text-end fw-bold',
                    render: function(data) {
                        return 'Rp ' + parseInt(data).toLocaleString('id-ID');
                    }
                },
                {
                    data: 'id',
                    className: 'text-center',
                    orderable: false,
                    render: function(data) {
                        return '<button class="btn btn-sm btn-outline-primary view-detail" data-id="' + data + '">' +
                            '<i class="bi bi-eye"></i> Detail</button>';
                    }
                }
            ],
            order: [[1, 'desc']],
            pageLength: 10,
            language: {
                processing: "Memuat data...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ transaksi",
                infoEmpty: "Tidak ada data",
                infoFiltered: "(difilter dari _MAX_ total transaksi)",
                zeroRecords: "Tidak ada transaksi ditemukan",
                emptyTable: "Tidak ada transaksi pada periode ini",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });

        // Click handler for detail button
        $('#transactionsTable').on('click', '.view-detail', function() {
            const transactionId = $(this).data('id');
            viewTransactionDetail(transactionId);
        });

        // Click row to view detail (optional)
        $('#transactionsTable tbody').on('click', 'tr', function() {
            const data = table.row(this).data();
            if (data) {
                viewTransactionDetail(data.id);
            }
        });
    });
    <?php endif; ?>

    function viewTransactionDetail(transactionId) {
        // Get existing modal element
        const modalElement = document.getElementById('modalTransactionDetail');
        const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
        
        modal.show();

        // Load transaction detail via AJAX
        fetch(`/api/transaction-detail/${transactionId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('transactionDetailContent').innerHTML = `
                    <div class="alert alert-danger">${data.error}</div>
                `;
                    return;
                }

                const trx = data.transaction;
                const details = data.details;

                // Helper function to format rupiah
                function formatRupiah(num) {
                    return parseInt(num).toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                }

                // Calculate profit
                let totalRevenue = 0;
                let totalCost = 0;
                let itemsHtml = '';

                details.forEach(item => {
                    const revenue = parseFloat(item.price) * parseInt(item.qty);
                    const cost = parseFloat(item.cost_price) * parseInt(item.qty);
                    const profit = revenue - cost;
                    const margin = revenue > 0 ? (profit / revenue) * 100 : 0;

                    totalRevenue += revenue;
                    totalCost += cost;

                    itemsHtml += `
                    <tr>
                        <td>
                            <strong>${item.product_name}</strong><br>
                            <small class="text-muted">SKU: ${item.sku || '-'}</small>
                        </td>
                        <td class="text-center">${item.qty}</td>
                        <td class="text-end">Rp ${formatRupiah(item.price)}</td>
                        <td class="text-end text-danger">Rp ${formatRupiah(item.cost_price)}</td>
                        <td class="text-end">Rp ${formatRupiah(item.discount)}</td>
                        <td class="text-end fw-bold ${profit >= 0 ? 'text-success' : 'text-danger'}">
                            Rp ${formatRupiah(profit)}<br>
                            <small>(${margin.toFixed(1)}%)</small>
                        </td>
                    </tr>
                `;
                });

                const totalProfit = totalRevenue - totalCost;
                const totalMargin = totalRevenue > 0 ? (totalProfit / totalRevenue) * 100 : 0;

                const html = `
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="140"><strong>Kode Transaksi</strong></td>
                                    <td>: <code class="text-primary">${trx.transaction_code}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal</strong></td>
                                    <td>: ${new Date(trx.created_at).toLocaleString('id-ID')}</td>
                                </tr>
                                <tr>
                                    <td><strong>Customer</strong></td>
                                    <td>: ${trx.customer_name || '-'}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="120"><strong>Payment</strong></td>
                                    <td>: <span class="badge bg-primary">${trx.payment_method.toUpperCase()}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>: <span class="badge bg-success">${trx.payment_status.toUpperCase()}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Profit Summary Cards -->
                <div class="row g-3 mb-3">
                    <div class="col-6 col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-3">
                                <small class="text-muted d-block mb-1">Total Revenue</small>
                                <h6 class="mb-0 fw-bold" style="color: #10b981;">Rp ${formatRupiah(totalRevenue)}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-3">
                                <small class="text-muted d-block mb-1">Total HPP</small>
                                <h6 class="mb-0 fw-bold" style="color: #ef4444;">Rp ${formatRupiah(totalCost)}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-3">
                                <small class="text-muted d-block mb-1">Gross Profit</small>
                                <h6 class="mb-0 fw-bold" style="color: #f59e0b;">Rp ${formatRupiah(totalProfit)}</h6>
                                <small class="text-muted">(${totalMargin.toFixed(2)}%)</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h6 class="mb-3">Item Produk & Analisis Profit</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-sm">
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
        modalElement.addEventListener('hidden.bs.modal', function () {
            // Remove any lingering backdrops
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            
            // Restore body scroll
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });

        // Toggle custom filter form
        const toggleBtn = document.getElementById('toggleCustomFilter');
        const customForm = document.getElementById('customFilterForm');
        
        if (toggleBtn && customForm) {
            toggleBtn.addEventListener('click', function() {
                if (customForm.style.display === 'none') {
                    customForm.style.display = 'block';
                } else {
                    customForm.style.display = 'none';
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>