<?php
include 'config.php';

$input = $_POST;
if (empty($input)) {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
}

$orderId = $input['order_id'] ?? null;
$newStatus = $input['status'] ?? null;
$newPaymentStatus = $input['payment_status'] ?? null;

if ($orderId && ($newStatus || $newPaymentStatus)) {
    try {
        $params = [$newStatus, $newPaymentStatus, $orderId];
        $query = "UPDATE orders SET status = COALESCE(?, status), payment_status = COALESCE(?, payment_status) WHERE order_id = ?";
        
        // If it's a cancellation from a user, we might want to verify user_id
        if ($newStatus === 'cancelled' && isset($_SESSION['user_id'])) {
            $query .= " AND user_id = ?";
            $params[] = $_SESSION['user_id'];
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
}
exit();
?>