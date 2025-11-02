<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductStockModel extends Model
{
    protected $table = 'product_stocks';
    protected $primaryKey = 'id';
    protected $allowedFields = ['product_id', 'outlet_id', 'stock'];
    protected $useTimestamps = false;
    protected $updatedField = 'updated_at';

    /**
     * Get stock for specific product and outlet
     */
    public function getStock(int $productId, int $outletId): int
    {
        $stock = $this->where('product_id', $productId)
            ->where('outlet_id', $outletId)
            ->first();
        
        return $stock ? (int) $stock['stock'] : 0;
    }

    /**
     * Update stock (add or reduce)
     */
    public function updateStock(int $productId, int $outletId, int $quantity): bool
    {
        $existing = $this->where('product_id', $productId)
            ->where('outlet_id', $outletId)
            ->first();

        if ($existing) {
            return $this->update($existing['id'], [
                'stock'      => $existing['stock'] + $quantity,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            return $this->insert([
                'product_id' => $productId,
                'outlet_id'  => $outletId,
                'stock'      => $quantity,
                'updated_at' => date('Y-m-d H:i:s'),
            ]) !== false;
        }
    }

    /**
     * Set stock (overwrite existing)
     */
    public function setStock(int $productId, int $outletId, int $stock): bool
    {
        $existing = $this->where('product_id', $productId)
            ->where('outlet_id', $outletId)
            ->first();

        if ($existing) {
            return $this->update($existing['id'], [
                'stock'      => $stock,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            return $this->insert([
                'product_id' => $productId,
                'outlet_id'  => $outletId,
                'stock'      => $stock,
                'updated_at' => date('Y-m-d H:i:s'),
            ]) !== false;
        }
    }

    /**
     * Get all stocks for a product
     */
    public function getStocksByProduct(int $productId)
    {
        return $this->select('product_stocks.*, outlets.name as outlet_name, outlets.code as outlet_code')
            ->join('outlets', 'outlets.id = product_stocks.outlet_id')
            ->where('product_id', $productId)
            ->findAll();
    }

    /**
     * Get all stocks for an outlet
     */
    public function getStocksByOutlet(int $outletId)
    {
        return $this->select('product_stocks.*, products.name as product_name, products.sku, products.barcode')
            ->join('products', 'products.id = product_stocks.product_id')
            ->where('outlet_id', $outletId)
            ->findAll();
    }

    /**
     * Get low stock products for an outlet
     */
    public function getLowStockProducts(int $outletId, int $threshold = 10)
    {
        return $this->select('product_stocks.*, products.name as product_name, products.sku')
            ->join('products', 'products.id = product_stocks.product_id')
            ->where('outlet_id', $outletId)
            ->where('stock <=', $threshold)
            ->findAll();
    }
}
