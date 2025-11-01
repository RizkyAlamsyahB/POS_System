<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('sidebar') ?>
<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link active" href="/manager/dashboard">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/manager/products">
            <i class="bi bi-box"></i> Products
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/manager/inventory">
            <i class="bi bi-inbox"></i> Inventory
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/manager/promotions">
            <i class="bi bi-percent"></i> Promotions
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/manager/reports">
            <i class="bi bi-bar-chart"></i> Reports
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/pos">
            <i class="bi bi-cart"></i> POS
        </a>
    </li>
</ul>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-speedometer2"></i> Manager Dashboard
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-calendar"></i> Today
            </button>
        </div>
    </div>
</div>

<div class="row">
    <!-- Today's Sales -->
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Today's Sales</h6>
                        <h2 class="mb-0">Rp -</h2>
                    </div>
                    <div>
                        <i class="bi bi-cash-stack" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Count -->
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Transactions</h6>
                        <h2 class="mb-0">-</h2>
                    </div>
                    <div>
                        <i class="bi bi-receipt" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Items -->
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Low Stock</h6>
                        <h2 class="mb-0">-</h2>
                    </div>
                    <div>
                        <i class="bi bi-exclamation-triangle" style="font-size: 3rem; opacity: 0.3;"></i>
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
                <p>Selamat datang di Manager Dashboard.</p>
                <p class="text-muted mb-0">
                    <strong>Role:</strong> Manager<br>
                    <strong>Access Level:</strong> Outlet Management & Reports
                </p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
