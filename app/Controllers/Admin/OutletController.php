<?php

namespace App\Controllers\Admin;

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
     * Display outlet list (Super Admin - All Outlets)
     */
    public function index()
    {
        $data = [
            'title'   => 'Kelola Outlet',
            'user'    => auth()->user(),
        ];

        return view('admin/outlets/index', $data);
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
            0 => 'id',
            1 => 'code',
            2 => 'name',
            3 => 'address',
            4 => 'phone',
            5 => 'is_active',
            6 => 'id' // Actions
        ];
        
        $orderBy = isset($columns[$orderCol]) ? $columns[$orderCol] : 'name';
        $orderDir = ($orderDir === 'desc') ? 'DESC' : 'ASC';

        // Build query
        $builder = $this->outletModel->builder();

        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                ->like('code', $search)
                ->orLike('name', $search)
                ->orLike('address', $search)
                ->orLike('phone', $search)
            ->groupEnd();
        }

        // Get total records
        $totalRecords = $this->outletModel->countAll();
        
        // Get filtered records count
        $filteredRecords = $builder->countAllResults(false);

        // Apply ordering and pagination
        $outlets = $builder->orderBy($orderBy, $orderDir)
                          ->limit($length, $start)
                          ->get()
                          ->getResultArray();

        // Format data for DataTable
        $data = [];
        foreach ($outlets as $index => $outlet) {
            $statusBadge = $outlet['is_active'] 
                ? '<span class="badge bg-success">Aktif</span>' 
                : '<span class="badge bg-danger">Nonaktif</span>';
            
            $data[] = [
                $start + $index + 1,
                '<code>' . esc($outlet['code']) . '</code>',
                '<strong>' . esc($outlet['name']) . '</strong>',
                esc($outlet['address'] ?? '-'),
                esc($outlet['phone'] ?? '-'),
                $statusBadge,
                view('admin/outlets/_actions', ['outlet' => $outlet]),
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
     * Show create outlet form
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Outlet',
            'user'  => auth()->user(),
        ];

        return view('admin/outlets/create', $data);
    }

    /**
     * Store new outlet
     */
    public function store()
    {
        $rules = [
            'code'    => 'required|max_length[20]|is_unique[outlets.code]',
            'name'    => 'required|max_length[100]',
            'phone'   => 'permit_empty|max_length[20]',
            'address' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'code'      => strtoupper($this->request->getPost('code')),
            'name'      => $this->request->getPost('name'),
            'address'   => $this->request->getPost('address'),
            'phone'     => $this->request->getPost('phone'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if ($this->outletModel->insert($data)) {
            return redirect()->to('/admin/outlets')->with('message', 'Outlet berhasil ditambahkan!');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menambahkan outlet!');
    }

    /**
     * Show edit outlet form
     */
    public function edit($id)
    {
        $outlet = $this->outletModel->asObject()->find($id);

        if (!$outlet) {
            return redirect()->to('/admin/outlets')->with('error', 'Outlet tidak ditemukan!');
        }

        $data = [
            'title'  => 'Edit Outlet',
            'user'   => auth()->user(),
            'outlet' => $outlet,
        ];

        return view('admin/outlets/edit', $data);
    }

    /**
     * Update outlet
     */
    public function update($id)
    {
        log_message('info', '=== UPDATE OUTLET START ===');
        log_message('info', 'Outlet ID: ' . $id);
        
        $outlet = $this->outletModel->find($id);

        if (!$outlet) {
            log_message('error', 'Outlet tidak ditemukan dengan ID: ' . $id);
            return redirect()->to('/admin/outlets')->with('error', 'Outlet tidak ditemukan!');
        }

        log_message('info', 'Outlet ditemukan: ' . json_encode($outlet));

        // Validation rules with proper is_unique exception
        $rules = [
            'code'    => [
                'rules' => "required|max_length[20]|is_unique[outlets.code,id,{$id}]",
                'errors' => [
                    'required' => 'Kode outlet harus diisi',
                    'is_unique' => 'Kode outlet sudah digunakan oleh outlet lain',
                ]
            ],
            'name'    => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Nama outlet harus diisi',
                ]
            ],
            'phone'   => 'permit_empty|max_length[20]',
            'address' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            log_message('error', 'Validation failed: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $isActiveRaw = $this->request->getPost('is_active');
        log_message('info', 'is_active RAW value: ' . var_export($isActiveRaw, true));
        log_message('info', 'is_active type: ' . gettype($isActiveRaw));

        $data = [
            'code'      => strtoupper($this->request->getPost('code')),
            'name'      => $this->request->getPost('name'),
            'address'   => $this->request->getPost('address'),
            'phone'     => $this->request->getPost('phone'),
            'is_active' => (int) $isActiveRaw,
        ];

        log_message('info', 'Data to update: ' . json_encode($data));

        // Skip model validation since we already validated
        $this->outletModel->skipValidation(true);
        $updateResult = $this->outletModel->update($id, $data);
        $this->outletModel->skipValidation(false);
        
        log_message('info', 'Update result: ' . var_export($updateResult, true));

        if ($updateResult) {
            log_message('info', 'Update BERHASIL');
            return redirect()->to('/admin/outlets')->with('message', 'Outlet berhasil diupdate!');
        }

        log_message('error', 'Update GAGAL - Model errors: ' . json_encode($this->outletModel->errors()));
        return redirect()->back()->withInput()->with('error', 'Gagal mengupdate outlet!');
    }

    /**
     * Delete outlet
     */
    public function delete($id)
    {
        $outlet = $this->outletModel->find($id);

        if (!$outlet) {
            return redirect()->to('/admin/outlets')->with('error', 'Outlet tidak ditemukan!');
        }

        // Check if outlet has users
        $usersCount = $this->userModel->where('outlet_id', $id)->countAllResults();
        if ($usersCount > 0) {
            return redirect()->to('/admin/outlets')->with('error', 'Outlet tidak dapat dihapus karena masih memiliki ' . $usersCount . ' pengguna!');
        }

        // Check if outlet has stocks
        $db = \Config\Database::connect();
        $stocksCount = $db->table('product_stocks')->where('outlet_id', $id)->countAllResults();
        if ($stocksCount > 0) {
            return redirect()->to('/admin/outlets')->with('error', 'Outlet tidak dapat dihapus karena masih memiliki stok produk!');
        }

        if ($this->outletModel->delete($id)) {
            return redirect()->to('/admin/outlets')->with('message', 'Outlet berhasil dihapus!');
        }

        return redirect()->to('/admin/outlets')->with('error', 'Gagal menghapus outlet!');
    }

    /**
     * View outlet details with users and stock info
     */
    public function view($id)
    {
        $outlet = $this->outletModel->asObject()->find($id);

        if (!$outlet) {
            return redirect()->to('/admin/outlets')->with('error', 'Outlet tidak ditemukan!');
        }

        // Get users assigned to this outlet
        $users = $this->userModel->where('outlet_id', $id)->findAll();

        // Get stock summary
        $db = \Config\Database::connect();
        $stockSummary = $db->table('product_stocks')
            ->select('COUNT(*) as total_products, SUM(stock) as total_stock')
            ->where('outlet_id', $id)
            ->get()
            ->getRowArray();

        $data = [
            'title'        => 'Detail Outlet',
            'user'         => auth()->user(),
            'outlet'       => $outlet,
            'users'        => $users,
            'stockSummary' => $stockSummary,
        ];

        return view('admin/outlets/view', $data);
    }
}
