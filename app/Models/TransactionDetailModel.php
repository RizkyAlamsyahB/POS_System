<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionDetailModel extends Model
{
    protected $table            = 'transaction_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'transaction_id',
        'product_id',
        'qty',
        'price',
        'cost_price',
        'discount',
        'discount_note',
        'tax_type',
        'tax_rate',
        'tax_amount',
        'subtotal',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'transaction_id' => 'required|integer',
        'product_id'     => 'required|integer',
        'qty'            => 'required|integer|greater_than[0]',
        'price'          => 'required|decimal',
        'subtotal'       => 'required|decimal',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get transaction details with product info
     */
    public function getTransactionDetails($transactionId)
    {
        return $this->select('transaction_details.*, products.name as product_name, products.sku')
                    ->join('products', 'products.id = transaction_details.product_id')
                    ->where('transaction_id', $transactionId)
                    ->findAll();
    }
}
