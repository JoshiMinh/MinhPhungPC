<?php
include 'core/config.php';
include 'core/helpers.php';

$type = $_GET['table'] ?? ''; // keeping parameter name 'table' for URL compatibility if needed, or change to 'type'
$mapping = getProductTypeMapping();
$tableDisplayName = $mapping[$type] ?? 'Unknown Category';

$brands = $items = [];
$brandFilter = $_GET['brands'] ?? [];
$minPrice = $_GET['min_price'] ?? null;
$maxPrice = $_GET['max_price'] ?? null;

if ($tableDisplayName !== 'Unknown Category') {
    try {
        $stmtBrands = $pdo->prepare("SELECT DISTINCT b.name as brand FROM products p JOIN brands b ON p.brand_id = b.brand_id WHERE p.type = ? ORDER BY b.name");
        $stmtBrands->execute([$type]);
        $brands = $stmtBrands->fetchAll(PDO::FETCH_ASSOC);

        $stmtPriceRange = $pdo->prepare("SELECT MIN(price) AS min_price, MAX(price) AS max_price FROM products WHERE type = ?");
        $stmtPriceRange->execute([$type]);
        $priceRange = $stmtPriceRange->fetch(PDO::FETCH_ASSOC);

        $minPrice = $minPrice ?: $priceRange['min_price'];
        $maxPrice = $maxPrice ?: $priceRange['max_price'];

        $minPriceNumeric = (float)preg_replace('/[^\d]/', '', $minPrice);
        $maxPriceNumeric = (float)preg_replace('/[^\d]/', '', $maxPrice);

        $formattedMinPrice = number_format($minPriceNumeric, 0, ',', '.');
        $formattedMaxPrice = number_format($maxPriceNumeric, 0, ',', '.');

        $brandCondition = '';
        $params = [':type' => $type, ':min_price' => $minPriceNumeric, ':max_price' => $maxPriceNumeric];
        
        if ($brandFilter) {
            $placeholders = [];
            foreach ($brandFilter as $i => $brand) {
                $key = ":brand$i";
                $placeholders[] = $key;
                $params[$key] = $brand;
            }
            $brandCondition = "AND b.name IN (" . implode(',', $placeholders) . ")";
        }

        $query = "SELECT p.*, b.name as brand, :type as item_table 
                  FROM products p 
                  JOIN brands b ON p.brand_id = b.brand_id 
                  WHERE p.type = :type $brandCondition AND price BETWEEN :min_price AND :max_price 
                  ORDER BY b.name";
        
        $stmtItems = $pdo->prepare($query);
        $stmtItems->execute($params);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        if (isset($_GET['sort'])) {
            sortItems($items, $_GET['sort']);
        }
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
    <link rel="icon" href="../storage/images/icon.png" type="image/png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../styles/styles.css">
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
        <?php include 'components/navbar.php'; ?>
        <?php include 'core/cart_add.php'; ?>
        <h2 class="text-center my-3"><?= $tableDisplayName ?></h2>
        <div class="container-fluid my-1 mx-1">
            <div class="row">
                <?php if ($brands || $minPrice || $maxPrice): ?>
                    <div class="col-12 col-md-3 sidebar-container">
                        <h5>Filter by Price (₫)</h5>
                        <form method="get">
                            <input type="hidden" name="table" value="<?= htmlspecialchars($table) ?>">
                            <div class="form-inline mb-3">
                                <div class="form-group mb-2 w-100">
                                    <label for="minPrice" class="mr-2">Min Price: </label>
                                    <input type="text" class="form-control w-100" id="minPrice" name="min_price"
                                        value="<?= $formattedMinPrice ?>" data-price="<?= $minPriceNumeric ?>"
                                        oninput="formatPriceInput(this)">
                                </div>
                                <div class="form-group mb-2 w-100">
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
                            <button type="submit" class="btn btn-primary btn-sm mt-3 w-100">Filter</button>
                        </form>
                    </div>
                <?php endif; ?>

                <div class="col-12 col-md-9 content-container">
                    <?php if ($items): include 'components/item_display.php' ?>
                    <?php else: ?>
                        <div class="alert alert-warning">No items found.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php include 'components/footer.php'; ?>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="../scripts/main.js"></script>
<script>
    function formatPriceInput(input) {
        let value = input.value.replace(/[^\d]/g, '');
        input.value = new Intl.NumberFormat('de-DE').format(value);
    }
</script>
</body>
</html>