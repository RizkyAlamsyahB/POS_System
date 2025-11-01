<div class="btn-group" role="group">
    <a href="/admin/products/stock/<?= $product['id'] ?>" class="btn btn-sm btn-info" title="Kelola Stok">
        <i class="bi bi-box-seam"></i>
    </a>
    <a href="/admin/products/edit/<?= $product['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
        <i class="bi bi-pencil"></i>
    </a>
    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $product['id'] ?>)" title="Hapus">
        <i class="bi bi-trash"></i>
    </button>
</div>