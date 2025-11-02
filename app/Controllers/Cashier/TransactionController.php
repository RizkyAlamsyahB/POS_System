<?php

namespace App\Controllers\Cashier;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;
use App\Models\ProductModel;
use App\Models\ProductStockModel;
use App\Models\OutletModel;

class TransactionController extends BaseController
{
    protected $transactionModel;
    protected $detailModel;
    protected $productModel;
    protected $stockModel;
    protected $outletModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
        $this->detailModel = new TransactionDetailModel();
        $this->productModel = new ProductModel();
        $this->stockModel = new ProductStockModel();
        $this->outletModel = new OutletModel();
    }

    /**
     * Process checkout from POS
     */
    public function checkout()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $user = auth()->user();
        $outletId = $user->outlet_id;

        // Validate outlet
        if (!$outletId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda tidak memiliki outlet yang ditugaskan!'
            ]);
        }

        // Check outlet is active
// Check outlet exists
$outlet = $this->outletModel->find($outletId);

if (!$outlet) {
    return $this->response->setJSON([
        'success' => false,
        'message' => 'Outlet tidak ditemukan!'
    ]);
}

// Check if outlet is active (with null check)
$isActive = isset($outlet['is_active']) ? (int)$outlet['is_active'] : 1;
if ($isActive !== 1) {
    return $this->response->setJSON([
        'success' => false,
        'message' => 'Outlet sedang nonaktif. Hubungi administrator!'
    ]);
}
        // Get cart data from request
        $cartItems = $this->request->getJSON(true)['items'] ?? [];
        $paymentMethod = $this->request->getJSON(true)['payment_method'] ?? 'cash';
        $cashAmount = (float) ($this->request->getJSON(true)['cash_amount'] ?? 0);
        $notes = $this->request->getJSON(true)['notes'] ?? null;

        if (empty($cartItems)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Keranjang belanja kosong!'
            ]);
        }

        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Generate transaction code: TRX-OUT001-20241101-0001
            $transactionCode = $this->generateTransactionCode($outletId);

            // Calculate totals
            $totalAmount = 0;
            $totalDiscount = 0;
            $subtotalBeforeTax = 0;
            $totalTax = 0;
            $grandTotal = 0;

            $details = [];

            foreach ($cartItems as $item) {
                $productId = $item['product_id'];
                $qty = (int) $item['qty'];
                $discount = (float) ($item['discount'] ?? 0);

                // Get product
                $product = $this->productModel->find($productId);
                if (!$product) {
                    throw new \Exception("Produk ID {$productId} tidak ditemukan!");
                }

                // Check stock
                $stock = $this->stockModel->where('product_id', $productId)
                                          ->where('outlet_id', $outletId)
                                          ->first();

                if (!$stock || $stock['stock'] < $qty) {
                    throw new \Exception("Stok {$product['name']} tidak mencukupi!");
                }

                // Calculate price
                $price = (float) $product['price'];
                $costPrice = (float) $product['cost_price'];
                $itemTotal = $price * $qty;
                $itemDiscount = $discount;
                $itemAfterDiscount = $itemTotal - $itemDiscount;

                // Calculate tax
                $taxRate = (float) $product['tax_rate'];
                $taxType = $product['tax_type'];
                $taxAmount = 0;

                if ($taxType !== 'NONE' && $taxRate > 0) {
                    if ($product['tax_included']) {
                        // Tax included: harga sudah termasuk pajak
                        $taxAmount = $itemAfterDiscount - ($itemAfterDiscount / (1 + ($taxRate / 100)));
                    } else {
                        // Tax excluded: pajak ditambahkan ke harga
                        $taxAmount = $itemAfterDiscount * ($taxRate / 100);
                    }
                }

                $itemSubtotal = $itemAfterDiscount + ($product['tax_included'] ? 0 : $taxAmount);

                // Accumulate totals
                $totalAmount += $itemTotal;
                $totalDiscount += $itemDiscount;
                $totalTax += $taxAmount;
                $grandTotal += $itemSubtotal;

                // Store detail for later insert
                $details[] = [
                    'product_id'    => $productId,
                    'qty'           => $qty,
                    'price'         => $price,
                    'cost_price'    => $costPrice,
                    'discount'      => $itemDiscount,
                    'discount_note' => $item['discount_note'] ?? null,
                    'tax_type'      => $taxType,
                    'tax_rate'      => $taxRate,
                    'tax_amount'    => $taxAmount,
                    'subtotal'      => $itemSubtotal,
                ];

                // Reduce stock
                $newStock = $stock['stock'] - $qty;
                $this->stockModel->update($stock['id'], ['stock' => $newStock]);
            }

            $subtotalBeforeTax = $totalAmount - $totalDiscount;

            // Calculate change
            $changeAmount = 0;
            if ($paymentMethod === 'cash') {
                if ($cashAmount < $grandTotal) {
                    throw new \Exception('Uang tunai tidak mencukupi!');
                }
                $changeAmount = $cashAmount - $grandTotal;
            } else {
                $cashAmount = $grandTotal; // For non-cash, cash_amount = grand_total
            }

            // Insert transaction header
            $transactionData = [
                'transaction_code'    => $transactionCode,
                'outlet_id'           => $outletId,
                'user_id'             => $user->id,
                'total_amount'        => $totalAmount,
                'total_discount'      => $totalDiscount,
                'subtotal_before_tax' => $subtotalBeforeTax,
                'total_tax'           => $totalTax,
                'grand_total'         => $grandTotal,
                'payment_method'      => $paymentMethod,
                'cash_amount'         => $cashAmount,
                'change_amount'       => $changeAmount,
                'payment_status'      => 'paid',
                'notes'               => $notes,
            ];

            $transactionId = $this->transactionModel->insert($transactionData);

            if (!$transactionId) {
                throw new \Exception('Gagal menyimpan transaksi!');
            }

            // Insert transaction details
            foreach ($details as $detail) {
                $detail['transaction_id'] = $transactionId;
                $this->detailModel->insert($detail);
            }

            // Commit transaction
            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal!');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'data'    => [
                    'transaction_id'   => $transactionId,
                    'transaction_code' => $transactionCode,
                    'grand_total'      => $grandTotal,
                    'cash_amount'      => $cashAmount,
                    'change_amount'    => $changeAmount,
                ]
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Checkout gagal: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate unique transaction code
     */
    private function generateTransactionCode($outletId)
    {
        $outlet = $this->outletModel->find($outletId);
        $outletCode = $outlet['code'];
        $date = date('Ymd');
        
        // Get last transaction today
        $lastTransaction = $this->transactionModel
            ->where('outlet_id', $outletId)
            ->like('transaction_code', "TRX-{$outletCode}-{$date}", 'after')
            ->orderBy('id', 'DESC')
            ->first();

        $sequence = 1;
        if ($lastTransaction) {
            // Extract sequence from last code: TRX-OUT001-20241101-0001
            $parts = explode('-', $lastTransaction['transaction_code']);
            $sequence = isset($parts[3]) ? ((int) $parts[3]) + 1 : 1;
        }

        return sprintf('TRX-%s-%s-%04d', $outletCode, $date, $sequence);
    }

    /**
     * Get transaction history for cashier
     */
    public function history()
    {
        $user = auth()->user();
        $outletId = $user->outlet_id;

        $data = [
            'title'  => 'Riwayat Transaksi',
            'user'   => $user,
        ];

        return view('cashier/transactions/history', $data);
    }

    /**
     * DataTable endpoint for transaction history
     */
    public function datatable()
    {
        $user = auth()->user();
        $outletId = $user->outlet_id;
        $userId = $user->id;

        $request = $this->request;
        
        // Get DataTable parameters
        $draw = intval($request->getGet('draw') ?? 0);
        $start = intval($request->getGet('start') ?? 0);
        $length = intval($request->getGet('length') ?? 10);
        
        // Get search value
        $searchValue = $request->getGet('search');
        $search = is_array($searchValue) ? ($searchValue['value'] ?? '') : '';

        // Build query
        $builder = $this->transactionModel->builder();
        $builder->select('transactions.*')
                ->where('transactions.outlet_id', $outletId);

        // If cashier, show only their transactions
        if ($user->inGroup('cashier')) {
            $builder->where('transactions.user_id', $userId);
        }

        // Apply search
        if (!empty($search)) {
            $builder->like('transaction_code', $search);
        }

        // Get total records
        $totalRecords = $builder->countAllResults(false);
        
        // Apply ordering and pagination
        $transactions = $builder->orderBy('created_at', 'DESC')
                               ->limit($length, $start)
                               ->get()
                               ->getResultArray();

        // Format data
        $data = [];
        foreach ($transactions as $index => $trx) {
            $data[] = [
                $start + $index + 1,
                '<code>' . esc($trx['transaction_code']) . '</code>',
                date('d/m/Y H:i', strtotime($trx['created_at'])),
                'Rp ' . number_format($trx['grand_total'], 0, ',', '.'),
                '<span class="badge bg-' . $this->getPaymentMethodBadge($trx['payment_method']) . '">' . 
                    strtoupper($trx['payment_method']) . '</span>',
                '<span class="badge bg-' . $this->getStatusBadge($trx['payment_status']) . '">' . 
                    ucfirst($trx['payment_status']) . '</span>',
                view('cashier/transactions/_actions', ['transaction' => $trx]),
            ];
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }

    /**
     * View transaction detail
     */
    public function view($id)
    {
        $transaction = $this->transactionModel->find($id);

        if (!$transaction) {
            return redirect()->back()->with('error', 'Transaksi tidak ditemukan!');
        }

        // Get details with product info
        $details = $this->detailModel->getTransactionDetails($id);

        $data = [
            'title'       => 'Detail Transaksi',
            'transaction' => $transaction,
            'details'     => $details,
        ];

        return view('cashier/transactions/view', $data);
    }

    private function getPaymentMethodBadge($method)
    {
        $badges = [
            'cash'    => 'success',
            'debit'   => 'primary',
            'credit'  => 'warning',
            'ewallet' => 'info',
        ];
        return $badges[$method] ?? 'secondary';
    }

    private function getStatusBadge($status)
    {
        $badges = [
            'paid'    => 'success',
            'void'    => 'danger',
            'pending' => 'warning',
        ];
        return $badges[$status] ?? 'secondary';
    }
}
