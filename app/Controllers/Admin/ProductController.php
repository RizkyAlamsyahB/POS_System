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
            'categories' => $this->categoryModel->getActiveCategories(),
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
            $imageFile->move(WRITEPATH . 'uploads/products', $newName);
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
            'is_active'    => $this->request->getPost('is_active') ? 1 : 0,
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
            'categories' => $this->categoryModel->getActiveCategories(),
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

        if (!$this->validate($this->productModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle image upload
        $imagePath = $product['image'];
        $imageFile = $this->request->getFile('image');
        
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            // Delete old image
            if ($product['image'] && file_exists(WRITEPATH . $product['image'])) {
                unlink(WRITEPATH . $product['image']);
            }
            
            $newName = $imageFile->getRandomName();
            $imageFile->move(WRITEPATH . 'uploads/products', $newName);
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
            'is_active'    => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if ($this->productModel->update($id, $data)) {
            return redirect()->to('/admin/products')->with('message', 'Produk berhasil diupdate!');
        }

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

        // Delete image
        if ($product['image'] && file_exists(WRITEPATH . $product['image'])) {
            unlink(WRITEPATH . $product['image']);
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
