<?php

namespace App\Controllers;

use App\Controllers\BaseController;

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
        $data = [
            'title' => 'Manager Dashboard',
            'user'  => auth()->user(),
        ];

        return view('dashboard/manager', $data);
    }

    /**
     * Cashier/POS Interface
     */
    public function pos()
    {
        $data = [
            'title' => 'Point of Sale',
            'user'  => auth()->user(),
        ];

        return view('pos/index', $data);
    }
}
