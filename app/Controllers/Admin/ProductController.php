<?php

namespace App\Controllers\Admin;

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
     * Display product list
     */
    public function index()
    {
        $data = [
            'title'    => 'Master Produk',
            'user'     => auth()->user(),
            'products' => $this->productModel->getProductsWithCategory(),
        ];

        return view('admin/products/index', $data);
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
     * Delete product
     */
    public function delete($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/admin/products')->with('error', 'Produk tidak ditemukan!');
        }

        // Delete image file if exists
        if ($product['image'] && file_exists(FCPATH . $product['image'])) {
            unlink(FCPATH . $product['image']);
        }

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
            return redirect()->back()->with('message', 'Stok berhasil diupdate!');
        }

        return redirect()->back()->with('error', 'Gagal mengupdate stok!');
    }
}
