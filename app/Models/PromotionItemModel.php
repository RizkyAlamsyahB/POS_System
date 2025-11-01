<?php

namespace App\Models;

use CodeIgniter\Model;

class PromotionItemModel extends Model
{
    protected $table            = 'promotion_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'promotion_id',
        'product_id',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'promotion_id' => 'required|integer',
        'product_id'   => 'required|integer',
    ];

    protected $validationMessages = [
        'promotion_id' => [
            'required' => 'ID Promosi harus diisi',
        ],
        'product_id' => [
            'required' => 'ID Produk harus diisi',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get items for specific promotion
     */
    public function getItemsByPromotion($promotionId)
    {
        return $this->select('promotion_items.*, products.name as product_name, products.sku, products.price')
                    ->join('products', 'products.id = promotion_items.product_id')
                    ->where('promotion_items.promotion_id', $promotionId)
                    ->findAll();
    }

    /**
     * Delete all items for specific promotion
     */
    public function deleteByPromotion($promotionId)
    {
        return $this->where('promotion_id', $promotionId)->delete();
    }

    /**
     * Check if product already in promotion
     */
    public function isProductInPromotion($promotionId, $productId)
    {
        return $this->where('promotion_id', $promotionId)
                    ->where('product_id', $productId)
                    ->countAllResults() > 0;
    }

    /**
     * Add multiple products to promotion
     */
    public function addProducts($promotionId, array $productIds)
    {
        $data = [];
        foreach ($productIds as $productId) {
            // Skip if already exists
            if (!$this->isProductInPromotion($promotionId, $productId)) {
                $data[] = [
                    'promotion_id' => $promotionId,
                    'product_id'   => $productId,
                ];
            }
        }

        if (!empty($data)) {
            return $this->insertBatch($data);
        }

        return true;
    }

    /**
     * Remove product from promotion
     */
    public function removeProduct($promotionId, $productId)
    {
        return $this->where('promotion_id', $promotionId)
                    ->where('product_id', $productId)
                    ->delete();
    }
}
