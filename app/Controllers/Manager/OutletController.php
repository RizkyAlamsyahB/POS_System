<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\OutletModel;
use App\Models\UserModel;

class OutletController extends BaseController
{
    protected $outletModel;
    protected $userModel;

    public function __construct()
    {
        $this->outletModel = new OutletModel();
        $this->userModel = new UserModel();
    }

    /**
     * View own outlet details (Manager can only view their assigned outlet)
     */
    public function index()
    {
        $user = auth()->user();
        $outletId = $this->userModel->getUserOutletId($user->id);

        if (!$outletId) {
            return redirect()->to('/manager/dashboard')->with('error', 'Anda tidak memiliki outlet yang ditugaskan!');
        }

        $outlet = $this->outletModel->find($outletId);

        if (!$outlet) {
            return redirect()->to('/manager/dashboard')->with('error', 'Outlet tidak ditemukan!');
        }

        // Get users in same outlet
        $users = $this->userModel->where('outlet_id', $outletId)->findAll();

        // Get stock summary for this outlet
        $db = \Config\Database::connect();
        $stockSummary = $db->table('product_stocks')
            ->select('COUNT(*) as total_products, SUM(stock) as total_stock')
            ->where('outlet_id', $outletId)
            ->get()
            ->getRowArray();

        // Get low stock products (stock <= 10)
        $lowStockProducts = $db->table('product_stocks')
            ->select('product_stocks.*, products.name as product_name, products.sku')
            ->join('products', 'products.id = product_stocks.product_id')
            ->where('product_stocks.outlet_id', $outletId)
            ->where('product_stocks.stock <=', 10)
            ->get()
            ->getResultArray();

        $data = [
            'title'            => 'Outlet Saya',
            'user'             => $user,
            'outlet'           => $outlet,
            'users'            => $users,
            'stockSummary'     => $stockSummary,
            'lowStockProducts' => $lowStockProducts,
        ];

        return view('manager/outlets/index', $data);
    }
}
