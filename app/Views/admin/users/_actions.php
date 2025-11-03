<?php if ($user['deleted_at']): ?>
    <!-- User is deleted - show restore button only -->
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-sm btn-success" onclick="confirmRestore(<?= $user['id'] ?>)" title="Pulihkan">
            <i class="bi bi-arrow-counterclockwise"></i> Pulihkan
        </button>
    </div>
<?php else: ?>
    <!-- User is active - show normal actions -->
    <div class="btn-group" role="group">
        <a href="/admin/users/edit/<?= $user['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
            <i class="bi bi-pencil"></i>
        </a>
        <button type="button" class="btn btn-sm <?= $user['active'] ? 'btn-secondary' : 'btn-success' ?>" 
                onclick="toggleStatus(<?= $user['id'] ?>)" 
                title="<?= $user['active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
            <i class="bi bi-<?= $user['active'] ? 'lock' : 'unlock' ?>"></i>
        </button>
        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $user['id'] ?>)" title="Hapus">
            <i class="bi bi-trash"></i>
        </button>
    </div>
<?php endif; ?>
