<?php 
$user = auth()->user();
$currentUrl = current_url();
?>

<div class="sidebar-wrapper active">
    <div class="sidebar-header position-relative">
        <div class="d-flex justify-content-between align-items-center">
            <div class="logo">
                <a href="<?= base_url() ?>">Point Of Sale</a>
            </div>
            <div class="sidebar-toggler x">
                <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
            </div>
        </div>
    </div>
    
    <div class="sidebar-menu">
        <ul class="menu">
            <li class="sidebar-title">Main Menu</li>
            
            <?php if ($user->inGroup('admin')): ?>
            <!-- Admin Menu -->
            <li class="sidebar-item <?= str_contains($currentUrl, 'admin/dashboard') ? 'active' : '' ?>">
                <a href="/admin/dashboard" class='sidebar-link'>
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li class="sidebar-title">Management</li>
            
            <li class="sidebar-item <?= str_contains($currentUrl, 'admin/outlets') ? 'active' : '' ?>">
                <a href="/admin/outlets" class='sidebar-link'>
                    <i class="bi bi-shop"></i>
                    <span>Outlets</span>
                </a>
            </li>
            
            <li class="sidebar-item <?= str_contains($currentUrl, 'admin/users') ? 'active' : '' ?>">
                <a href="/admin/users" class='sidebar-link'>
                    <i class="bi bi-people-fill"></i>
                    <span>Users</span>
                </a>
            </li>
            
            <li class="sidebar-item <?= str_contains($currentUrl, 'admin/products') ? 'active' : '' ?>">
                <a href="/admin/products" class='sidebar-link'>
                    <i class="bi bi-box-seam"></i>
                    <span>Products</span>
                </a>
            </li>
            
            <li class="sidebar-item <?= str_contains($currentUrl, 'admin/categories') ? 'active' : '' ?>">
                <a href="/admin/categories" class='sidebar-link'>
                    <i class="bi bi-tags-fill"></i>
                    <span>Categories</span>
                </a>
            </li>
            
            <li class="sidebar-item <?= str_contains($currentUrl, 'admin/promotions') ? 'active' : '' ?>">
                <a href="/admin/promotions" class='sidebar-link'>
                    <i class="bi bi-percent"></i>
                    <span>Promotions</span>
                </a>
            </li>
            
            <li class="sidebar-title">Reports</li>
            
            <li class="sidebar-item <?= str_contains($currentUrl, 'admin/reports') ? 'active' : '' ?>">
                <a href="/admin/reports" class='sidebar-link'>
                    <i class="bi bi-bar-chart-fill"></i>
                    <span>Reports</span>
                </a>
            </li>
            
            <?php elseif ($user->inGroup('manager')): ?>
            <!-- Manager Menu -->
            <li class="sidebar-item <?= str_contains($currentUrl, 'manager/dashboard') ? 'active' : '' ?>">
                <a href="/manager/dashboard" class='sidebar-link'>
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li class="sidebar-title">Inventory</li>
            
            <li class="sidebar-item <?= str_contains($currentUrl, 'manager/products') ? 'active' : '' ?>">
                <a href="/manager/products" class='sidebar-link'>
                    <i class="bi bi-box-seam"></i>
                    <span>Products</span>
                </a>
            </li>
            
            <li class="sidebar-title">Sales</li>
            
            <li class="sidebar-item <?= str_contains($currentUrl, '/pos') ? 'active' : '' ?>">
                <a href="/pos" class='sidebar-link'>
                    <span>Point Of Sale</span>
                </a>
            </li>
            
            <li class="sidebar-item <?= str_contains($currentUrl, 'manager/reports') ? 'active' : '' ?>">
                <a href="/manager/reports" class='sidebar-link'>
                    <i class="bi bi-bar-chart-fill"></i>
                    <span>Reports</span>
                </a>
            </li>
            
            <?php else: ?>
            <!-- Cashier Menu -->
            <li class="sidebar-item <?= str_contains($currentUrl, '/pos') ? 'active' : '' ?>">
                <a href="<?= base_url('pos') ?>" class='sidebar-link'>
                    <span>Point Of Sale</span>
                </a>
            </li>
            <?php endif; ?>
            
            <li class="sidebar-title">Account</li>
            
            <li class="sidebar-item">
                <a href="/profile" class='sidebar-link'>
                    <i class="bi bi-person-circle"></i>
                    <span>Profile</span>
                </a>
            </li>
            
            <li class="sidebar-item">
                <a href="/logout" class='sidebar-link'>
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>
