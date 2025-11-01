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
            'outlets' => $this->outletModel->findAll(),
        ];

        return view('admin/outlets/index', $data);
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
        $outlet = $this->outletModel->find($id);

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
        $outlet = $this->outletModel->find($id);

        if (!$outlet) {
            return redirect()->to('/admin/outlets')->with('error', 'Outlet tidak ditemukan!');
        }

        $rules = [
            'code'    => "required|max_length[20]|is_unique[outlets.code,id,{$id}]",
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

        if ($this->outletModel->update($id, $data)) {
            return redirect()->to('/admin/outlets')->with('message', 'Outlet berhasil diupdate!');
        }

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
        $outlet = $this->outletModel->find($id);

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
