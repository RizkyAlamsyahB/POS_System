<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PromotionModel;
use App\Models\PromotionItemModel;
use App\Models\OutletModel;
use App\Models\ProductModel;

class PromotionController extends BaseController
{
    protected $promotionModel;
    protected $promotionItemModel;
    protected $outletModel;
    protected $productModel;

    public function __construct()
    {
        $this->promotionModel = new PromotionModel();
        $this->promotionItemModel = new PromotionItemModel();
        $this->outletModel = new OutletModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Display promotion list
     */
    public function index()
    {
        $data = [
            'title' => 'Kelola Promosi',
            'user'  => auth()->user(),
        ];

        return view('admin/promotions/index', $data);
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

        // Column mapping
        $columns = [
            0 => 'promotions.id',
            1 => 'promotions.code',
            2 => 'promotions.name',
            3 => 'promotions.discount_type',
            4 => 'promotions.start_date',
            5 => 'promotions.end_date',
            6 => 'outlets.name',
            7 => 'promotions.is_active',
            8 => 'promotions.id' // Actions
        ];
        
        $orderBy = isset($columns[$orderCol]) ? $columns[$orderCol] : 'promotions.created_at';
        $orderDir = ($orderDir === 'desc') ? 'DESC' : 'ASC';

        // Build query
        $db = \Config\Database::connect();
        $builder = $db->table('promotions');
        $builder->select('promotions.*, outlets.name as outlet_name, outlets.code as outlet_code, COUNT(promotion_items.id) as product_count')
                ->join('outlets', 'outlets.id = promotions.outlet_id', 'left')
                ->join('promotion_items', 'promotion_items.promotion_id = promotions.id', 'left')
                ->groupBy('promotions.id');

        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                ->like('promotions.code', $search)
                ->orLike('promotions.name', $search)
                ->orLike('promotions.description', $search)
                ->orLike('outlets.name', $search)
            ->groupEnd();
        }

        // Get total records
        $totalRecords = $this->promotionModel->countAll();
        
        // Get filtered records count
        $builderCount = clone $builder;
        $filteredRecords = $builderCount->countAllResults(false);

        // Apply ordering and pagination
        $promotions = $builder->orderBy($orderBy, $orderDir)
                             ->limit($length, $start)
                             ->get()
                             ->getResultArray();

        // Format data for DataTable
        $data = [];
        foreach ($promotions as $index => $promo) {
            // Status badge
            $statusBadge = $promo['is_active'] 
                ? '<span class="badge bg-success">Aktif</span>' 
                : '<span class="badge bg-secondary">Nonaktif</span>';
            
            // Discount type and value
            $discountDisplay = '';
            if ($promo['discount_type'] === 'percentage') {
                $discountDisplay = '<span class="badge bg-primary">' . number_format($promo['discount_value'], 0) . '%</span>';
            } else {
                $discountDisplay = '<span class="badge bg-success">Rp ' . number_format($promo['discount_value'], 0, ',', '.') . '</span>';
            }
            
            // Period
            $period = date('d/m/Y', strtotime($promo['start_date'])) . ' - ' . date('d/m/Y', strtotime($promo['end_date']));
            
            // Outlet
            $outletDisplay = $promo['outlet_name'] 
                ? '<code>' . esc($promo['outlet_code']) . '</code> ' . esc($promo['outlet_name'])
                : '<span class="text-muted">Semua Outlet</span>';
            
            // Product count
            $productBadge = '<span class="badge bg-info">' . $promo['product_count'] . ' produk</span>';
            
            $data[] = [
                $start + $index + 1,
                '<code>' . esc($promo['code']) . '</code>',
                '<strong>' . esc($promo['name']) . '</strong><br><small class="text-muted">' . $productBadge . '</small>',
                $discountDisplay,
                $period,
                $outletDisplay,
                $statusBadge,
                view('admin/promotions/_actions', ['promotion' => $promo]),
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
     * Show create promotion form
     */
    public function create()
    {
        $data = [
            'title'   => 'Tambah Promosi',
            'user'    => auth()->user(),
            'outlets' => $this->outletModel->where('is_active', 1)->findAll(),
        ];

        return view('admin/promotions/create', $data);
    }

    /**
     * Store new promotion
     */
    public function store()
    {
        // CRITICAL: Transform code ke uppercase SEBELUM validasi
        $postData = $this->request->getPost();
        if (isset($postData['code'])) {
            $_POST['code'] = strtoupper($postData['code']);
        }
        
        $rules = $this->promotionModel->getValidationRules();

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare data
        $data = [
            'code'           => strtoupper($this->request->getPost('code')),
            'name'           => $this->request->getPost('name'),
            'description'    => $this->request->getPost('description'),
            'discount_type'  => $this->request->getPost('discount_type'),
            'discount_value' => $this->request->getPost('discount_value'),
            'min_purchase'   => $this->request->getPost('min_purchase') ?: null,
            'max_discount'   => $this->request->getPost('max_discount') ?: null,
            'start_date'     => $this->request->getPost('start_date'),
            'end_date'       => $this->request->getPost('end_date'),
            'start_time'     => $this->request->getPost('start_time') ?: null,
            'end_time'       => $this->request->getPost('end_time') ?: null,
            'outlet_id'      => $this->request->getPost('outlet_id') ?: null,
            'is_active'      => (int) ($this->request->getPost('is_active') ?: 0),
        ];

        if ($this->promotionModel->insert($data)) {
            return redirect()->to('/admin/promotions')->with('message', 'Promosi berhasil ditambahkan!');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menambahkan promosi!');
    }

    /**
     * Show edit promotion form
     */
    public function edit($id)
    {
        $promotion = $this->promotionModel->find($id);

        if (!$promotion) {
            return redirect()->to('/admin/promotions')->with('error', 'Promosi tidak ditemukan!');
        }

        $data = [
            'title'     => 'Edit Promosi',
            'user'      => auth()->user(),
            'promotion' => $promotion,
            'outlets'   => $this->outletModel->where('is_active', 1)->findAll(),
        ];

        return view('admin/promotions/edit', $data);
    }

    /**
     * Update promotion
     */
    public function update($id)
    {
        log_message('info', '=== UPDATE PROMOTION START ===');
        log_message('info', 'Promotion ID: ' . $id);
        
        $promotion = $this->promotionModel->find($id);

        if (!$promotion) {
            log_message('error', 'Promotion tidak ditemukan dengan ID: ' . $id);
            return redirect()->to('/admin/promotions')->with('error', 'Promosi tidak ditemukan!');
        }

        log_message('info', 'Promotion ditemukan: ' . json_encode($promotion));

        // CRITICAL: Transform code ke uppercase SEBELUM validasi
        $postData = $this->request->getPost();
        if (isset($postData['code'])) {
            $_POST['code'] = strtoupper($postData['code']);
        }

        // Validation rules with proper is_unique exception
        $rules = [
            'code' => [
                'rules' => "required|max_length[50]|is_unique[promotions.code,id,{$id}]",
                'errors' => [
                    'required' => 'Kode promo harus diisi',
                    'is_unique' => 'Kode promo sudah digunakan oleh promo lain',
                ]
            ],
            'name'           => 'required|max_length[100]',
            'discount_type'  => 'required|in_list[percentage,fixed_amount]',
            'discount_value' => 'required|decimal|greater_than[0]',
            'start_date'     => 'required|valid_date',
            'end_date'       => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            log_message('error', 'Validation failed: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $isActiveRaw = $this->request->getPost('is_active');
        log_message('info', 'is_active RAW value: ' . var_export($isActiveRaw, true));
        log_message('info', 'is_active type: ' . gettype($isActiveRaw));

        $data = [
            'code'           => strtoupper($this->request->getPost('code')),
            'name'           => $this->request->getPost('name'),
            'description'    => $this->request->getPost('description'),
            'discount_type'  => $this->request->getPost('discount_type'),
            'discount_value' => $this->request->getPost('discount_value'),
            'min_purchase'   => $this->request->getPost('min_purchase') ?: null,
            'max_discount'   => $this->request->getPost('max_discount') ?: null,
            'start_date'     => $this->request->getPost('start_date'),
            'end_date'       => $this->request->getPost('end_date'),
            'start_time'     => $this->request->getPost('start_time') ?: null,
            'end_time'       => $this->request->getPost('end_time') ?: null,
            'outlet_id'      => $this->request->getPost('outlet_id') ?: null,
            'is_active'      => (int) $isActiveRaw,
        ];

        log_message('info', 'Data to update: ' . json_encode($data));

        // Skip model validation since we already validated
        $this->promotionModel->skipValidation(true);
        $updateResult = $this->promotionModel->update($id, $data);
        $this->promotionModel->skipValidation(false);
        
        log_message('info', 'Update result: ' . var_export($updateResult, true));

        if ($updateResult) {
            log_message('info', 'Update BERHASIL');
            return redirect()->to('/admin/promotions')->with('message', 'Promosi berhasil diupdate!');
        }

        log_message('error', 'Update GAGAL - Model errors: ' . json_encode($this->promotionModel->errors()));
        return redirect()->back()->withInput()->with('error', 'Gagal mengupdate promosi!');
    }

    /**
     * Delete promotion
     */
    public function delete($id)
    {
        $promotion = $this->promotionModel->find($id);

        if (!$promotion) {
            return redirect()->to('/admin/promotions')->with('error', 'Promosi tidak ditemukan!');
        }

        // Delete promotion items first (CASCADE should handle this, but just to be safe)
        $this->promotionItemModel->deleteByPromotion($id);

        if ($this->promotionModel->delete($id)) {
            return redirect()->to('/admin/promotions')->with('message', 'Promosi berhasil dihapus!');
        }

        return redirect()->to('/admin/promotions')->with('error', 'Gagal menghapus promosi!');
    }

    /**
     * Manage promotion items
     */
    public function manageItems($id)
    {
        $promotion = $this->promotionModel->getPromotionWithItems($id);

        if (!$promotion) {
            return redirect()->to('/admin/promotions')->with('error', 'Promosi tidak ditemukan!');
        }

        // Get all products
        $products = $this->productModel->select('products.*, categories.name as category_name')
                                      ->join('categories', 'categories.id = products.category_id', 'left')
                                      ->orderBy('products.name', 'ASC')
                                      ->findAll();

        $data = [
            'title'     => 'Kelola Item Promosi',
            'user'      => auth()->user(),
            'promotion' => $promotion,
            'products'  => $products,
        ];

        return view('admin/promotions/manage_items', $data);
    }

    /**
     * Add products to promotion
     */
    public function addItems($id)
    {
        $promotion = $this->promotionModel->find($id);

        if (!$promotion) {
            return $this->response->setJSON(['success' => false, 'message' => 'Promosi tidak ditemukan!']);
        }

        $productIds = $this->request->getPost('product_ids');

        if (empty($productIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pilih minimal satu produk!']);
        }

        if ($this->promotionItemModel->addProducts($id, $productIds)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Produk berhasil ditambahkan!']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menambahkan produk!']);
    }

    /**
     * Remove product from promotion
     */
    public function removeItem($promotionId, $productId)
    {
        if ($this->promotionItemModel->removeProduct($promotionId, $productId)) {
            return redirect()->back()->with('message', 'Produk berhasil dihapus dari promosi!');
        }

        return redirect()->back()->with('error', 'Gagal menghapus produk!');
    }
}
