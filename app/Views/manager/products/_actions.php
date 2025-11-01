<button type="button" class="btn btn-sm btn-primary" onclick="updateStock(<?= $product['id'] ?>, '<?= esc($product['name']) ?>', <?= $product['stock'] ?>)">
    <i class="bi bi-pencil-square"></i> Update Stok
</button>
<a href="/manager/products/view/<?= $product['id'] ?>" class="btn btn-sm btn-info">
    <i class="bi bi-eye"></i>
</a>
