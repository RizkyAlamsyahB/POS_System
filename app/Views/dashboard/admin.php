<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('sidebar') ?>
<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link active" href="/admin/dashboard">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/admin/outlets">
            <i class="bi bi-shop"></i> Outlets
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/admin/users">
            <i class="bi bi-people"></i> Users
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/admin/products">
            <i class="bi bi-box"></i> Products
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/admin/categories">
            <i class="bi bi-tags"></i> Categories
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/admin/promotions">
            <i class="bi bi-percent"></i> Promotions
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/admin/reports">
            <i class="bi bi-bar-chart"></i> Reports
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/admin/settings">
            <i class="bi bi-gear"></i> Settings
        </a>
    </li>
</ul>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-speedometer2"></i> Admin Dashboard
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-calendar"></i> This Week
            </button>
        </div>
    </div>
</div>

<div class="row">
    <!-- Total Outlets -->
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Outlets</h6>
                        <h2 class="mb-0">-</h2>
                    </div>
                    <div>
                        <i class="bi bi-shop" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Users -->
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Users</h6>
                        <h2 class="mb-0">-</h2>
                    </div>
                    <div>
                        <i class="bi bi-people" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Products -->
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Products</h6>
                        <h2 class="mb-0">-</h2>
                    </div>
                    <div>
                        <i class="bi bi-box" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Transactions -->
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Today's Sales</h6>
                        <h2 class="mb-0">-</h2>
                    </div>
                    <div>
                        <i class="bi bi-cash-stack" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Welcome, <?= esc($user->username) ?>!</h5>
            </div>
            <div class="card-body">
                <p>Selamat datang di Admin Dashboard POS Multi-Outlet System.</p>
                <p class="text-muted mb-0">
                    <strong>Role:</strong> Administrator<br>
                    <strong>Access Level:</strong> Full Access (All Outlets)
                </p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
