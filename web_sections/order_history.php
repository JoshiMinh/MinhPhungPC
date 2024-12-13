<?php
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT name FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order_id'])) {
    $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE order_id = ?");
    $stmt->execute([$_POST['cancel_order_id']]);
    echo "<script>window.location.href = window.location.href;</script>";
    exit();
}
?>

<main class="container my-4">
    <?php if ($orders): ?>
        <?php foreach ($orders as $index => $order): ?>
            <div class="card mb-4 p-1">
                <div class="card-body">
                    <h4 class="card-title">Order #<?= count($orders) - $index ?></h4>
                    <?php
                    $statusClass = '';
                    if ($order['status'] === 'cancelled') {
                        $statusClass = 'text-danger';
                    } elseif ($order['status'] === 'delivered') {
                        $statusClass = 'text-success';
                    }
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="card-text"><strong>Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
                            <p class="card-text"><strong>Status:</strong> <span class="<?= $statusClass ?>"><?= ucfirst(htmlspecialchars($order['status'])) ?></span></p>
                            <p class="card-text"><strong>Total Amount:</strong> <?= number_format($order['total_amount'], 0, ',', '.') ?>₫</p>
                        </div>
                        <div class="col-md-6">
                            <p class="card-text"><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
                            <p class="card-text"><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                            <p class="card-text"><strong>Payment Status:</strong> <?= ucfirst(htmlspecialchars($order['payment_status'])) ?></p>
                        </div>
                    </div>

                    <?php
                    $itemsDetails = [];
                    foreach (explode(' ', $order['items']) as $item) {
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

                    <?php if (!in_array($order['status'], ['delivered', 'shipped', 'cancelled'])): ?>
                        <button class="btn btn-danger" data-toggle="modal" data-target="#cancelModal" data-order-id="<?= $order['order_id'] ?>">Cancel Order</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-warning">You have no order history.</div>
    <?php endif; ?>
</main>

<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Cancel Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">Do you really want to cancel the order?</div>
            <div class="modal-footer">
                <form method="POST" id="cancelOrderForm">
                    <input type="hidden" name="cancel_order_id" id="cancelOrderId">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Yes, Cancel Order</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $('#cancelModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var orderId = button.data('order-id');
        $(this).find('#cancelOrderId').val(orderId);
    });
</script>