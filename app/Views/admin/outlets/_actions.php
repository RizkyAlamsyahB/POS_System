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
