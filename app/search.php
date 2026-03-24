<?php
include 'core/config.php';
include 'core/helpers.php';

$query = $_GET['query'] ?? '';
$items = [];

if ($query) {
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, b.name as brand, p.type AS item_table 
            FROM products p 
            JOIN brands b ON p.brand_id = b.brand_id 
            WHERE p.name LIKE :query OR b.name LIKE :query 
            ORDER BY b.name
        ");
        $stmt->execute(['query' => '%' . $query . '%']);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Search Results: <?= htmlspecialchars($query) ?></title>
    <link rel="icon" href="../storage/images/icon.png" type="image/png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body style="transition: 0.5s;">
    
<div class="wrapper">
    <div class="content">
        <?php include 'ui/navbar.php'; ?>

        <main class="container my-4">
            <h2 class="text-center my-3">Search Results for: <?= htmlspecialchars($query) ?></h2>
            
            <?php if ($items): ?>
                <?php include 'core/cart_add.php'; ?>
                <?php include 'ui/item_display.php'; ?>
            <?php else: ?>
                <div class="alert alert-warning" style="margin: 4rem 0;">
                    No items found for this search.
                </div>
            <?php endif; ?>
        </main>
    </div>

    <?php include 'ui/footer.php'; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>