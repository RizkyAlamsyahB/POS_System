<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ProductStockModel;
use App\Models\OutletModel;

class ProductController extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $stockModel;
    protected $outletModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->stockModel = new ProductStockModel();
        $this->outletModel = new OutletModel();
    }

    /**
     * Display product list with stock for manager's outlet
     */
    public function index()
    {
        $user = auth()->user();
        $outlet = null;
        
        if ($user->outlet_id) {
            $outlet = $this->outletModel->find($user->outlet_id);
        }

        $data = [
            'title'  => 'Kelola Stok Produk',
            'user'   => $user,
            'outlet' => $outlet,
        ];

        return view('manager/products/index', $data);
    }

    /**
     * DataTable server-side endpoint for products with stock
     */
    public function datatable()
    {
        $user = auth()->user();
        $outletId = $user->outlet_id;

        if (!$outletId) {
            return $this->response->setJSON([
                'draw' => 0,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Manager harus memiliki outlet yang ditugaskan'
            ]);
        }

        $request = $this->request;
        
        // Get DataTable parameters
        $draw = intval($request->getGet('draw') ?? 0);
        $start = intval($request->getGet('start') ?? 0);
        $length = intval($request->getGet('length') ?? 10);
        
        // Get search value
        $searchValue = $request->getGet('search');
        $search = is_array($searchValue) ? ($searchValue['value'] ?? '') : '';
        
        // Get order parameters
        $orderData = $request->getGet('order');
        $orderCol = 0;
        $orderDir = 'asc';
        
        if (is_array($orderData) && isset($orderData[0])) {
            $orderCol = intval($orderData[0]['column'] ?? 0);
            $orderDir = $orderData[0]['dir'] ?? 'asc';
        }

        // Column mapping
        $columns = [
            0 => 'products.id',
            1 => 'products.sku',
            2 => 'products.barcode',
            3 => 'products.name',
            4 => 'categories.name',
            5 => 'products.price',
            6 => 'stock',
            7 => 'products.id' // Actions
        ];
        
        $orderBy = isset($columns[$orderCol]) ? $columns[$orderCol] : 'products.name';
        $orderDir = ($orderDir === 'desc') ? 'DESC' : 'ASC';

        // Build query with stock for manager's outlet
        $db = \Config\Database::connect();
        $builder = $db->table('products');
        $builder->select('products.*, categories.name as category_name, COALESCE(product_stocks.stock, 0) as stock, product_stocks.id as stock_id')
                ->join('categories', 'categories.id = products.category_id', 'left')
                ->join('product_stocks', "product_stocks.product_id = products.id AND product_stocks.outlet_id = {$outletId}", 'left');

        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                ->like('products.name', $search)
                ->orLike('products.sku', $search)
                ->orLike('products.barcode', $search)
                ->orLike('categories.name', $search)
            ->groupEnd();
        }

        // Get total records
        $totalRecords = $this->productModel->countAll();
        
        // Get filtered records count
        $builderCount = clone $builder;
        $filteredRecords = $builderCount->countAllResults(false);

        // Apply ordering and pagination
        $products = $builder->orderBy($orderBy, $orderDir)
                           ->limit($length, $start)
                           ->get()
                           ->getResultArray();

        // Format data for DataTable
        $data = [];
        foreach ($products as $index => $product) {
            // Stock badge with color
            $stockValue = (int) $product['stock'];
            $stockBadge = '';
            
            if ($stockValue <= 0) {
                $stockBadge = '<span class="badge bg-danger">Habis</span>';
            } elseif ($stockValue < 10) {
                $stockBadge = '<span class="badge bg-warning">' . $stockValue . '</span>';
            } else {
                $stockBadge = '<span class="badge bg-success">' . $stockValue . '</span>';
            }
            
            $data[] = [
                $start + $index + 1,
                '<code>' . esc($product['sku']) . '</code>',
                '<code>' . esc($product['barcode']) . '</code>',
                '<strong>' . esc($product['name']) . '</strong>',
                esc($product['category_name'] ?? '-'),
                'Rp ' . number_format($product['price'], 0, ',', '.'),
                $stockBadge,
                view('manager/products/_actions', ['product' => $product]),
            ];
        }

        // Return JSON response
        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    /**
     * Update stock via AJAX
     */
    public function updateStock()
    {
        $user = auth()->user();
        $outletId = $user->outlet_id;

        if (!$outletId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Manager harus memiliki outlet yang ditugaskan!'
            ]);
        }

        $productId = $this->request->getPost('product_id');
        $product = $this->productModel->find($productId);

        if (!$product) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Produk tidak ditemukan!'
            ]);
        }

        // Validation
        $rules = [
            'stock' => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Stok harus berupa angka dan tidak boleh negatif!',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $newStock = (int) $this->request->getPost('stock');

        // Check if stock record exists
        $existingStock = $this->stockModel->where('product_id', $productId)
                                          ->where('outlet_id', $outletId)
                                          ->first();

        if ($existingStock) {
            // Update existing stock
            $result = $this->stockModel->update($existingStock['id'], [
                'stock' => $newStock
            ]);
        } else {
            // Create new stock record
            $result = $this->stockModel->insert([
                'product_id' => $productId,
                'outlet_id'  => $outletId,
                'stock'      => $newStock
            ]);
        }

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Stok berhasil diupdate!',
                'stock' => $newStock
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal mengupdate stok!'
        ]);
    }

    /**
     * View product details
     */
    public function view($id)
    {
        $user = auth()->user();
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan!');
        }

        // Get category
        $category = $this->categoryModel->find($product['category_id']);

        // Get stock for manager's outlet
        $stock = null;
        if ($user->outlet_id) {
            $stock = $this->stockModel->where('product_id', $id)
                                      ->where('outlet_id', $user->outlet_id)
                                      ->first();
        }

        $outlet = $user->outlet_id ? $this->outletModel->find($user->outlet_id) : null;

        $data = [
            'title'    => 'Detail Produk',
            'user'     => $user,
            'product'  => $product,
            'category' => $category,
            'stock'    => $stock,
            'outlet'   => $outlet,
        ];

        return view('manager/products/view', $data);
    }
}
