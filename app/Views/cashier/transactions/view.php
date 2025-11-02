<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="bi bi-receipt"></i> Detail Transaksi</h3>
                <p class="text-subtitle text-muted"><?= esc($transaction['transaction_code']) ?></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('pos') ?>">POS</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('cashier/transactions') ?>">Transaksi</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-md-8">
                <!-- Transaction Items -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Item Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Diskon</th>
                                    <th>Pajak</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($details as $index => $item): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <strong><?= esc($item['product_name']) ?></strong><br>
                                        <small class="text-muted"><?= esc($item['sku']) ?></small>
                                    </td>
                                    <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                    <td><?= $item['qty'] ?></td>
                                    <td>
                                        Rp <?= number_format($item['discount'], 0, ',', '.') ?>
                                        <?php if ($item['discount_note']): ?>
                                            <br><small class="text-muted"><?= esc($item['discount_note']) ?></small>
                                        <?php endif ?>
                                    </td>
                                    <td>
                                        <?php if ($item['tax_type'] !== 'NONE'): ?>
                                            <?= $item['tax_type'] ?> (<?= number_format($item['tax_rate'], 2) ?>%)<br>
                                            Rp <?= number_format($item['tax_amount'], 0, ',', '.') ?>
                                        <?php else: ?>
                                            -
                                        <?php endif ?>
                                    </td>
                                    <td class="text-end">
                                        <strong>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></strong>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-end"><strong>Total Sebelum Diskon:</strong></td>
                                    <td class="text-end">Rp <?= number_format($transaction['total_amount'], 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end"><strong>Total Diskon:</strong></td>
                                    <td class="text-end text-danger">- Rp <?= number_format($transaction['total_discount'], 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end"><strong>Subtotal (DPP):</strong></td>
                                    <td class="text-end">Rp <?= number_format($transaction['subtotal_before_tax'], 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end"><strong>Total Pajak:</strong></td>
                                    <td class="text-end">Rp <?= number_format($transaction['total_tax'], 0, ',', '.') ?></td>
                                </tr>
                                <tr class="table-active">
                                    <td colspan="6" class="text-end"><strong>GRAND TOTAL:</strong></td>
                                    <td class="text-end">
                                        <h5 class="mb-0">Rp <?= number_format($transaction['grand_total'], 0, ',', '.') ?></h5>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Transaction Info -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Informasi Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td class="text-muted">Kode Transaksi:</td>
                                <td><code><?= esc($transaction['transaction_code']) ?></code></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal:</td>
                                <td><?= date('d/m/Y H:i:s', strtotime($transaction['created_at'])) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Metode Bayar:</td>
                                <td>
                                    <span class="badge bg-primary"><?= strtoupper($transaction['payment_method']) ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status:</td>
                                <td>
                                    <?php 
                                    $statusColors = ['paid' => 'success', 'void' => 'danger', 'pending' => 'warning'];
                                    $statusColor = $statusColors[$transaction['payment_status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $statusColor ?>"><?= ucfirst($transaction['payment_status']) ?></span>
                                </td>
                            </tr>
                            <?php if ($transaction['payment_method'] === 'cash'): ?>
                            <tr>
                                <td class="text-muted">Uang Diterima:</td>
                                <td>Rp <?= number_format($transaction['cash_amount'], 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kembalian:</td>
                                <td>Rp <?= number_format($transaction['change_amount'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endif ?>
                            <?php if ($transaction['notes']): ?>
                            <tr>
                                <td class="text-muted">Catatan:</td>
                                <td><?= esc($transaction['notes']) ?></td>
                            </tr>
                            <?php endif ?>
                        </table>
                        
                        <div class="mt-3">
                            <a href="<?= base_url('cashier/transactions') ?>" class="btn btn-secondary btn-block w-100">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
