<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    body {
        background: #f5f7fa;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
    }
    
    .login-container {
        max-width: 420px;
        width: 100%;
        padding: 20px;
    }
    
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        background: white;
    }
    
    .card-header {
        background: white;
        border: none;
        padding: 2.5rem 2rem 1rem;
        text-align: center;
    }
    
    .logo-icon {
        width: 60px;
        height: 60px;
        background: #3772F0;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }
    
    .logo-icon i {
        font-size: 2rem;
        color: white;
    }
    
    .card-header h3 {
        margin: 0;
        font-weight: 600;
        color: #1a1a1a;
        font-size: 1.5rem;
    }
    
    .card-header p {
        margin: 0.5rem 0 0;
        color: #6b7280;
        font-size: 0.9rem;
    }
    
    .card-body {
        padding: 1.5rem 2rem 2rem;
    }
    
    .form-label {
        font-weight: 500;
        color: #374151;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }
    
    .form-control {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.65rem 0.9rem;
        font-size: 0.95rem;
        transition: all 0.2s;
    }
    
    .form-control:focus {
        border-color: #3772F0;
        box-shadow: 0 0 0 3px rgba(55, 114, 240, 0.1);
    }
    
    .btn-login {
        background: #3772F0;
        border: none;
        padding: 0.7rem;
        font-weight: 500;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.2s;
    }
    
    .btn-login:hover {
        background: #2557d6;
        transform: translateY(-1px);
    }
    
    .form-check-input:checked {
        background-color: #3772F0;
        border-color: #3772F0;
    }
    
    .form-check-label {
        color: #6b7280;
        font-size: 0.9rem;
    }
    
    .card-footer {
        background: transparent;
        border: none;
        padding: 1rem 2rem 2rem;
    }
    
    .alert {
        border-radius: 8px;
        border: none;
        font-size: 0.9rem;
    }
    
    .alert-danger {
        background: #fef2f2;
        color: #991b1b;
    }
    
    .alert-success {
        background: #f0fdf4;
        color: #166534;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="login-container">
    <div class="card">
        <div class="card-header">
            <div class="logo-icon">
                <i class="bi bi-shop"></i>
            </div>
            <h3>POS System</h3>
            <p>Multi-Outlet Point of Sale</p>
        </div>
        <div class="card-body">
            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <?= session('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif ?>

            <?php if (session()->has('message')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <?= session('message') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif ?>

            <?php if (session()->has('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach (session('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif ?>

            <form action="<?= url_to('login') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="Enter your email"
                           value="<?= old('email') ?>" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Enter your password" required>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-login">
                        Login
                    </button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center">
            <small style="color: #9ca3af;">&copy; 2025 POS Multi-Outlet System</small>
        </div>
    </div>
</div>
<?= $this->endSection() ?>