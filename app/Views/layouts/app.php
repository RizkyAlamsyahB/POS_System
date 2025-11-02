<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Point Of Sale') ?> - POS Multi-Outlet</title>
    
    <!-- Mazer CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/css/app.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/css/iconly.css">
    
    <!-- Theme Color Override - Change Mazer primary color from #435EBE to #3772F0 -->
    <link rel="stylesheet" href="<?= base_url('assets/css/theme-override.css') ?>">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">
    
    <?= $this->renderSection('styles') ?>
</head>

<body>
    <div id="app">
        <!-- Sidebar -->
        <div id="sidebar">
            <?= $this->include('layouts/partials/sidebar') ?>
        </div>
        
        <!-- Main Content -->
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
            
            <!-- Session Messages / Alerts -->
            <?php if (session()->has('message')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> <?= session('message') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i> <?= session('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->has('warning')): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?= session('warning') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->has('info')): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle"></i> <?= session('info') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <!-- Page Content -->
            <?= $this->renderSection('content') ?>
            
        </div>
    </div>
    
    <!-- jQuery (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Mazer JS -->
    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/js/app.js"></script>
    
    <!-- Admin Common Functions -->
    <script src="<?= base_url('assets/js/admin-common.js') ?>"></script>
    
    <!-- Global Scripts -->
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });

        // Global delete confirmation function
        function confirmDelete(event, itemName = 'item ini') {
            event.preventDefault();
            
            const form = event.target;
            
            // Create custom modal
            const modalHtml = `
                <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">
                                    <i class="bi bi-exclamation-triangle"></i> Konfirmasi Hapus
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center py-4">
                                <i class="bi bi-trash text-danger" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">Apakah Anda yakin?</h5>
                                <p class="text-muted">Data <strong>${itemName}</strong> akan dihapus secara permanen dan tidak dapat dikembalikan.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="bi bi-x-circle"></i> Batal
                                </button>
                                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                                    <i class="bi bi-trash"></i> Ya, Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal if any
            const existingModal = document.getElementById('deleteConfirmModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            modal.show();
            
            // Handle confirm button
            document.getElementById('confirmDeleteBtn').onclick = function() {
                modal.hide();
                form.submit();
            };
            
            return false;
        }
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>

</html>
