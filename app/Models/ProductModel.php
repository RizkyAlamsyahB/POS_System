<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'category_id',
        'sku',
        'barcode',
        'barcode_alt',
        'name',
        'unit',
        'price',
        'cost_price',
        'tax_type',
        'tax_rate',
        'tax_included',
        'image',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'category_id' => 'required|numeric',
        'sku'         => 'required|max_length[50]|is_unique[products.sku,id,{id}]',
        'barcode'     => 'required|max_length[100]|is_unique[products.barcode,id,{id}]',
        'name'        => 'required|max_length[100]',
        'unit'        => 'required|max_length[10]',
        'price'       => 'required|decimal',
        'cost_price'  => 'required|decimal',
    ];

    protected $validationMessages = [
        'category_id' => [
            'required' => 'Kategori harus dipilih',
            'numeric'  => 'Kategori tidak valid',
        ],
        'sku' => [
            'required'  => 'SKU harus diisi',
            'is_unique' => 'SKU sudah digunakan',
        ],
        'barcode' => [
            'required'  => 'Barcode harus diisi',
            'is_unique' => 'Barcode sudah digunakan',
        ],
        'name' => [
            'required' => 'Nama produk harus diisi',
        ],
        'price' => [
            'required' => 'Harga jual harus diisi',
        ],
        'cost_price' => [
            'required' => 'Harga pokok harus diisi',
        ],
    ];

    /**
     * Get products with category information
     */
    public function getProductsWithCategory()
    {
        return $this->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id')
            ->findAll();
    }

    /**
     * Get product with category and stock info
     */
    public function getProductWithDetails(int $productId, ?int $outletId = null)
    {
        $builder = $this->select('products.*, categories.name as category_name');
        
        if ($outletId !== null) {
            $builder->select('product_stocks.stock')
                ->join('product_stocks', 'product_stocks.product_id = products.id AND product_stocks.outlet_id = ' . $outletId, 'left');
        }
        
        return $builder->join('categories', 'categories.id = products.category_id')
            ->where('products.id', $productId)
            ->first();
    }

    /**
     * Get products by category
     */
    public function getProductsByCategory(int $categoryId)
    {
        return $this->where('category_id', $categoryId)
            ->findAll();
    }

    /**
     * Search products
     */
    public function searchProducts(string $keyword)
    {
        return $this->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id')
            ->groupStart()
                ->like('products.name', $keyword)
                ->orLike('products.sku', $keyword)
                ->orLike('products.barcode', $keyword)
                ->orLike('products.barcode_alt', $keyword)
            ->groupEnd()
            ->findAll();
    }

    /**
     * Get product by barcode
     */
    public function getProductByBarcode(string $barcode)
    {
        return $this->where('barcode', $barcode)
            ->orWhere('barcode_alt', $barcode)
            ->first();
    }
}
