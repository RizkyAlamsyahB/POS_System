<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\OutletModel;

class OutletActiveFilter implements FilterInterface
{
    /**
     * Check if user's outlet is active before allowing access
     * Only applies to manager and cashier (not admin)
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = auth()->user();
        
        // Skip check if not logged in (handled by auth filter)
        if (!$user) {
            return;
        }
        
        // Skip check for admin (admin has access to all outlets)
        if ($user->inGroup('admin')) {
            return;
        }
        
        // Check if user has outlet assigned
        if (!$user->outlet_id) {
            // User without outlet (shouldn't happen for manager/cashier)
            return;
        }
        
        // Check outlet status
        $outletModel = new OutletModel();
        $outlet = $outletModel->find($user->outlet_id);
        
        if (!$outlet) {
            return redirect()->to('/login')->with('error', 'Outlet tidak ditemukan.');
        }
        
        // If outlet is not active, block access to transaction/modification routes ONLY
        if (!$outlet->is_active) {
            $currentPath = trim($request->getUri()->getPath(), '/');
            
            // Block only these specific actions when outlet is inactive
            $blockedPatterns = [
                'pos',           // POS interface
                '/store',        // Create operations
                '/update',       // Update operations
                '/delete',       // Delete operations
                '/create',       // Create form
            ];
            
            foreach ($blockedPatterns as $pattern) {
                if (strpos($currentPath, $pattern) !== false) {
                    // Don't redirect - just return with session error
                    // The controller will handle displaying the error
                    session()->setFlashdata('error', 'Outlet Anda sedang nonaktif. Hubungi administrator untuk mengaktifkan kembali.');
                    return redirect()->to('/manager/dashboard');
                }
            }
        }
        
        return;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
