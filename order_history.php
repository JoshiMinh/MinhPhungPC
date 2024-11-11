<?php
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: account.php");
    exit();
}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT name FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="icon" href="icon.png" type="image/png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="transition">

<div class="wrapper">
    <div class="content">
        <?php include 'web_sections/navbar.php'; ?>
        <main class="container my-4">
            <h1 class="mb-4">Order History for <?= htmlspecialchars($user['name']) ?></h1>

            <?php if ($orders): ?>
                <?php foreach ($orders as $index => $order): ?>
                    <div class="card mb-4 p-1">
                        <div class="card-body">
                            <h4 class="card-title">Order #<?= count($orders) - $index ?></h4>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="card-text"><strong>Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
                                    <p class="card-text"><strong>Status:</strong> <?= ucfirst(htmlspecialchars($order['status'])) ?></p>
                                    <p class="card-text"><strong>Total Amount:</strong> <?= number_format($order['total_amount'], 0, ',', '.') ?>₫</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="card-text"><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
                                    <p class="card-text"><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                                    <p class="card-text"><strong>Payment Status:</strong> <?= ucfirst(htmlspecialchars($order['payment_status'])) ?></p>
                                </div>
                            </div>

                            <?php
                            $items = explode(' ', $order['items']);
                            $itemsDetails = [];
                            foreach ($items as $item) {
                                list($itemTable, $itemId, $itemQuantity) = explode('-', $item);
                                $itemStmt = $pdo->prepare("SELECT name, price FROM $itemTable WHERE id = ?");
                                $itemStmt->execute([$itemId]);
                                $itemDetails = $itemStmt->fetch(PDO::FETCH_ASSOC);

                                if ($itemDetails) {
                                    $itemsDetails[] = htmlspecialchars($itemDetails['name']) . " (" . number_format($itemDetails['price'], 0, ',', '.') . "₫) x" . $itemQuantity;
                                }
                            }
                            ?>
                            <p class="card-text mt-4"><strong>Items:</strong> <?= implode(', ', $itemsDetails) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-warning">You have no order history.</div>
            <?php endif; ?>
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