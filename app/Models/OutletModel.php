<?php

namespace App\Models;

use CodeIgniter\Model;

class OutletModel extends Model
{
    protected $table            = 'outlets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'code',
        'name',
        'address',
        'phone',
        'is_active',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'code' => 'required|min_length[3]|max_length[20]|is_unique[outlets.code,id,{id}]',
        'name' => 'required|min_length[3]|max_length[100]',
    ];

    protected $validationMessages = [
        'code' => [
            'required'   => 'Kode outlet harus diisi',
            'is_unique'  => 'Kode outlet sudah digunakan',
        ],
        'name' => [
            'required'   => 'Nama outlet harus diisi',
        ],
    ];

    /**
     * Get active outlets only
     */
    public function getActiveOutlets()
    {
        return $this->where('is_active', 1)->findAll();
    }

    /**
     * Generate next outlet code
     */
    public function generateCode(): string
    {
        $lastOutlet = $this->orderBy('id', 'DESC')->first();
        
        if (!$lastOutlet) {
            return 'OUT001';
        }

        // Extract number from last code (OUT001 -> 001)
        $lastNumber = (int) substr($lastOutlet->code, 3);
        $newNumber = $lastNumber + 1;

        return 'OUT' . str_pad((string) $newNumber, 3, '0', STR_PAD_LEFT);
    }
}
