<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;
use App\Models\ProductModel;
use App\Models\ProductStockModel;
use CodeIgniter\HTTP\ResponseInterface;

class PosController extends BaseController
{
    protected $transactionModel;
    protected $transactionDetailModel;
    protected $productModel;
    protected $productStockModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
        $this->transactionDetailModel = new TransactionDetailModel();
        $this->productModel = new ProductModel();
        $this->productStockModel = new ProductStockModel();
    }

    /**
     * Process checkout transaction
     */
    public function checkout()
    {
        // Only accept AJAX POST requests
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ])->setStatusCode(ResponseInterface::HTTP_METHOD_NOT_ALLOWED);
        }

        // Get current user
        $user = auth()->user();
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not authenticated'
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        // Get outlet_id from user
        $outletId = $user->outlet_id;
        if (!$outletId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User is not assigned to any outlet'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Get JSON input
        $input = $this->request->getJSON(true);

        // Validate input
        if (empty($input['items']) || !is_array($input['items'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cart items are required'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Calculate totals
            $totalAmount = 0;
            $totalDiscount = 0;
            $totalTax = 0; // Tax yang ditambahkan (tax_included = false)
            $totalTaxIncluded = 0; // Tax yang sudah termasuk (tax_included = true) - untuk laporan
            $subtotalBeforeTax = 0;

            $items = [];

            // Process each cart item
            foreach ($input['items'] as $cartItem) {
                $productId = $cartItem['product_id'];
                $qty = (int)($cartItem['qty'] ?? 1);
                $discount = (float)($cartItem['discount'] ?? 0);
                $discountNote = $cartItem['discount_note'] ?? null;

                // Get product details from database
                $product = $this->productModel->find($productId);
                if (!$product) {
                    throw new \Exception("Product ID {$productId} not found");
                }

                // Check stock
                $stock = $this->productStockModel
                    ->where('product_id', $productId)
                    ->where('outlet_id', $outletId)
                    ->first();

                if (!$stock || $stock['stock'] < $qty) {
                    throw new \Exception("Insufficient stock for product: {$product['name']}");
                }

                // Calculate item amounts
                $price = (float)$product['price'];
                $costPrice = (float)$product['cost_price'];
                $itemSubtotal = $price * $qty;
                
                $totalAmount += $itemSubtotal;
                $totalDiscount += $discount;

                // Calculate tax for this item
                $itemTaxAmount = 0;
                $priceAfterDiscount = $itemSubtotal - $discount;

                if ($product['tax_type'] !== 'NONE' && $product['tax_rate'] > 0) {
                    if ($product['tax_included']) {
                        // Tax already included - calculate for record keeping
                        $taxDivisor = 1 + ((float)$product['tax_rate'] / 100);
                        $itemTaxAmount = ($priceAfterDiscount / $taxDivisor) * ((float)$product['tax_rate'] / 100);
                        $totalTaxIncluded += $itemTaxAmount; // Simpan untuk laporan pajak
                    } else {
                        // Tax NOT included - add to total
                        $itemTaxAmount = $priceAfterDiscount * ((float)$product['tax_rate'] / 100);
                        $totalTax += $itemTaxAmount; // Ditambahkan ke grand total
                    }
                }

                $itemFinalSubtotal = $priceAfterDiscount + ($product['tax_included'] ? 0 : $itemTaxAmount);

                // Prepare transaction detail
                $items[] = [
                    'product_id' => $productId,
                    'qty' => $qty,
                    'price' => $price,
                    'cost_price' => $costPrice,
                    'discount' => $discount,
                    'discount_note' => $discountNote,
                    'tax_type' => $product['tax_type'],
                    'tax_rate' => (float)$product['tax_rate'],
                    'tax_amount' => $itemTaxAmount,
                    'subtotal' => $itemFinalSubtotal,
                ];
            }

            // Calculate grand total
            $subtotalBeforeTax = $totalAmount - $totalDiscount;
            $grandTotal = $subtotalBeforeTax + $totalTax;

            // Get payment details
            $paymentMethod = $input['payment_method'] ?? 'cash';
            $cashAmount = (float)($input['cash_amount'] ?? $grandTotal);
            $changeAmount = 0;

            // Validate payment for cash
            if ($paymentMethod === 'cash') {
                if ($cashAmount < $grandTotal) {
                    throw new \Exception('Insufficient cash amount');
                }
                $changeAmount = $cashAmount - $grandTotal;
            } else {
                // For non-cash payments, set cash_amount = grand_total
                $cashAmount = $grandTotal;
            }

            // Generate transaction code
            $transactionCode = $this->generateTransactionCode($outletId);
            
            // Get order info from input
            $orderType = $input['order_type'] ?? 'dine_in';
            $tableNumber = $input['table_number'] ?? null;
            $customerName = $input['customer_name'] ?? null;

            // Create transaction header
            $transactionData = [
                'transaction_code' => $transactionCode,
                'outlet_id' => $outletId,
                'user_id' => $user->id,
                'order_type' => $orderType,
                'table_number' => $tableNumber,
                'customer_name' => $customerName,
                'total_amount' => $totalAmount,
                'total_discount' => $totalDiscount,
                'subtotal_before_tax' => $subtotalBeforeTax,
                'total_tax' => $totalTax,
                'total_tax_included' => $totalTaxIncluded,
                'grand_total' => $grandTotal,
                'payment_method' => $paymentMethod,
                'cash_amount' => $cashAmount,
                'change_amount' => $changeAmount,
                'payment_status' => 'paid',
                'notes' => $input['notes'] ?? null,
            ];

            $transactionId = $this->transactionModel->insert($transactionData);

            if (!$transactionId) {
                throw new \Exception('Failed to create transaction: ' . json_encode($this->transactionModel->errors()));
            }

            // Insert transaction details and update stock
            foreach ($items as $item) {
                $item['transaction_id'] = $transactionId;
                $detailId = $this->transactionDetailModel->insert($item);

                if (!$detailId) {
                    throw new \Exception('Failed to create transaction detail');
                }

                // Update stock (deduct)
                $this->productStockModel
                    ->where('product_id', $item['product_id'])
                    ->where('outlet_id', $outletId)
                    ->set('stock', 'stock - ' . $item['qty'], false)
                    ->update();
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            // Success response
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Transaction completed successfully',
                'data' => [
                    'transaction_id' => $transactionId,
                    'transaction_code' => $transactionCode,
                    'grand_total' => $grandTotal,
                    'cash_amount' => $cashAmount,
                    'change_amount' => $changeAmount,
                ]
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Generate unique transaction code
     * Format: TRX-OUT{outlet_id}-YYYYMMDD-{sequence}
     */
    private function generateTransactionCode($outletId)
    {
        $date = date('Ymd');
        $prefix = sprintf('TRX-OUT%03d-%s-', $outletId, $date);

        // Get last transaction code for today
        $lastTransaction = $this->transactionModel
            ->where('outlet_id', $outletId)
            ->where('DATE(created_at)', date('Y-m-d'))
            ->orderBy('id', 'DESC')
            ->first();

        $sequence = 1;
        if ($lastTransaction) {
            // Extract sequence from last code
            $lastCode = $lastTransaction['transaction_code'];
            $parts = explode('-', $lastCode);
            if (count($parts) >= 4) {
                $sequence = (int)end($parts) + 1;
            }
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
