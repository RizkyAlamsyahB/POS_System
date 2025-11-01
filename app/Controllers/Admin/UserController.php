<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\OutletModel;
use CodeIgniter\Shield\Entities\User;

class UserController extends BaseController
{
    protected $userModel;
    protected $outletModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->outletModel = new OutletModel();
    }

    /**
     * Display list of users
     */
    public function index()
    {
        $users = $this->userModel->findAll();
        
        // Get outlet info for each user
        foreach ($users as $user) {
            if ($user->outlet_id) {
                $user->outlet = $this->outletModel->find($user->outlet_id);
            }
            // Get user groups (roles)
            $user->groups = $user->getGroups();
        }

        $data = [
            'title' => 'Manajemen User',
            'users' => $users,
        ];

        return view('admin/users/index', $data);
    }

    /**
     * Show create user form
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah User',
            'outlets' => $this->outletModel->where('is_active', 1)->findAll(),
            'roles' => ['manager', 'cashier'], // Only manager and cashier can be created
        ];

        return view('admin/users/create', $data);
    }

    /**
     * Store new user
     */
    public function store()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'email' => 'permit_empty|valid_email|is_unique[auth_identities.secret]',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
            'role' => 'required|in_list[manager,cashier]', // Only manager and cashier allowed
            'outlet_id' => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $role = $this->request->getPost('role');
        $outletId = $this->request->getPost('outlet_id');

        // Validate: manager/cashier must have outlet_id
        if (in_array($role, ['manager', 'cashier']) && !$outletId) {
            return redirect()->back()->withInput()->with('error', 'Manager dan Cashier harus di-assign ke outlet');
        }

        // Create user using Shield
        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
        ];

        $user = new User($userData);
        $this->userModel->save($user);

        // Get the user ID
        $userId = $this->userModel->getInsertID();

        // Update outlet_id
        if ($outletId) {
            $this->userModel->update($userId, ['outlet_id' => $outletId]);
        }

        // Assign role (group)
        $user = $this->userModel->find($userId);
        $user->addGroup($role);

        return redirect()->to('/admin/users')->with('message', 'User berhasil ditambahkan');
    }

    /**
     * Show edit user form
     */
    public function edit($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
        }

        $currentRole = $user->getGroups()[0] ?? null;
        
        // If user is admin, allow editing admin role. Otherwise, only manager/cashier
        $availableRoles = ($currentRole === 'admin') 
            ? ['admin'] // Admin can only remain admin, cannot be downgraded
            : ['manager', 'cashier']; // Non-admin cannot become admin

        $data = [
            'title' => 'Edit User',
            'user' => $user,
            'outlets' => $this->outletModel->where('is_active', 1)->findAll(),
            'roles' => $availableRoles,
            'currentRole' => $currentRole,
        ];

        return view('admin/users/edit', $data);
    }

    /**
     * Update user
     */
    public function update($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
        }

        $currentRole = $user->getGroups()[0] ?? null;
        $newRole = $this->request->getPost('role');

        // Prevent changing admin role or creating new admin
        if ($currentRole !== 'admin' && $newRole === 'admin') {
            return redirect()->back()->withInput()->with('error', 'Tidak dapat mengubah user menjadi Admin');
        }

        $rules = [
            'username' => "required|min_length[3]|max_length[30]|is_unique[users.username,id,{$id}]",
            'email' => "permit_empty|valid_email|is_unique[auth_identities.secret,user_id,{$id}]",
            'password' => 'permit_empty|min_length[8]',
            'password_confirm' => 'permit_empty|matches[password]',
            'role' => ($currentRole === 'admin') ? 'required|in_list[admin]' : 'required|in_list[manager,cashier]',
            'outlet_id' => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $outletId = $this->request->getPost('outlet_id');

        // Validate outlet assignment
        if ($newRole === 'admin' && $outletId) {
            return redirect()->back()->withInput()->with('error', 'Admin tidak boleh di-assign ke outlet tertentu');
        }
        
        if (in_array($newRole, ['manager', 'cashier']) && !$outletId) {
            return redirect()->back()->withInput()->with('error', 'Manager dan Cashier harus di-assign ke outlet');
        }

        // Update user data
        $updateData = [
            'username' => $this->request->getPost('username'),
        ];

        if ($this->request->getPost('email')) {
            $updateData['email'] = $this->request->getPost('email');
        }

        // Update outlet_id
        $updateData['outlet_id'] = $outletId ?: null;

        $this->userModel->update($id, $updateData);

        // Update password if provided
        if ($this->request->getPost('password')) {
            $user->password = $this->request->getPost('password');
            $this->userModel->save($user);
        }

        // Update role
        $currentGroups = $user->getGroups();
        if (!empty($currentGroups)) {
            foreach ($currentGroups as $group) {
                $user->removeGroup($group);
            }
        }
        $user->addGroup($newRole);

        return redirect()->to('/admin/users')->with('message', 'User berhasil diupdate');
    }

    /**
     * Delete user
     */
    public function delete($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
        }

        // Prevent deleting current logged-in user
        if ($id == auth()->id()) {
            return redirect()->to('/admin/users')->with('error', 'Tidak dapat menghapus user yang sedang login');
        }

        $this->userModel->delete($id);

        return redirect()->to('/admin/users')->with('message', 'User berhasil dihapus');
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
        }

        // Prevent disabling current logged-in user
        if ($id == auth()->id()) {
            return redirect()->to('/admin/users')->with('error', 'Tidak dapat menonaktifkan user yang sedang login');
        }

        // Toggle active status
        $newStatus = $user->active ? 0 : 1;
        $this->userModel->update($id, ['active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->to('/admin/users')->with('message', "User berhasil {$statusText}");
    }
}
