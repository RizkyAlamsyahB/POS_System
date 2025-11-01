<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OutletModel;

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
        
        $data = [
            'title' => 'Point of Sale',
            'user'  => $user,
            'outlet' => $outletStatus['outlet'],
        ];

        return view('pos/index', $data);
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
