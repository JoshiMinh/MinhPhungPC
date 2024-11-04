<?php if ($items): ?>
    <div class="container">
        <div class="row">
            <?php foreach ($items as $item): ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="card h-100 bg-white text-dark" style="border-radius: 10px;">
                        <a href="item.php?table=<?= urlencode($tableName) ?>" class="nav-link">
                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="card-img-top" style="max-height: 200px;">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($item['name']) ?></h5>
                            <p class="card-text mb-0"><?= number_format($item['price'], 0, ',', '.') . '₫' ?></p>
                        </div>
                        <div class="card-footer d-flex" style="padding: 0; height: 50px;">
                            <button class="btn btn-primary btn-sm" style="flex: 7; height: 100%; margin: 0;">Add to Cart</button>
                            <button class="btn btn-secondary btn-sm" style="flex: 3; height: 100%; margin: 0;">View</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-warning mt-3 mb-3">No items found for this search.</div>
<?php endif; ?>