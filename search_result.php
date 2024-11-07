<?php
include 'db.php';
include 'web_sections/categoryMap.php';
include 'web_sections/tableColumns.php';

$query = $_GET['query'] ?? '';
$items = [];

if ($query) {
    foreach (array_keys($components) as $table) {
        try {
            $stmt = $pdo->prepare("SELECT *, :table AS item_table FROM $table WHERE name LIKE :query OR brand LIKE :query");
            $stmt->execute(['query' => '%' . $query . '%', 'table' => $table]);
            $items = array_merge($items, $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            echo "Error fetching data: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results: <?= htmlspecialchars($query) ?></title>
    <link rel="icon" href="icon.png" type="image/png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    
<div class="wrapper">
    <div class="content">
        <?php include 'web_sections/navbar.php'; ?>

        <main class="container my-4">
            <h2 class="text-center my-3">Search Results for: <?= htmlspecialchars($query) ?></h2>
            <?php include 'web_sections/add_to_cart.php'; ?>
            <?php include 'web_sections/item_display.php'; ?>
        </main>
    </div>

    <?php include 'web_sections/footer.php'; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="darkmode.js"></script>
</body>
</html>