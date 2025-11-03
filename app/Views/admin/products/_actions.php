<?php if ($product['deleted_at']): ?>
    <!-- Product is deleted - show restore button only -->
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-sm btn-success" onclick="confirmRestore(<?= $product['id'] ?>)" title="Pulihkan">
            <i class="bi bi-arrow-counterclockwise"></i> Pulihkan
        </button>
    </div>
<?php else: ?>
    <!-- Product is active - show normal actions -->
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
<?php endif; ?>
