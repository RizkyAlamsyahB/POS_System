<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'description', 'is_active'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|max_length[100]|is_unique[categories.name,id,{id}]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Nama kategori harus diisi',
            'max_length' => 'Nama kategori maksimal 100 karakter',
            'is_unique'  => 'Nama kategori sudah digunakan',
        ],
    ];

    /**
     * Get all active categories
     */
    public function getActiveCategories()
    {
        return $this->where('is_active', 1)->findAll();
    }

    /**
     * Get category with product count
     */
    public function getCategoriesWithProductCount()
    {
        return $this->select('categories.*, COUNT(products.id) as product_count')
            ->join('products', 'products.category_id = categories.id', 'left')
            ->groupBy('categories.id')
            ->findAll();
    }

    /**
     * Check if category has products
     */
    public function hasProducts(int $categoryId): bool
    {
        $db = \Config\Database::connect();
        $result = $db->table('products')
            ->where('category_id', $categoryId)
            ->countAllResults();
        
        return $result > 0;
    }
}
