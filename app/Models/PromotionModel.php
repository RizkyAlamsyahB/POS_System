<?php

namespace App\Models;

use CodeIgniter\Model;

class PromotionModel extends Model
{
    protected $table            = 'promotions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'min_purchase',
        'max_discount',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'outlet_id',
        'is_active',
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
        'code'           => 'required|max_length[50]|is_unique[promotions.code,id,{id}]',
        'name'           => 'required|max_length[100]',
        'discount_type'  => 'required|in_list[percentage,fixed_amount]',
        'discount_value' => 'required|decimal|greater_than[0]',
        'start_date'     => 'required|valid_date',
        'end_date'       => 'required|valid_date',
    ];

    protected $validationMessages = [
        'code' => [
            'required'   => 'Kode promo harus diisi',
            'is_unique'  => 'Kode promo sudah digunakan',
        ],
        'name' => [
            'required'   => 'Nama promo harus diisi',
        ],
        'discount_type' => [
            'required'   => 'Tipe diskon harus dipilih',
            'in_list'    => 'Tipe diskon tidak valid',
        ],
        'discount_value' => [
            'required'      => 'Nilai diskon harus diisi',
            'greater_than'  => 'Nilai diskon harus lebih dari 0',
        ],
        'start_date' => [
            'required'   => 'Tanggal mulai harus diisi',
        ],
        'end_date' => [
            'required'   => 'Tanggal selesai harus diisi',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get promotions with outlet info
     */
    public function getPromotionsWithOutlet()
    {
        return $this->select('promotions.*, outlets.name as outlet_name, outlets.code as outlet_code')
                    ->join('outlets', 'outlets.id = promotions.outlet_id', 'left')
                    ->orderBy('promotions.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get active promotions for specific outlet and current date/time
     */
    public function getActivePromotions($outletId = null)
    {
        $builder = $this->where('is_active', 1)
                        ->where('start_date <=', date('Y-m-d'))
                        ->where('end_date >=', date('Y-m-d'));

        // Filter by outlet
        if ($outletId) {
            $builder->groupStart()
                    ->where('outlet_id', $outletId)
                    ->orWhere('outlet_id', null)
                    ->groupEnd();
        } else {
            $builder->where('outlet_id', null);
        }

        return $builder->findAll();
    }

    /**
     * Get promotion with items
     */
    public function getPromotionWithItems($id)
    {
        $promotion = $this->select('promotions.*, outlets.name as outlet_name')
                          ->join('outlets', 'outlets.id = promotions.outlet_id', 'left')
                          ->find($id);

        if (!$promotion) {
            return null;
        }

        // Get promotion items
        $db = \Config\Database::connect();
        $promotion['items'] = $db->table('promotion_items')
                                ->select('promotion_items.*, products.name as product_name, products.sku, products.price')
                                ->join('products', 'products.id = promotion_items.product_id')
                                ->where('promotion_items.promotion_id', $id)
                                ->get()
                                ->getResultArray();

        return $promotion;
    }

    /**
     * Check if promotion code exists (for AJAX validation)
     */
    public function isCodeUnique($code, $excludeId = null)
    {
        $builder = $this->where('code', $code);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() === 0;
    }

    /**
     * Get validation rules for create/update
     */
    public function getRulesForUpdate($id = null)
    {
        $rules = $this->validationRules;
        
        if ($id) {
            $rules['code'] = "required|max_length[50]|is_unique[promotions.code,id,{$id}]";
        }
        
        return $rules;
    }
}
