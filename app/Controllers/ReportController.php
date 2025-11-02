<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;
use App\Models\OutletModel;
use App\Models\ProductModel;
use App\Models\UserModel;

class ReportController extends BaseController
{
    protected $transactionModel;
    protected $transactionDetailModel;
    protected $outletModel;
    protected $productModel;
    protected $userModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
        $this->transactionDetailModel = new TransactionDetailModel();
        $this->outletModel = new OutletModel();
        $this->productModel = new ProductModel();
        $this->userModel = new UserModel();
    }

    /**
     * Admin Report - Summary semua outlet
     */
    public function adminSalesReport()
    {
        // Get filter type from query string
        $filterType = $this->request->getGet('filter') ?? 'custom';
        $selectedOutlet = $this->request->getGet('outlet_id') ?? null; // Filter outlet
        
        // Calculate date range based on filter type
        switch ($filterType) {
            case 'today':
                $startDate = date('Y-m-d');
                $endDate = date('Y-m-d');
                break;
            case 'week':
                // Senin sampai Minggu minggu ini
                $startDate = date('Y-m-d', strtotime('monday this week'));
                $endDate = date('Y-m-d', strtotime('sunday this week'));
                break;
            case 'month':
                // Tanggal 1 sampai akhir bulan ini
                $startDate = date('Y-m-01');
                $endDate = date('Y-m-t');
                break;
            case 'year':
                // 1 Januari sampai 31 Desember tahun ini
                $startDate = date('Y-01-01');
                $endDate = date('Y-12-31');
                break;
            default: // custom
                $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
                $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
                break;
        }
        
        // Query summary per outlet dengan profit calculation
        $db = \Config\Database::connect();
        $builder = $db->table('transactions t');
        $builder->select('
            t.outlet_id,
            o.name as outlet_name,
            o.code as outlet_code,
            COUNT(t.id) as total_transactions,
            SUM(t.grand_total) as total_sales,
            SUM(t.total_discount) as total_discounts,
            SUM(t.total_tax) as total_tax
        ');
        $builder->join('outlets o', 'o.id = t.outlet_id', 'left');
        $builder->where('t.payment_status', 'paid');
        $builder->where('DATE(t.created_at) >=', $startDate);
        $builder->where('DATE(t.created_at) <=', $endDate);
        $builder->groupBy('t.outlet_id, o.name, o.code');
        $builder->orderBy('total_sales', 'DESC');
        
        $outletSummary = $builder->get()->getResultArray();
        
        // Tambahkan profit calculation untuk setiap outlet
        foreach ($outletSummary as &$outlet) {
            $profitData = $this->calculateOutletProfit($outlet['outlet_id'], $startDate, $endDate);
            $outlet['total_revenue'] = $profitData['total_revenue'];
            $outlet['total_cost'] = $profitData['total_cost'];
            $outlet['gross_profit'] = $profitData['gross_profit'];
            $outlet['profit_margin'] = $profitData['profit_margin'];
        }
        unset($outlet); // Break reference
        
        // Hitung grand total semua outlet
        $grandTotal = array_sum(array_column($outletSummary, 'total_sales'));
        $grandTransactions = array_sum(array_column($outletSummary, 'total_transactions'));
        
        // Top selling products (across all outlets)
        $topProducts = $this->getTopSellingProducts($startDate, $endDate);
        
        // Get all outlets for dropdown
        $outlets = $this->outletModel->where('is_active', 1)->findAll();
        
        // Jika outlet dipilih, ambil detail transaksi outlet tersebut
        $outletTransactions = [];
        $outletInfo = null;
        $outletProfit = null;
        
        if ($selectedOutlet) {
            $outletInfo = $this->outletModel->find($selectedOutlet);
            
            // Get transactions for selected outlet
            $builder = $db->table('transactions t');
            $builder->select('
                t.id,
                t.transaction_code,
                t.created_at,
                t.grand_total,
                t.payment_method,
                t.customer_name,
                u.username as cashier_name
            ');
            $builder->join('users u', 'u.id = t.user_id', 'left');
            $builder->where('t.outlet_id', $selectedOutlet);
            $builder->where('t.payment_status', 'paid');
            $builder->where('DATE(t.created_at) >=', $startDate);
            $builder->where('DATE(t.created_at) <=', $endDate);
            $builder->orderBy('t.created_at', 'DESC');
            
            $outletTransactions = $builder->get()->getResultArray();
            
            // Calculate profit for selected outlet
            $outletProfit = $this->calculateOutletProfit($selectedOutlet, $startDate, $endDate);
        }
        
        $data = [
            'title' => 'Laporan Penjualan - Admin',
            'user' => auth()->user(),
            'outletSummary' => $outletSummary,
            'grandTotal' => $grandTotal,
            'grandTransactions' => $grandTransactions,
            'topProducts' => $topProducts,
            'outlets' => $outlets,
            'selectedOutlet' => $selectedOutlet,
            'outletInfo' => $outletInfo,
            'outletTransactions' => $outletTransactions,
            'outletProfit' => $outletProfit,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filterType' => $filterType,
        ];
        
        return view('admin/reports/sales', $data);
    }

    /**
     * DataTables AJAX untuk transaksi outlet (Admin)
     */
    public function adminTransactionsDatatable()
    {
        $request = $this->request;
        $outletId = $request->getGet('outlet_id');
        $startDate = $request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $request->getGet('end_date') ?? date('Y-m-d');
        
        $db = \Config\Database::connect();
        $builder = $db->table('transactions t');
        
        // Columns for ordering
        $columns = ['t.transaction_code', 't.created_at', 'u.username', 't.customer_name', 't.payment_method', 't.grand_total'];
        
        // Base query
        $builder->select('
            t.id,
            t.transaction_code,
            t.created_at,
            t.grand_total,
            t.payment_method,
            t.customer_name,
            u.username as cashier_name
        ');
        $builder->join('users u', 'u.id = t.user_id', 'left');
        $builder->where('t.outlet_id', $outletId);
        $builder->where('t.payment_status', 'paid');
        $builder->where('DATE(t.created_at) >=', $startDate);
        $builder->where('DATE(t.created_at) <=', $endDate);
        
        // Search
        if ($request->getGet('search')['value']) {
            $search = $request->getGet('search')['value'];
            $builder->groupStart()
                ->like('t.transaction_code', $search)
                ->orLike('u.username', $search)
                ->orLike('t.customer_name', $search)
                ->groupEnd();
        }
        
        // Count total records
        $totalRecords = $builder->countAllResults(false);
        
        // Order
        if ($request->getGet('order')) {
            $orderColumnIndex = $request->getGet('order')[0]['column'];
            $orderDir = $request->getGet('order')[0]['dir'];
            $builder->orderBy($columns[$orderColumnIndex], $orderDir);
        } else {
            $builder->orderBy('t.created_at', 'DESC');
        }
        
        // Limit
        $length = $request->getGet('length') ?? 10;
        $start = $request->getGet('start') ?? 0;
        $builder->limit($length, $start);
        
        $data = $builder->get()->getResultArray();
        
        return $this->response->setJSON([
            'draw' => intval($request->getGet('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }

    /**
     * Manager Report - Outlet sendiri saja
     */
    public function managerSalesReport()
    {
        $user = auth()->user();
        $userModel = new UserModel();
        $outletId = $userModel->getUserOutletId($user->id);
        
        if (!$outletId) {
            return redirect()->back()->with('error', 'Anda tidak terdaftar di outlet manapun');
        }
        
        // Get filter dari query string
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        $filterType = $this->request->getGet('filter') ?? 'custom'; // week, month, year, custom
        
        // Set date range based on filter type
        switch ($filterType) {
            case 'week':
                $startDate = date('Y-m-d', strtotime('monday this week'));
                $endDate = date('Y-m-d', strtotime('sunday this week'));
                break;
            case 'month':
                $startDate = date('Y-m-01');
                $endDate = date('Y-m-t');
                break;
            case 'year':
                $startDate = date('Y-01-01');
                $endDate = date('Y-12-31');
                break;
        }
        
        // Summary outlet sendiri
        $db = \Config\Database::connect();
        $builder = $db->table('transactions t');
        $builder->select('
            COUNT(t.id) as total_transactions,
            SUM(t.grand_total) as total_sales,
            SUM(t.total_discount) as total_discounts,
            SUM(t.total_tax) as total_tax,
            SUM(t.total_amount) as total_before_discount
        ');
        $builder->where('t.outlet_id', $outletId);
        $builder->where('t.payment_status', 'paid');
        $builder->where('DATE(t.created_at) >=', $startDate);
        $builder->where('DATE(t.created_at) <=', $endDate);
        
        $summary = $builder->get()->getRowArray();
        
        // Calculate profit (revenue - cost)
        $profit = $this->calculateOutletProfit($outletId, $startDate, $endDate);
        
        // Top selling products (outlet sendiri)
        $topProducts = $this->getTopSellingProducts($startDate, $endDate, $outletId);
        
        // Payment method breakdown
        $paymentBreakdown = $this->getPaymentMethodBreakdown($startDate, $endDate, $outletId);
        
        // Get outlet info
        $outlet = $this->outletModel->find($outletId);
        
        $data = [
            'title' => 'Laporan Penjualan',
            'user' => $user,
            'outlet' => $outlet,
            'outletId' => $outletId,
            'summary' => $summary,
            'profit' => $profit,
            'topProducts' => $topProducts,
            'paymentBreakdown' => $paymentBreakdown,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filterType' => $filterType,
        ];
        
        return view('manager/reports/sales', $data);
    }

    /**
     * DataTables AJAX untuk transaksi manager
     */
    public function managerTransactionsDatatable()
    {
        $user = auth()->user();
        $userModel = new UserModel();
        $outletId = $userModel->getUserOutletId($user->id);
        
        if (!$outletId) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }
        
        $request = $this->request;
        $startDate = $request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $request->getGet('end_date') ?? date('Y-m-d');
        
        $db = \Config\Database::connect();
        $builder = $db->table('transactions t');
        
        // Columns for ordering
        $columns = ['t.transaction_code', 't.created_at', 'u.username', 't.customer_name', 't.payment_method', 't.grand_total'];
        
        // Base query
        $builder->select('
            t.id,
            t.transaction_code,
            t.created_at,
            t.grand_total,
            t.payment_method,
            t.customer_name,
            u.username as cashier_name
        ');
        $builder->join('users u', 'u.id = t.user_id', 'left');
        $builder->where('t.outlet_id', $outletId);
        $builder->where('t.payment_status', 'paid');
        $builder->where('DATE(t.created_at) >=', $startDate);
        $builder->where('DATE(t.created_at) <=', $endDate);
        
        // Search
        if ($request->getGet('search')['value']) {
            $search = $request->getGet('search')['value'];
            $builder->groupStart()
                ->like('t.transaction_code', $search)
                ->orLike('u.username', $search)
                ->orLike('t.customer_name', $search)
                ->groupEnd();
        }
        
        // Count total records
        $totalRecords = $builder->countAllResults(false);
        
        // Order
        if ($request->getGet('order')) {
            $orderColumnIndex = $request->getGet('order')[0]['column'];
            $orderDir = $request->getGet('order')[0]['dir'];
            $builder->orderBy($columns[$orderColumnIndex], $orderDir);
        } else {
            $builder->orderBy('t.created_at', 'DESC');
        }
        
        // Limit
        $length = $request->getGet('length') ?? 10;
        $start = $request->getGet('start') ?? 0;
        $builder->limit($length, $start);
        
        $data = $builder->get()->getResultArray();
        
        return $this->response->setJSON([
            'draw' => intval($request->getGet('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }

    /**
     * Detail transaksi (untuk modal popup)
     */
    public function transactionDetail($transactionId)
    {
        $user = auth()->user();
        
        // Get transaction
        $transaction = $this->transactionModel->find($transactionId);
        
        if (!$transaction) {
            return $this->response->setJSON(['error' => 'Transaksi tidak ditemukan']);
        }
        
        // Check authorization
        if ($user->inGroup('manager')) {
            $userModel = new UserModel();
            $outletId = $userModel->getUserOutletId($user->id);
            
            if ($transaction['outlet_id'] != $outletId) {
                return $this->response->setJSON(['error' => 'Unauthorized']);
            }
        }
        
        // Get transaction details with product info
        $db = \Config\Database::connect();
        $builder = $db->table('transaction_details td');
        $builder->select('
            td.*,
            p.name as product_name,
            p.sku,
            c.name as category_name
        ');
        $builder->join('products p', 'p.id = td.product_id', 'left');
        $builder->join('categories c', 'c.id = p.category_id', 'left');
        $builder->where('td.transaction_id', $transactionId);
        
        $details = $builder->get()->getResultArray();
        
        return $this->response->setJSON([
            'transaction' => $transaction,
            'details' => $details,
        ]);
    }

    /**
     * Helper: Top Selling Products
     */
    private function getTopSellingProducts($startDate, $endDate, $outletId = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('transaction_details td');
        $builder->select('
            td.product_id,
            p.name as product_name,
            p.sku,
            SUM(td.qty) as total_qty,
            SUM(td.subtotal) as total_revenue
        ');
        $builder->join('transactions t', 't.id = td.transaction_id');
        $builder->join('products p', 'p.id = td.product_id', 'left');
        $builder->where('t.payment_status', 'paid');
        $builder->where('DATE(t.created_at) >=', $startDate);
        $builder->where('DATE(t.created_at) <=', $endDate);
        
        if ($outletId) {
            $builder->where('t.outlet_id', $outletId);
        }
        
        $builder->groupBy('td.product_id, p.name, p.sku');
        $builder->orderBy('total_qty', 'DESC');
        $builder->limit(10);
        
        return $builder->get()->getResultArray();
    }

    /**
     * Helper: Payment Method Breakdown
     */
    private function getPaymentMethodBreakdown($startDate, $endDate, $outletId = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('transactions');
        $builder->select('
            payment_method,
            COUNT(id) as total_transactions,
            SUM(grand_total) as total_amount
        ');
        $builder->where('payment_status', 'paid');
        $builder->where('DATE(created_at) >=', $startDate);
        $builder->where('DATE(created_at) <=', $endDate);
        
        if ($outletId) {
            $builder->where('outlet_id', $outletId);
        }
        
        $builder->groupBy('payment_method');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Helper: Calculate Profit untuk outlet tertentu
     */
    private function calculateOutletProfit($outletId, $startDate, $endDate)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('transaction_details td');
        $builder->select('
            SUM(td.qty * td.price) as total_revenue,
            SUM(td.qty * td.cost_price) as total_cost,
            SUM(td.qty * (td.price - td.cost_price)) as gross_profit
        ');
        $builder->join('transactions t', 't.id = td.transaction_id');
        $builder->where('t.outlet_id', $outletId);
        $builder->where('t.payment_status', 'paid');
        $builder->where('DATE(t.created_at) >=', $startDate);
        $builder->where('DATE(t.created_at) <=', $endDate);
        
        $result = $builder->get()->getRowArray();
        
        $totalRevenue = $result['total_revenue'] ?? 0;
        $totalCost = $result['total_cost'] ?? 0;
        $grossProfit = $result['gross_profit'] ?? 0;
        $profitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;
        
        return [
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'gross_profit' => $grossProfit,
            'profit_margin' => $profitMargin,
        ];
    }
}
