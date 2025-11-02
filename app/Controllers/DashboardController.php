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
        $data = [
            'title' => 'Admin Dashboard',
            'user'  => auth()->user(),
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
        
        $data = [
            'title' => 'Manager Dashboard',
            'user'  => $user,
            'outlet' => $outletStatus['outlet'],
            'outletInactive' => !$outletStatus['is_active'],
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
        
        // Get all categories
        $categories = $categoryModel->findAll();
        
        // Get all products with their stock
        $db = \Config\Database::connect();
        $sql = "SELECT p.*, c.name as category_name, COALESCE(ps.stock, 0) as stock 
                FROM products p
                LEFT JOIN categories c ON c.id = p.category_id
                LEFT JOIN product_stocks ps ON ps.product_id = p.id AND ps.outlet_id = ?";
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
}
