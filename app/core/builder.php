<?php
include 'config.php';
include 'helpers.php';

if (isset($_POST['component_id'], $_POST['table_name'])) {
    $component_id = $_POST['component_id'];
    $type = $_POST['table_name']; // maps to 'type' in new schema
    $totalAmount = 0;

    $userId = $_SESSION['user_id'] ?? null;
    $buildset = '';

    if ($userId) {
        $stmt = $pdo->prepare("SELECT buildset FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $buildset = $stmt->fetchColumn() ?: '';
    } else {
        $buildset = $_COOKIE['buildset'] ?? '';
    }

    $buildset_array = parseBuildset($buildset);
    $buildset_array[$type] = $component_id;
    $updatedBuildset = formatBuildset($buildset_array);

    if ($userId) {
        $stmt = $pdo->prepare("UPDATE users SET buildset = ? WHERE user_id = ?");
        $stmt->execute([$updatedBuildset, $userId]);
    } else {
        setcookie('buildset', $updatedBuildset, time() + 3600 * 24 * 30, '/');
    }

    foreach ($buildset_array as $compType => $compId) {
        $priceStmt = $pdo->prepare("SELECT price FROM products WHERE product_id = ?");
        $priceStmt->execute([$compId]);
        $totalAmount += $priceStmt->fetchColumn() ?: 0;
    }

    echo json_encode(['status' => 'success', 'message' => 'Buildset updated successfully.', 'totalAmount' => $totalAmount]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing required data.']);
}
?>