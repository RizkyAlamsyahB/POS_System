<div class="btn-group" role="group">
    <a href="/admin/promotions/manage-items/<?= $promotion['id'] ?>" class="btn btn-sm btn-primary" title="Kelola Item">
        <i class="bi bi-box-seam"></i>
    </a>
    <a href="/admin/promotions/edit/<?= $promotion['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
        <i class="bi bi-pencil"></i>
    </a>
    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $promotion['id'] ?>)" title="Hapus">
        <i class="bi bi-trash"></i>
    </button>
</div>
