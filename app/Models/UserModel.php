<?php

namespace App\Models;

use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

class UserModel extends ShieldUserModel
{
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';
    
    protected $allowedFields = [
        'username',
        'status',
        'status_message',
        'active',
        'last_active',
        'deleted_at',
        'outlet_id', // Custom field for multi-outlet
    ];

    /**
     * Get user with outlet information
     */
    public function getUserWithOutlet(int $userId)
    {
        return $this->select('users.*, outlets.code as outlet_code, outlets.name as outlet_name')
            ->join('outlets', 'outlets.id = users.outlet_id', 'left')
            ->where('users.id', $userId)
            ->first();
    }

    /**
     * Get users by outlet
     */
    public function getUsersByOutlet(int $outletId)
    {
        return $this->where('outlet_id', $outletId)
            ->where('active', 1)
            ->findAll();
    }

    /**
     * Check if user is super admin (no outlet assigned)
     */
    public function isSuperAdmin(int $userId): bool
    {
        $user = $this->find($userId);
        return $user && $user->outlet_id === null;
    }

    /**
     * Get user's outlet ID
     */
    public function getUserOutletId(int $userId): ?int
    {
        $user = $this->find($userId);
        return $user ? $user->outlet_id : null;
    }
}
