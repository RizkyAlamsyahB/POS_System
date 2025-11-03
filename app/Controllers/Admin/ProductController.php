<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ProductStockModel;
use App\Models\OutletModel;
use App\Libraries\PusherService;

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
     * Display product list
     */
    public function index()
    {
        $data = [
            'title'    => 'Master Produk',
            'user'     => auth()->user(),
        ];

        return view('admin/products/index', $data);
    }

    /**
     * DataTable server-side endpoint
     */
    public function datatable()
    {
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

        // Column mapping (match with DataTable columns)
        $columns = [
            0 => 'products.id',           // No (we'll use this for row number)
            1 => 'products.sku',
            2 => 'products.barcode',
            3 => 'products.name',
            4 => 'categories.name',
            5 => 'products.price',
            6 => 'products.cost_price',
            7 => 'total_stock',           // Total stock
            8 => 'products.id'            // Actions (not orderable)
        ];
        
        $orderBy = isset($columns[$orderCol]) ? $columns[$orderCol] : 'products.name';
        $orderDir = ($orderDir === 'desc') ? 'DESC' : 'ASC';

        // Build query - Simplified without aggregation to avoid GROUP BY issues
        $db = \Config\Database::connect();
        $builder = $db->table('products');
        $builder->select('products.*, categories.name as category_name')
                ->join('categories', 'categories.id = products.category_id', 'left');

        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                ->like('products.name', $search)
                ->orLike('products.sku', $search)
                ->orLike('products.barcode', $search)
                ->orLike('categories.name', $search)
            ->groupEnd();
        }

        // Get total records (before filtering)
        $totalRecords = $this->productModel->withDeleted()->countAllResults(false);
        
        // Get filtered records count (need to clone builder for count)
        $builderCount = clone $builder;
        $filteredRecords = $builderCount->countAllResults(false);

        // Apply ordering and pagination
        $products = $builder->orderBy($orderBy, $orderDir)
                           ->limit($length, $start)
                           ->get()
                           ->getResultArray();

        // Get stock details per outlet for each product
        $productIds = array_column($products, 'id');
        $stockDetails = [];
        $stockTotals = [];
        
        if (!empty($productIds)) {
            $stockBuilder = $db->table('product_stocks');
            $stocks = $stockBuilder->select('product_stocks.product_id, product_stocks.stock, outlets.name as outlet_name')
                                   ->join('outlets', 'outlets.id = product_stocks.outlet_id', 'left')
                                   ->whereIn('product_stocks.product_id', $productIds)
                                   ->get()
                                   ->getResultArray();
            
            foreach ($stocks as $stock) {
                $stockDetails[$stock['product_id']][] = [
                    'outlet' => $stock['outlet_name'],
                    'stock' => $stock['stock']
                ];
                
                // Calculate total stock per product
                if (!isset($stockTotals[$stock['product_id']])) {
                    $stockTotals[$stock['product_id']] = 0;
                }
                $stockTotals[$stock['product_id']] += (int)$stock['stock'];
            }
        }

        // Format data for DataTable
        $data = [];
        foreach ($products as $index => $product) {
            $totalStock = $stockTotals[$product['id']] ?? 0;
            
            // Build stock info HTML
            $stockHtml = '<strong>' . number_format($totalStock, 0, ',', '.') . '</strong>';
            
            if (isset($stockDetails[$product['id']])) {
                $stockHtml .= '<br><small class="text-muted">';
                $stockItems = [];
                foreach ($stockDetails[$product['id']] as $detail) {
                    $stockItems[] = $detail['outlet'] . ': ' . $detail['stock'];
                }
                $stockHtml .= implode('<br>', $stockItems);
                $stockHtml .= '</small>';
            } else {
                $stockHtml .= '<br><small class="text-warning">Belum ada stok</small>';
            }
            
            // Product name with deleted badge
            $productName = '<strong>' . esc($product['name']) . '</strong>';
            if ($product['deleted_at']) {
                $productName .= ' <span class="badge bg-danger ms-2">Dihapus</span>';
            }
            
            $data[] = [
                $start + $index + 1,                                           // No
                '<code>' . esc($product['sku']) . '</code>',                   // SKU
                '<code>' . esc($product['barcode']) . '</code>',              // Barcode
                $productName,                                                  // Nama Produk + badge
                esc($product['category_name'] ?? '-'),                        // Kategori
                'Rp ' . number_format($product['price'], 0, ',', '.'),       // Harga Jual
                'Rp ' . number_format($product['cost_price'], 0, ',', '.'),  // HPP
                $stockHtml,                                                    // Stok Total + Detail per outlet
                view('admin/products/_actions', ['product' => $product]),     // Actions
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
     * Show create product form
     */
    public function create()
    {
        $data = [
            'title'      => 'Tambah Produk',
            'user'       => auth()->user(),
            'categories' => $this->categoryModel->findAll(),
        ];

        return view('admin/products/create', $data);
    }

    /**
     * Store new product
     */
    public function store()
    {
        if (!$this->validate($this->productModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle image upload
        $imagePath = null;
        $imageFile = $this->request->getFile('image');
        
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $newName = $imageFile->getRandomName();
            // Simpan di public/uploads/products agar bisa diakses via web
            $imageFile->move(FCPATH . 'uploads/products', $newName);
            $imagePath = 'uploads/products/' . $newName;
        }

        $data = [
            'category_id'  => $this->request->getPost('category_id'),
            'sku'          => strtoupper($this->request->getPost('sku')),
            'barcode'      => $this->request->getPost('barcode'),
            'barcode_alt'  => $this->request->getPost('barcode_alt'),
            'name'         => $this->request->getPost('name'),
            'unit'         => $this->request->getPost('unit'),
            'price'        => $this->request->getPost('price'),
            'cost_price'   => $this->request->getPost('cost_price'),
            'tax_type'     => $this->request->getPost('tax_type'),
            'tax_rate'     => $this->request->getPost('tax_rate') ?? 0,
            'tax_included' => $this->request->getPost('tax_included') ? 1 : 0,
            'image'        => $imagePath,
        ];

        if ($productId = $this->productModel->insert($data)) {
            return redirect()->to('/admin/products')->with('message', 'Produk berhasil ditambahkan!');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menambahkan produk!');
    }

    /**
     * Show edit product form
     */
    public function edit($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/admin/products')->with('error', 'Produk tidak ditemukan!');
        }

        $data = [
            'title'      => 'Edit Produk',
            'user'       => auth()->user(),
            'product'    => $product,
            'categories' => $this->categoryModel->findAll(),
        ];

        return view('admin/products/edit', $data);
    }

    /**
     * Update product
     */
    public function update($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/admin/products')->with('error', 'Produk tidak ditemukan!');
        }

        // CRITICAL: Transform SKU ke uppercase SEBELUM validasi
        $postData = $this->request->getPost();
        if (isset($postData['sku'])) {
            $postData['sku'] = strtoupper($postData['sku']);
            $_POST['sku'] = $postData['sku'];
        }

        // Validation rules dengan ignore parameter untuk is_unique
        $rules = [
            'category_id' => 'required|numeric',
            'sku'         => "required|max_length[50]|is_unique[products.sku,id,{$id}]",
            'barcode'     => "required|max_length[100]|is_unique[products.barcode,id,{$id}]",
            'name'        => 'required|max_length[100]',
            'unit'        => 'required|max_length[10]',
            'price'       => 'required|decimal',
            'cost_price'  => 'required|decimal',
        ];

        $messages = [
            'sku' => [
                'required'  => 'SKU harus diisi',
                'is_unique' => 'SKU sudah digunakan produk lain',
            ],
            'barcode' => [
                'required'  => 'Barcode harus diisi',
                'is_unique' => 'Barcode sudah digunakan produk lain',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle image upload
        $imagePath = $product['image'];
        $imageFile = $this->request->getFile('image');
        
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            // Delete old image if exists
            if ($product['image'] && file_exists(FCPATH . $product['image'])) {
                unlink(FCPATH . $product['image']);
            }
            
            $newName = $imageFile->getRandomName();
            $imageFile->move(FCPATH . 'uploads/products', $newName);
            $imagePath = 'uploads/products/' . $newName;
        } elseif ($this->request->getPost('delete_image')) {
            // Handle delete image checkbox
            if ($product['image'] && file_exists(FCPATH . $product['image'])) {
                unlink(FCPATH . $product['image']);
            }
            $imagePath = null;
        }
        
        $data = [
            'category_id'  => $this->request->getPost('category_id'),
            'sku'          => strtoupper($this->request->getPost('sku')),
            'barcode'      => $this->request->getPost('barcode'),
            'barcode_alt'  => $this->request->getPost('barcode_alt'),
            'name'         => $this->request->getPost('name'),
            'unit'         => $this->request->getPost('unit'),
            'price'        => $this->request->getPost('price'),
            'cost_price'   => $this->request->getPost('cost_price'),
            'tax_type'     => $this->request->getPost('tax_type'),
            'tax_rate'     => $this->request->getPost('tax_rate') ?? 0,
            'tax_included' => $this->request->getPost('tax_included') ? 1 : 0,
            'image'        => $imagePath,
        ];

        // Skip Model validation karena sudah divalidasi di atas dengan custom rules
        $this->productModel->skipValidation(true);
        
        if ($this->productModel->update($id, $data)) {
            return redirect()->to('/admin/products')->with('message', 'Produk berhasil diupdate!');
        }

        log_message('error', 'Failed to update product ID: ' . $id);
        log_message('error', 'Model errors: ' . json_encode($this->productModel->errors()));
        
        return redirect()->back()->withInput()->with('error', 'Gagal mengupdate produk!');
    }
    /**
     * Delete product (soft delete)
     */
    public function delete($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/admin/products')->with('error', 'Produk tidak ditemukan!');
        }

        // IMPORTANT: Soft delete - file gambar tetap disimpan
        // Produk bisa dipulihkan kembali dengan tombol restore
        
        if ($this->productModel->delete($id)) {
            return redirect()->to('/admin/products')->with('message', 'Produk berhasil dihapus!');
        }

        return redirect()->to('/admin/products')->with('error', 'Gagal menghapus produk!');
    }

    /**
     * Manage product stock
     */
    public function stock($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/admin/products')->with('error', 'Produk tidak ditemukan!');
        }

        $data = [
            'title'   => 'Kelola Stok - ' . $product['name'],
            'user'    => auth()->user(),
            'product' => $product,
            'stocks'  => $this->stockModel->getStocksByProduct($id),
            'outlets' => $this->outletModel->where('is_active', 1)->findAll(),
        ];

        return view('admin/products/stock', $data);
    }

    /**
     * Update stock for product
     */
    public function updateStock($productId)
    {
        $outletId = $this->request->getPost('outlet_id');
        $stock = $this->request->getPost('stock');

        if ($this->stockModel->setStock($productId, $outletId, $stock)) {
            // Broadcast stock update via Pusher
            $product = $this->productModel->find($productId);
            if ($product) {
                $pusher = new PusherService();
                $pusher->broadcastStockUpdate(
                    $outletId,
                    $productId,
                    (int)$stock,
                    $product['name']
                );
            }

            return redirect()->back()->with('message', 'Stok berhasil diupdate!');
        }

        return redirect()->back()->with('error', 'Gagal mengupdate stok!');
    }

    /**
     * Restore soft deleted product
     */
    public function restore($id)
    {
        $product = $this->productModel->withDeleted()->find($id);

        if (!$product) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan!');
        }

        // Check if product is actually deleted
        if (!$product['deleted_at']) {
            return redirect()->back()->with('error', 'Produk tidak dalam status dihapus!');
        }

        // Use Query Builder directly to bypass soft delete filter
        $db = \Config\Database::connect();
        $builder = $db->table('products');
        
        if ($builder->where('id', $id)->update(['deleted_at' => null])) {
            return redirect()->back()->with('message', 'Produk berhasil dipulihkan!');
        }

        return redirect()->back()->with('error', 'Gagal memulihkan produk!');
    }
}
