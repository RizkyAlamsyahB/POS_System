<div class="btn-group" role="group">
    <a href="/admin/categories/edit/<?= $category['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
        <i class="bi bi-pencil"></i>
    </a>
    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $category['id'] ?>)" title="Hapus">
        <i class="bi bi-trash"></i>
    </button>
</div>
