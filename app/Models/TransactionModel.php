<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table            = 'transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'transaction_code',
        'outlet_id',
        'user_id',
        'order_type',
        'table_number',
        'customer_name',
        'total_amount',
        'total_discount',
        'subtotal_before_tax',
        'total_tax',
        'total_tax_included',
        'grand_total',
        'payment_method',
        'cash_amount',
        'change_amount',
        'payment_status',
        'notes',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'outlet_id'      => 'required|integer',
        'user_id'        => 'required|integer',
        'grand_total'    => 'required|decimal',
        'payment_method' => 'required|in_list[cash,debit,credit,ewallet]',
        'payment_status' => 'permit_empty|in_list[paid,void,pending]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateTransactionCode'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Generate unique transaction code before insert
     */
    protected function generateTransactionCode(array $data)
    {
        if (!isset($data['data']['transaction_code'])) {
            $outletId = $data['data']['outlet_id'] ?? 0;
            $outlet = model('OutletModel')->find($outletId);
            $outletCode = $outlet['code'] ?? 'XXX';
            
            $date = date('Ymd');
            
            // Get last transaction number for today
            $lastTransaction = $this->where('DATE(created_at)', date('Y-m-d'))
                                    ->where('outlet_id', $outletId)
                                    ->orderBy('id', 'DESC')
                                    ->first();
            
            $sequence = 1;
            if ($lastTransaction) {
                // Extract sequence from last transaction code
                $parts = explode('-', $lastTransaction['transaction_code']);
                if (count($parts) == 4) {
                    $sequence = intval($parts[3]) + 1;
                }
            }
            
            $data['data']['transaction_code'] = sprintf('TRX-%s-%s-%04d', $outletCode, $date, $sequence);
        }
        
        return $data;
    }

    /**
     * Get transaction with details
     */
    public function getTransactionWithDetails($id)
    {
        $transaction = $this->find($id);
        if (!$transaction) {
            return null;
        }

        $detailModel = model('TransactionDetailModel');
        $details = $detailModel->select('transaction_details.*, products.name as product_name, products.sku')
                               ->join('products', 'products.id = transaction_details.product_id')
                               ->where('transaction_id', $id)
                               ->findAll();

        $transaction['details'] = $details;
        return $transaction;
    }

    /**
     * Get transactions for specific outlet with pagination
     */
    public function getTransactionsByOutlet($outletId, $limit = 10, $offset = 0, $search = '')
    {
        $builder = $this->select('transactions.*, users.username as cashier_name')
                        ->join('users', 'users.id = transactions.user_id')
                        ->where('transactions.outlet_id', $outletId);

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('transaction_code', $search)
                    ->orLike('users.username', $search)
                    ->groupEnd();
        }

        return $builder->orderBy('transactions.created_at', 'DESC')
                       ->findAll($limit, $offset);
    }

    /**
     * Get sales summary for dashboard
     */
    public function getSalesSummary($outletId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('
            COUNT(*) as total_transactions,
            SUM(grand_total) as total_sales,
            AVG(grand_total) as average_transaction
        ')->where('outlet_id', $outletId)
          ->where('payment_status', 'paid');

        if ($startDate) {
            $builder->where('DATE(created_at) >=', $startDate);
        }

        if ($endDate) {
            $builder->where('DATE(created_at) <=', $endDate);
        }

        return $builder->first();
    }
}
