<?php
include 'db.php';
include 'scripts/categoryMap.php';

$table = $_GET['table'] ?? '';
$tableName = array_search($table, $categoryMap) ?: 'Unknown Category';
$tableDisplayName = htmlspecialchars(ucwords(str_replace('_', ' ', $tableName)));

$brands = $items = [];
$brandFilter = $_GET['brands'] ?? [];
$minPrice = $_GET['min_price'] ?? null;
$maxPrice = $_GET['max_price'] ?? null;

if ($tableName !== 'Unknown Category') {
    try {
        $stmtBrands = $pdo->query("SELECT DISTINCT brand FROM $table ORDER BY brand");
        $brands = $stmtBrands->fetchAll(PDO::FETCH_ASSOC);

        $stmtPriceRange = $pdo->query("SELECT MIN(price) AS min_price, MAX(price) AS max_price FROM $table");
        $priceRange = $stmtPriceRange->fetch(PDO::FETCH_ASSOC);

        $minPrice = $minPrice ?: $priceRange['min_price'];
        $maxPrice = $maxPrice ?: $priceRange['max_price'];

        $minPriceNumeric = (float)preg_replace('/[^\d]/', '', $minPrice);
        $maxPriceNumeric = (float)preg_replace('/[^\d]/', '', $maxPrice);

        $formattedMinPrice = number_format($minPriceNumeric, 0, ',', '.');
        $formattedMaxPrice = number_format($maxPriceNumeric, 0, ',', '.');

        $brandCondition = $brandFilter
            ? "AND brand IN ('" . implode("','", array_map('htmlspecialchars', $brandFilter)) . "')"
            : '';

        $priceCondition = "AND price BETWEEN :min_price AND :max_price";

        $stmtItems = $pdo->prepare("SELECT *, '$table' AS item_table FROM $table WHERE 1 $brandCondition $priceCondition ORDER BY brand");
        $stmtItems->bindParam(':min_price', $minPriceNumeric, PDO::PARAM_INT);
        $stmtItems->bindParam(':max_price', $maxPriceNumeric, PDO::PARAM_INT);
        $stmtItems->execute();
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        include 'scripts/sort_by.php';
    } catch (PDOException $e) {
        echo "Error fetching data: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tableDisplayName ?> - MinhPhungPC</title>
    <link rel="icon" href="icon.png" type="image/png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .sidebar-container { position: sticky; height: 100%; background-color: var(--bg-elevated); padding: 1.75rem; }
        .content-container { padding: 1rem; }
        .card { min-width: 200px; }
        .card img { height: auto; max-width: 100%; object-fit: contain; }
        .form-inline input { width: 45%; }
        .price-caption { font-size: 0.9rem; color: #6c757d; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="content">
        <?php include 'web_sections/navbar.php'; ?>
        <?php include 'scripts/add_to_cart.php'; ?>
        <h2 class="text-center my-3"><?= $tableDisplayName ?></h2>
        <div class="container-fluid my-1 mx-1">
            <div class="row">
                <?php if ($brands || $minPrice || $maxPrice): ?>
                    <div class="col-12 col-md-3 sidebar-container">
                        <h5>Filter by Price (₫)</h5>
                        <form method="get">
                            <input type="hidden" name="table" value="<?= htmlspecialchars($table) ?>">
                            <div class="form-inline mb-3">
                                <div class="form-group mr-3">
                                    <label for="minPrice" class="mr-2">Min Price: </label>
                                    <input type="text" class="form-control w-100" id="minPrice" name="min_price"
                                        value="<?= $formattedMinPrice ?>" data-price="<?= $minPriceNumeric ?>"
                                        oninput="formatPriceInput(this)">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="maxPrice" class="mr-2">Max Price:</label>
                                    <input type="text" class="form-control w-100" id="maxPrice" name="max_price"
                                        value="<?= $formattedMaxPrice ?>" data-price="<?= $maxPriceNumeric ?>"
                                        oninput="formatPriceInput(this)">
                                </div>
                            </div>
                            <?php if ($brands): ?>
                                <h5>Filter by Brand</h5>
                                <?php foreach ($brands as $brand): ?>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" 
                                               id="brand-<?= htmlspecialchars($brand['brand']) ?>"
                                               name="brands[]" value="<?= htmlspecialchars($brand['brand']) ?>"
                                               <?= in_array($brand['brand'], $brandFilter) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="brand-<?= htmlspecialchars($brand['brand']) ?>">
                                            <?= htmlspecialchars($brand['brand']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary btn-sm mt-3 w-100">Apply Filter</button>
                        </form>
                    </div>
                <?php endif; ?>

                <div class="col-12 col-md-9 content-container">
                    <?php if ($items): ?>
                        <div class="container">
                            <div class="row">
                                <?php foreach ($items as $index => $item): ?>
                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4 slide-up" style="animation-delay: <?= $index * 0.1 ?>s;">
                                        <div class="card h-100 text-dark p-0" style="border: none; border-radius: 10px; background-color: var(--bg-elevated);">
                                            <a href="item.php?table=<?= urlencode($item['item_table']) ?>&id=<?= urlencode($item['id']) ?>" class="nav-link">
                                                <img src="<?= htmlspecialchars($item['image']) ?>" 
                                                     alt="<?= htmlspecialchars($item['name']) ?>" 
                                                     class="card-img-top" 
                                                     style="height: 200px; object-fit: cover; background-color: white;">
                                            </a>
                                            <div class="card-body">
                                                <p class="card-text mb-2" style="font-family: 'Roboto', sans-serif; font-weight: 100; font-size: 1.1rem;">
                                                    <strong><?= number_format($item['price'], 0, ',', '.') . '₫' ?></strong>
                                                </p>
                                                <h6 class="card-title h6 mb-0">
                                                    <?= htmlspecialchars($item['name']) ?>
                                                </h6>
                                            </div>
                                            <div class="card-footer d-flex" style="padding: 0; height: 50px; border: none;">
                                                <form method="post" style="flex: 7; height: 100%; margin: 0;">
                                                    <input type="hidden" name="table" value="<?= htmlspecialchars($item['item_table']) ?>">
                                                    <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
                                                    <button type="submit" class="btn btn-primary btn-sm w-100 h-100" style="border-radius: 0;">Add to Cart</button>
                                                </form>
                                                <a href="item.php?table=<?= urlencode($item['item_table']) ?>&id=<?= urlencode($item['id']) ?>">
                                                    <button class="btn btn-secondary w-100 h-100" style="flex: 3; border-radius: 0;">View</button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">No items found.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php include 'web_sections/footer.php'; ?>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="darkmode.js"></script>
<script src="scrolledPosition.js"></script>
<script>
    function formatPriceInput(input) {
        let value = input.value.replace(/[^\d]/g, '');
        input.value = new Intl.NumberFormat('de-DE').format(value);
    }
</script>
</body>
</html>