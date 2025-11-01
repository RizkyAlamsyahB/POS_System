<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ProductStockModel;
use App\Models\UserModel;

class ProductController extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $stockModel;
    protected $userModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->stockModel = new ProductStockModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display product list (read-only)
     */
    public function index()
    {
        $user = auth()->user();
        $outletId = $this->userModel->getUserOutletId($user->id);

        $data = [
            'title'     => 'Daftar Produk',
            'user'      => $user,
            'products'  => $this->productModel->getProductsWithCategory(),
            'outlet_id' => $outletId,
        ];

        return view('manager/products/index', $data);
    }

    /**
     * Show product details (read-only)
     */
    public function view($id)
    {
        $user = auth()->user();
        $outletId = $this->userModel->getUserOutletId($user->id);

        $product = $this->productModel->getProductWithDetails($id, $outletId);

        if (!$product) {
            return redirect()->to('/manager/products')->with('error', 'Produk tidak ditemukan!');
        }

        $data = [
            'title'   => 'Detail Produk',
            'user'    => $user,
            'product' => $product,
        ];

        return view('manager/products/view', $data);
    }

    /**
     * View stock for manager's outlet (read-only)
     */
    public function stock()
    {
        $user = auth()->user();
        $outletId = $this->userModel->getUserOutletId($user->id);

        if (!$outletId) {
            return redirect()->to('/manager/dashboard')->with('error', 'Anda tidak memiliki outlet yang ditugaskan!');
        }

        $data = [
            'title'  => 'Stok Produk Outlet',
            'user'   => $user,
            'stocks' => $this->stockModel->getStocksByOutlet($outletId),
        ];

        return view('manager/products/stock', $data);
    }

    /**
     * Update stock for manager's outlet only
     */
    public function updateStock()
    {
        $user = auth()->user();
        $outletId = $this->userModel->getUserOutletId($user->id);

        if (!$outletId) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengupdate stok!');
        }

        $productId = $this->request->getPost('product_id');
        $stock = $this->request->getPost('stock');

        if ($this->stockModel->setStock($productId, $outletId, $stock)) {
            return redirect()->back()->with('message', 'Stok berhasil diupdate!');
        }

        return redirect()->back()->with('error', 'Gagal mengupdate stok!');
    }
}
