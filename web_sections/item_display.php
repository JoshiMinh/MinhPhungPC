<?php if ($items): ?>
    <div class="row">
        <?php foreach ($items as $item): ?>
            <div class="col-12 col-sm-6 col-md-4 mb-4">
                <div class="item-block component-card bg-white p-3 shadow-sm rounded text-center" style="height: 375px;">
                    <h5 class="mb-2" style="min-height: 40px;"><?= htmlspecialchars($item['name']) ?></h5>
                    <p class="font-weight-bold mb-1" style="min-height: 20px;">Price: <?= htmlspecialchars($item['price']) ?></p>
                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="img-fluid mb-3" style="width: 170px; height: auto; max-height: 200px;">
                    
                    <div class="additional-info d-flex flex-column align-items-center justify-content-center" style="flex-grow: 1;">
                        <?php 
                        $additionalInfoCount = 0;
                        foreach ($item as $key => $value) {
                            if (!in_array($key, ['name', 'price', 'image']) && $additionalInfoCount < 3) {
                                echo '<p class="font-weight-bold mb-1" style="min-height: 20px;">' . ucfirst(htmlspecialchars($key)) . ': ' . htmlspecialchars($value) . '</p>';
                                $additionalInfoCount++;
                            }
                        }
                        ?>
                    </div>
                </div>
                
                <div class="btn-group text-center mt-auto" role="group">
                    <button class="btn btn-success">Add to Cart</button>
                    <button class="btn btn-primary">View</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-warning text-center">No items found for this search.</div>
<?php endif; ?>