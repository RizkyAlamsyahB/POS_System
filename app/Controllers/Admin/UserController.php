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
        $data = [
            'title' => 'Manajemen User',
        ];

        return view('admin/users/index', $data);
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
            0 => 'users.id',
            1 => 'users.username',
            2 => 'auth_identities.secret', // email
            3 => 'outlets.name',
            4 => 'users.active',
            5 => 'users.id' // Actions
        ];
        
        $orderBy = isset($columns[$orderCol]) ? $columns[$orderCol] : 'users.username';
        $orderDir = ($orderDir === 'desc') ? 'DESC' : 'ASC';

        // Build query with proper joins
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->select('users.id, users.username, users.outlet_id, users.active, users.deleted_at, outlets.name as outlet_name, auth_identities.secret as email')
                ->join('outlets', 'outlets.id = users.outlet_id', 'left')
                ->join('auth_identities', 'auth_identities.user_id = users.id AND auth_identities.type = "email_password"', 'left')
                ->join('auth_groups_users', 'auth_groups_users.user_id = users.id', 'left')
                ->where('auth_groups_users.group !=', 'admin'); // Exclude admin users

        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                ->like('users.username', $search)
                ->orLike('auth_identities.secret', $search)
                ->orLike('outlets.name', $search)
            ->groupEnd();
        }

        // Get total records (excluding admin)
        $totalBuilder = $db->table('users');
        $totalRecords = $totalBuilder->join('auth_groups_users', 'auth_groups_users.user_id = users.id', 'left')
                                     ->where('auth_groups_users.group !=', 'admin')
                                     ->countAllResults();
        
        // Get filtered records count
        $filteredRecords = $builder->countAllResults(false);

        // Apply ordering and pagination
        $users = $builder->orderBy($orderBy, $orderDir)
                        ->limit($length, $start)
                        ->get()
                        ->getResultArray();

        // Get user roles
        $userIds = array_column($users, 'id');
        $roles = [];
        
        if (!empty($userIds)) {
            $roleBuilder = $db->table('auth_groups_users');
            $userRoles = $roleBuilder->select('user_id, group')
                                    ->whereIn('user_id', $userIds)
                                    ->get()
                                    ->getResultArray();
            
            foreach ($userRoles as $role) {
                $roles[$role['user_id']] = $role['group'];
            }
        }

        // Format data for DataTable
        $data = [];
        foreach ($users as $index => $user) {
            // Status badge - check deleted first
            if ($user['deleted_at']) {
                $statusBadge = '<span class="badge bg-secondary">Dihapus</span>';
            } else {
                $statusBadge = $user['active'] 
                    ? '<span class="badge bg-success">Aktif</span>' 
                    : '<span class="badge bg-danger">Nonaktif</span>';
            }
            
            $role = $roles[$user['id']] ?? 'unknown';
            $roleBadge = match($role) {
                'admin' => '<span class="badge bg-danger">Admin</span>',
                'manager' => '<span class="badge bg-primary">Manager</span>',
                'cashier' => '<span class="badge bg-info">Cashier</span>',
                default => '<span class="badge bg-secondary">-</span>',
            };
            
            $outletInfo = $user['outlet_name'] 
                ? esc($user['outlet_name'])
                : '<small class="text-muted">Super Admin (All Outlets)</small>';
            
            $username = '<strong>' . esc($user['username']) . '</strong>';
            if ($user['deleted_at']) {
                $username .= ' <span class="badge bg-danger ms-2">Dihapus</span>';
            }
            
            $data[] = [
                $start + $index + 1,
                $username . '<br><small class="text-muted">' . esc($user['email'] ?? '-') . '</small>',
                $roleBadge,
                $outletInfo,
                $statusBadge,
                view('admin/users/_actions', ['user' => $user]),
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
     * Show create user form
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah User',
            'outlets' => $this->outletModel->asObject()->where('is_active', 1)->findAll(),
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
            'outlets' => $this->outletModel->asObject()->where('is_active', 1)->findAll(),
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
     * Delete user (soft delete)
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

        // Soft delete
        $this->userModel->delete($id);

        return redirect()->to('/admin/users')->with('message', 'User berhasil dihapus');
    }

    /**
     * Restore soft deleted user
     */
    public function restore($id)
    {
        $user = $this->userModel->withDeleted()->find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan!');
        }

        if (!$user->deleted_at) {
            return redirect()->back()->with('error', 'User tidak dalam status dihapus!');
        }

        // Use Query Builder to bypass soft delete filter
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        
        if ($builder->where('id', $id)->update(['deleted_at' => null])) {
            return redirect()->back()->with('message', 'User berhasil dipulihkan!');
        }

        return redirect()->back()->with('error', 'Gagal memulihkan user!');
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