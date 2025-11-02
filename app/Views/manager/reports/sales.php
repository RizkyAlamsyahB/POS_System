<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
    .clickable-row {
        cursor: pointer;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3 class="text-dark">Laporan Penjualan</h3>
                <p class="text-subtitle text-muted"><?= esc($outlet['name']) ?> - <?= esc($outlet['code']) ?></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/manager/dashboard">Dashboard</a></li>
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
                    <div class="btn-group" role="group">
                        <a href="?filter=week" class="btn btn-sm <?= ($filterType ?? '') == 'week' ? 'btn-primary' : 'btn-outline-primary' ?>">
                            <i class="bi bi-calendar-week"></i> Minggu Ini
                        </a>
                        <a href="?filter=month" class="btn btn-sm <?= ($filterType ?? '') == 'month' ? 'btn-primary' : 'btn-outline-primary' ?>">
                            <i class="bi bi-calendar-month"></i> Bulan Ini
                        </a>
                        <a href="?filter=year" class="btn btn-sm <?= ($filterType ?? '') == 'year' ? 'btn-primary' : 'btn-outline-primary' ?>">
                            <i class="bi bi-calendar-range"></i> Tahun Ini
                        </a>
                        <button type="button" class="btn btn-sm <?= ($filterType ?? '') == 'custom' ? 'btn-primary' : 'btn-outline-secondary' ?>" onclick="document.getElementById('customFilter').style.display='block'">
                            <i class="bi bi-funnel"></i> Custom
                        </button>
                    </div>
                </div>

                <!-- Custom Date Filter (Hidden by default if using quick filter) -->
                <div id="customFilter" style="display: <?= ($filterType ?? 'custom') == 'custom' ? 'block' : 'none' ?>;">
                    <form method="GET" action="/manager/reports">
                        <input type="hidden" name="filter" value="custom">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control" value="<?= esc($startDate) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control" value="<?= esc($endDate) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Filter Laporan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Summary Cards -->
    <section class="section mb-4">
        <div class="row">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <small class="text-muted d-block mb-1">Total Penjualan</small>
                        <h6 class="mb-0 fw-bold" style="color: #10b981;">Rp <?= number_format($summary['total_sales'] ?? 0, 0, ',', '.') ?></h6>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <small class="text-muted d-block mb-1">Pendapatan Bersih</small>
                        <h6 class="mb-0 fw-bold" style="color: #f59e0b;">Rp <?= number_format($profit['gross_profit'] ?? 0, 0, ',', '.') ?></h6>
                        <small class="text-muted"><?= number_format($profit['profit_margin'] ?? 0, 1) ?>% margin</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <small class="text-muted d-block mb-1">Total Transaksi</small>
                        <h6 class="mb-0 fw-bold" style="color: #3b82f6;"><?= number_format($summary['total_transactions'] ?? 0, 0, ',', '.') ?></h6>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <small class="text-muted d-block mb-1">Total HPP</small>
                        <h6 class="mb-0 fw-bold" style="color: #ef4444;">Rp <?= number_format($profit['total_cost'] ?? 0, 0, ',', '.') ?></h6>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Two Column Layout -->
    <div class="row">
        <!-- Left Column: Transactions List -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detail Transaksi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="transactionsTable" class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Kode Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Kasir</th>
                                    <th>Customer</th>
                                    <th>Payment</th>
                                    <th class="text-end">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data loaded via DataTables AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Charts & Stats -->
        <div class="col-lg-4">
            <!-- Top Products -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">Top 5 Produk Terlaris</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($topProducts)): ?>
                        <p class="text-muted text-center mb-0">Tidak ada data</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach (array_slice($topProducts, 0, 5) as $index => $product): ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="badge bg-primary me-2">#<?= $index + 1 ?></span>
                                                <small class="fw-bold"><?= esc($product['product_name']) ?></small>
                                            </div>
                                            <small class="text-muted">SKU: <?= esc($product['sku']) ?></small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-success"><?= number_format($product['total_qty'], 0) ?> pcs</div>
                                            <small class="text-muted">Rp <?= number_format($product['total_revenue'], 0, ',', '.') ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Payment Breakdown -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Metode Pembayaran</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($paymentBreakdown)): ?>
                        <p class="text-muted text-center mb-0">Tidak ada data</p>
                    <?php else: ?>
                        <?php foreach ($paymentBreakdown as $payment): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <div class="fw-bold text-capitalize"><?= esc($payment['payment_method']) ?></div>
                                    <small class="text-muted"><?= number_format($payment['total_transactions'], 0) ?> transaksi</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-success">Rp <?= number_format($payment['total_amount'], 0, ',', '.') ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div class="modal fade" id="modalTransactionDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Transaksi</h5>
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
    function viewTransactionDetail(transactionId) {
        // Get existing modal element
        const modalElement = document.getElementById('modalTransactionDetail');
        const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);

        modal.show();

        // Helper function to format rupiah
        function formatRupiah(num) {
            return parseInt(num).toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

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

                    let itemsHtml = '';
                    details.forEach(item => {
                        itemsHtml += `
                    <tr>
                        <td>${item.product_name}</td>
                        <td class="text-center">${item.qty}</td>
                        <td class="text-end">Rp ${formatRupiah(item.price)}</td>
                        <td class="text-end">Rp ${formatRupiah(item.discount)}</td>
                        <td class="text-end fw-bold">Rp ${formatRupiah(item.subtotal)}</td>
                    </tr>
                `;
                    });

                    const html = `
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="120"><strong>Kode Transaksi</strong></td>
                                    <td>: <code>${trx.transaction_code}</code></td>
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
                
                <h6 class="mb-3">Item Produk</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Diskon</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHtml}
                        </tbody>
                    </table>
                </div>
                
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
                                <td>Pajak</td>
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

    // Initialize DataTables
    $(document).ready(function() {
        var table = $('#transactionsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/manager/reports/transactions-datatable',
                type: 'GET',
                data: {
                    start_date: '<?= $startDate ?>',
                    end_date: '<?= $endDate ?>'
                }
            },
            columns: [
                {
                    data: 'transaction_code',
                    render: function(data) {
                        return '<code class="text-primary">' + data + '</code>';
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
                    orderable: false,
                    render: function(data) {
                        return '<button class="btn btn-sm btn-outline-primary view-detail" data-id="' + data + '">' +
                            '<i class="bi bi-eye"></i></button>';
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

        // Click row to view detail
        $('#transactionsTable tbody').on('click', 'tr', function() {
            const data = table.row(this).data();
            if (data) {
                viewTransactionDetail(data.id);
            }
        });

        // Clean up modal when hidden
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
    });
</script>
<?= $this->endSection() ?>