<?php
include 'db.php';
include 'web_sections/categoryMap.php';

$table = $_GET['table'] ?? '';
$id = $_GET['id'] ?? '';

if ($table && $id) {
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>Item Not Found</h1>";
        exit;
    }
} else {
    echo "<h1>Invalid request</h1>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($item['name']) ?> - MinhPhungPC</title>
    <link rel="icon" href="icon.png" type="image/png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'web_sections/navbar.php'; ?>
    <main class="container my-4">
        <h2 class="text-center"><?= htmlspecialchars($item['name']) ?></h2>
        <div class="card mb-4">
            <img src="<?= htmlspecialchars($item['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($item['name']) ?>">
            <div class="card-body">
                <p class="card-text"><?= number_format($item['price'], 0, ',', '.') . '₫' ?></p>
                <p class="card-text"><strong>Brand:</strong> <?= htmlspecialchars($item['brand']) ?></p>
                <div class="additional-details">
                    <?php foreach ($item as $key => $value): ?>
                        <?php if (!in_array($key, ['id', 'name', 'price', 'image', 'brand'])): ?>
                            <p class="card-text"><strong><?= htmlspecialchars(ucfirst($key)) ?>:</strong> <?= htmlspecialchars($value) ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <button class="btn btn-primary">Add to Cart</button>
            </div>
        </div>
    </main>
    <?php include 'web_sections/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="darkmode.js"></script>
</body>
</html>