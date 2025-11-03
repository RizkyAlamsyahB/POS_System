<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OutletModel;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ProductStockModel;
use App\Models\PromotionModel;
use App\Models\PromotionItemModel;

class DashboardController extends BaseController
{
    /**
     * Admin Dashboard
     */
    public function adminDashboard()
    {
        $db = \Config\Database::connect();
        
        // Count total outlets
        $outletModel = new OutletModel();
        $totalOutlets = $outletModel->countAll();
        
        // Count total users
        $totalUsers = $db->table('users')->countAll();
        
        // Count total products
        $productModel = new ProductModel();
        $totalProducts = $productModel->countAll();
        
        // Today's sales
        $todaySales = $db->table('transactions')
            ->select('SUM(grand_total) as total')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->where('payment_status', 'paid')
            ->get()
            ->getRow()
            ->total ?? 0;
        
        // Recent transactions (last 10)
        $recentTransactions = $db->table('transactions t')
            ->select('t.id, t.transaction_code, t.grand_total, t.created_at, o.name as outlet_name, u.username as cashier_name')
            ->join('outlets o', 'o.id = t.outlet_id', 'left')
            ->join('users u', 'u.id = t.user_id', 'left')
            ->where('t.payment_status', 'paid')
            ->orderBy('t.created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();
        
        $data = [
            'title' => 'Admin Dashboard',
            'user'  => auth()->user(),
            'totalOutlets' => $totalOutlets,
            'totalUsers' => $totalUsers,
            'totalProducts' => $totalProducts,
            'todaySales' => $todaySales,
            'recentTransactions' => $recentTransactions,
        ];

        return view('dashboard/admin-mazer', $data);
    }

    /**
     * Manager Dashboard
     */
    public function managerDashboard()
    {
        $user = auth()->user();
        $outletStatus = $this->checkOutletStatus($user);
        
        $db = \Config\Database::connect();
        $outletId = $user->outlet_id;
        
        // Today's sales for this outlet
        $todaySales = $db->table('transactions')
            ->select('SUM(grand_total) as total, COUNT(id) as count')
            ->where('outlet_id', $outletId)
            ->where('DATE(created_at)', date('Y-m-d'))
            ->where('payment_status', 'paid')
            ->get()
            ->getRow();
        
        // Total products with stock in this outlet
        $totalProducts = $db->table('product_stocks')
            ->where('outlet_id', $outletId)
            ->countAllResults();
        
        // Products with low stock (stock < 10)
        $lowStock = $db->table('product_stocks')
            ->where('outlet_id', $outletId)
            ->where('stock <', 10)
            ->countAllResults();
        
        // Recent transactions (last 10 for this outlet)
        $recentTransactions = $db->table('transactions t')
            ->select('t.id, t.transaction_code, t.grand_total, t.created_at, t.customer_name, u.username as cashier_name')
            ->join('users u', 'u.id = t.user_id', 'left')
            ->where('t.outlet_id', $outletId)
            ->where('t.payment_status', 'paid')
            ->orderBy('t.created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();
        
        $data = [
            'title' => 'Manager Dashboard',
            'user'  => $user,
            'outlet' => $outletStatus['outlet'],
            'outletInactive' => !$outletStatus['is_active'],
            'todaySales' => $todaySales->total ?? 0,
            'todayTransactions' => $todaySales->count ?? 0,
            'totalProducts' => $totalProducts,
            'lowStock' => $lowStock,
            'recentTransactions' => $recentTransactions,
        ];

        return view('dashboard/manager', $data);
    }

    /**
     * Cashier/POS Interface
     */
    public function pos()
    {
        $user = auth()->user();
        $outletStatus = $this->checkOutletStatus($user);
        
        // Block POS access if outlet is inactive
        if (!$outletStatus['is_active'] && !$user->inGroup('admin')) {
            return redirect()->back()->with('error', 'Outlet Anda sedang nonaktif. Hubungi administrator untuk mengaktifkan kembali.');
        }
        
        // Get products with stock for this outlet
        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();
        $stockModel = new ProductStockModel();
        $outletId = $user->outlet_id;
        
        // Get all categories (only active/not deleted)
        $categories = $categoryModel->findAll();
        
        // Get all products with their stock (only active/not deleted products)
        $db = \Config\Database::connect();
        $sql = "SELECT p.*, c.name as category_name, COALESCE(ps.stock, 0) as stock 
                FROM products p
                LEFT JOIN categories c ON c.id = p.category_id AND c.deleted_at IS NULL
                LEFT JOIN product_stocks ps ON ps.product_id = p.id AND ps.outlet_id = ?
                WHERE p.deleted_at IS NULL";
        $query = $db->query($sql, [$outletId]);
        $products = $query->getResultArray();
        
        // Prepare categories with product count and icons
        $categoriesWithCount = [];
        foreach ($categories as $category) {
            $productCount = 0;
            foreach ($products as $product) {
                if ($product['category_id'] == $category['id']) {
                    $productCount++;
                }
            }
            
            // Icon mapping (sesuaikan dengan nama kategori di database)
            $categoryIcons = [
                // Bahasa Indonesia
                'Makanan' => 'bi-egg-fried',
                'Minuman' => 'bi-cup-straw',
                'Dessert' => 'bi-moon-stars-fill',
                'Tambahan' => 'bi-plus-circle',
                
                // English (jika ada)
                'Food' => 'bi-egg-fried',
                'Beverage' => 'bi-cup-straw',
                'Snack' => 'bi-basket',
                'Main Course' => 'bi-egg-fried',
                'Appetizer' => 'bi-stars',
                'Soup' => 'bi-cup-hot-fill',
            ];
            
            $categoriesWithCount[] = [
                'id' => $category['id'],
                'name' => $category['name'],
                'icon' => $categoryIcons[$category['name']] ?? 'bi-bookmark',
                'product_count' => $productCount,
            ];
        }
        
        // Get active promotions for this outlet
        $activePromotions = $this->getActivePromotions($outletId);
        
        $data = [
            'title'      => 'Point of Sale',
            'user'       => $user,
            'outlet'     => $outletStatus['outlet'],
            'categories' => $categoriesWithCount,
            'products'   => $products,
            'promotions' => $activePromotions,
        ];

        return view('pos/index', $data);
    }
    
    /**
     * Get active promotions for current outlet, date, and time
     */
    private function getActivePromotions($outletId)
    {
        $db = \Config\Database::connect();
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');
        
        // Get promotions that are active for this outlet and current date/time
        $sql = "SELECT p.*, 
                GROUP_CONCAT(pi.product_id) as product_ids
                FROM promotions p
                LEFT JOIN promotion_items pi ON pi.promotion_id = p.id
                WHERE p.is_active = 1
                AND p.start_date <= ?
                AND p.end_date >= ?
                AND (p.outlet_id IS NULL OR p.outlet_id = ?)
                AND (p.start_time IS NULL OR p.start_time <= ?)
                AND (p.end_time IS NULL OR p.end_time >= ?)
                GROUP BY p.id
                ORDER BY p.discount_value DESC";
        
        $query = $db->query($sql, [$currentDate, $currentDate, $outletId, $currentTime, $currentTime]);
        $promotions = $query->getResultArray();
        
        // Convert product_ids string to array
        foreach ($promotions as &$promo) {
            $promo['product_ids'] = $promo['product_ids'] ? explode(',', $promo['product_ids']) : [];
        }
        
        return $promotions;
    }
    
    /**
     * Helper: Check outlet status for current user
     */
    private function checkOutletStatus($user)
    {
        $outletModel = new OutletModel();
        $outlet = null;
        $isActive = true;
        
        if ($user->outlet_id) {
            $outlet = $outletModel->find($user->outlet_id);
            $isActive = $outlet ? (bool) $outlet['is_active'] : false;
        }
        
        return [
            'outlet' => $outlet,
            'is_active' => $isActive,
        ];
    }

    /**
     * Get transaction detail (Admin)
     */
    public function adminTransactionDetail($id)
    {
        $db = \Config\Database::connect();
        
        // Get transaction header
        $transaction = $db->table('transactions t')
            ->select('t.*, o.name as outlet_name, u.username as cashier_name')
            ->join('outlets o', 'o.id = t.outlet_id', 'left')
            ->join('users u', 'u.id = t.user_id', 'left')
            ->where('t.id', $id)
            ->get()
            ->getRowArray();
        
        if (!$transaction) {
            return $this->response->setJSON(['error' => 'Transaksi tidak ditemukan']);
        }
        
        // Get transaction items
        $items = $db->table('transaction_details td')
            ->select('td.*, p.name as product_name')
            ->join('products p', 'p.id = td.product_id', 'left')
            ->where('td.transaction_id', $id)
            ->get()
            ->getResultArray();
        
        return $this->response->setJSON([
            'transaction' => $transaction,
            'items' => $items
        ]);
    }

    /**
     * Get transaction detail (Manager)
     */
    public function managerTransactionDetail($id)
    {
        $user = auth()->user();
        $db = \Config\Database::connect();
        
        // Get transaction header - only from manager's outlet
        $transaction = $db->table('transactions t')
            ->select('t.*, u.username as cashier_name')
            ->join('users u', 'u.id = t.user_id', 'left')
            ->where('t.id', $id)
            ->where('t.outlet_id', $user->outlet_id)
            ->get()
            ->getRowArray();
        
        if (!$transaction) {
            return $this->response->setJSON(['error' => 'Transaksi tidak ditemukan atau bukan dari outlet Anda']);
        }
        
        // Get transaction items
        $items = $db->table('transaction_details td')
            ->select('td.*, p.name as product_name')
            ->join('products p', 'p.id = td.product_id', 'left')
            ->where('td.transaction_id', $id)
            ->get()
            ->getResultArray();
        
        return $this->response->setJSON([
            'transaction' => $transaction,
            'items' => $items
        ]);
    }

    /**
     * DataTable endpoint for Admin - Recent Transactions
     */
    public function adminTransactionsDatatable()
    {
        $request = $this->request;
        $db = \Config\Database::connect();
        
        // DataTable parameters
        $draw = $request->getGet('draw');
        $start = $request->getGet('start') ?? 0;
        $length = $request->getGet('length') ?? 10;
        $searchValue = $request->getGet('search')['value'] ?? '';
        $orderColumn = $request->getGet('order')[0]['column'] ?? 4; // default: created_at
        $orderDir = $request->getGet('order')[0]['dir'] ?? 'desc';
        
        // Column mapping
        $columns = [
            0 => 't.transaction_code',
            1 => 'o.name',
            2 => 'u.username',
            3 => 't.grand_total',
            4 => 't.created_at'
        ];
        
        // Base query
        $builder = $db->table('transactions t')
            ->select('t.id, t.transaction_code, t.grand_total, t.created_at, o.name as outlet_name, u.username as cashier_name')
            ->join('outlets o', 'o.id = t.outlet_id', 'left')
            ->join('users u', 'u.id = t.user_id', 'left')
            ->where('t.payment_status', 'paid');
        
        // Search filter
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('t.transaction_code', $searchValue)
                ->orLike('o.name', $searchValue)
                ->orLike('u.username', $searchValue)
                ->groupEnd();
        }
        
        // Total records (filtered)
        $recordsFiltered = $builder->countAllResults(false);
        
        // Total records (all)
        $recordsTotal = $db->table('transactions')
            ->where('payment_status', 'paid')
            ->countAllResults();
        
        // Apply ordering
        $orderColumnName = $columns[$orderColumn] ?? 't.created_at';
        $builder->orderBy($orderColumnName, $orderDir);
        
        // Apply pagination
        $builder->limit($length, $start);
        
        // Get data
        $data = $builder->get()->getResultArray();
        
        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

    /**
     * DataTable endpoint for Manager - Recent Transactions
     */
    public function managerTransactionsDatatable()
    {
        $request = $this->request;
        $user = auth()->user();
        $outletId = $user->outlet_id;
        $db = \Config\Database::connect();
        
        // DataTable parameters
        $draw = $request->getGet('draw');
        $start = $request->getGet('start') ?? 0;
        $length = $request->getGet('length') ?? 10;
        $searchValue = $request->getGet('search')['value'] ?? '';
        $orderColumn = $request->getGet('order')[0]['column'] ?? 4; // default: created_at
        $orderDir = $request->getGet('order')[0]['dir'] ?? 'desc';
        
        // Column mapping
        $columns = [
            0 => 't.transaction_code',
            1 => 'u.username',
            2 => 't.customer_name',
            3 => 't.grand_total',
            4 => 't.created_at'
        ];
        
        // Base query - only outlet transactions
        $builder = $db->table('transactions t')
            ->select('t.id, t.transaction_code, t.grand_total, t.created_at, t.customer_name, u.username as cashier_name')
            ->join('users u', 'u.id = t.user_id', 'left')
            ->where('t.outlet_id', $outletId)
            ->where('t.payment_status', 'paid');
        
        // Search filter
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('t.transaction_code', $searchValue)
                ->orLike('u.username', $searchValue)
                ->orLike('t.customer_name', $searchValue)
                ->groupEnd();
        }
        
        // Total records (filtered)
        $recordsFiltered = $builder->countAllResults(false);
        
        // Total records (all)
        $recordsTotal = $db->table('transactions')
            ->where('outlet_id', $outletId)
            ->where('payment_status', 'paid')
            ->countAllResults();
        
        // Apply ordering
        $orderColumnName = $columns[$orderColumn] ?? 't.created_at';
        $builder->orderBy($orderColumnName, $orderDir);
        
        // Apply pagination
        $builder->limit($length, $start);
        
        // Get data
        $data = $builder->get()->getResultArray();
        
        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }
}
