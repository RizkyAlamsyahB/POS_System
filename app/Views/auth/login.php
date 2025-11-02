<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    body {
        background: #f8fafc;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
    }
    
    .login-container {
        max-width: 440px;
        width: 100%;
        padding: 20px;
    }
    
    .login-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }
    
    .card-top {
        background: #3772F0;
        padding: 2.5rem 2rem;
        text-align: center;
    }
    
    .logo-wrapper {
        width: 70px;
        height: 70px;
        background: white;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    
    .logo-wrapper i {
        font-size: 2.2rem;
        color: #3772F0;
    }
    
    .card-top h3 {
        margin: 0;
        font-weight: 700;
        color: white;
        font-size: 1.75rem;
        letter-spacing: -0.5px;
    }
    
    .card-top p {
        margin: 0.5rem 0 0;
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.95rem;
        font-weight: 400;
    }
    
    .card-content {
        padding: 2rem 2rem 2.5rem;
    }
    
    .form-label {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .form-control {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.2s;
        background: #f8fafc;
    }
    
    .form-control:focus {
        border-color: #3772F0;
        box-shadow: 0 0 0 4px rgba(55, 114, 240, 0.1);
        background: white;
        outline: none;
    }
    
    .form-control::placeholder {
        color: #94a3b8;
    }
    
    .btn-login {
        background: #3772F0;
        border: none;
        padding: 0.85rem;
        font-weight: 600;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s;
        color: white;
        letter-spacing: 0.3px;
    }
    
    .btn-login:hover {
        background: #2557d6;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(55, 114, 240, 0.3);
    }
    
    .btn-login:active {
        transform: translateY(0);
    }
    
    .form-check-input {
        width: 1.1rem;
        height: 1.1rem;
        border: 2px solid #cbd5e1;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .form-check-input:checked {
        background-color: #3772F0;
        border-color: #3772F0;
    }
    
    .form-check-input:focus {
        box-shadow: 0 0 0 3px rgba(55, 114, 240, 0.1);
    }
    
    .form-check-label {
        color: #475569;
        font-size: 0.9rem;
        cursor: pointer;
        user-select: none;
    }
    
    .card-footer-custom {
        padding: 1.5rem 2rem;
        text-align: center;
        border-top: 1px solid #f1f5f9;
    }
    
    .alert {
        border-radius: 10px;
        border: none;
        font-size: 0.9rem;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
    }
    
    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .alert-success {
        background: #dcfce7;
        color: #166534;
    }
    
    .alert i {
        margin-right: 0.5rem;
    }
    
    .alert ul {
        padding-left: 1.25rem;
    }
    
    .copyright-text {
        color: #94a3b8;
        font-size: 0.85rem;
        font-weight: 500;
    }

    /* Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .login-card {
        animation: fadeInUp 0.5s ease-out;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="login-container">
    <div class="login-card">
        <div class="card-top">
            <div class="logo-wrapper">
                <i class="bi bi-shop"></i>
            </div>
            <h3>POS System</h3>
            <p>Multi-Outlet Point of Sale</p>
        </div>
        
        <div class="card-content">
            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <?= session('error') ?>
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
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="your@email.com"
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
                        Keep me signed in
                    </label>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-login">
                        Sign In
                    </button>
                </div>
            </form>
        </div>
        
        <div class="card-footer-custom">
            <span class="copyright-text">&copy; 2025 POS Multi-Outlet System</span>
        </div>
    </div>
</div>
<?= $this->endSection() ?>