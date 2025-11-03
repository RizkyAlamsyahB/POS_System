<?php if ($category['deleted_at']): ?>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-sm btn-success" onclick="confirmRestore(<?= $category['id'] ?>)" title="Pulihkan">
            <i class="bi bi-arrow-counterclockwise"></i> Pulihkan
        </button>
    </div>
<?php else: ?>
    <div class="btn-group" role="group">
        <a href="/admin/categories/edit/<?= $category['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
            <i class="bi bi-pencil"></i>
        </a>
        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $category['id'] ?>)" title="Hapus">
            <i class="bi bi-trash"></i>
        </button>
    </div>
<?php endif; ?>
