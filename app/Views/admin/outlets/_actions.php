<?php if ($outlet['deleted_at']): ?>
    <!-- Outlet is deleted - show restore button only -->
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-sm btn-success" onclick="confirmRestore(<?= $outlet['id'] ?>)" title="Pulihkan">
            <i class="bi bi-arrow-counterclockwise"></i> Pulihkan
        </button>
    </div>
<?php else: ?>
    <!-- Outlet is active - show normal actions -->
    <div class="btn-group" role="group">
        <a href="/admin/outlets/view/<?= $outlet['id'] ?>" class="btn btn-sm btn-info" title="Detail">
            <i class="bi bi-eye"></i>
        </a>
        <a href="/admin/outlets/edit/<?= $outlet['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
            <i class="bi bi-pencil"></i>
        </a>
        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $outlet['id'] ?>)" title="Hapus">
            <i class="bi bi-trash"></i>
        </button>
    </div>
<?php endif; ?>
