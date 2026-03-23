<?php
include 'config.php';
include 'helpers.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addToCart') {
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        $response['message'] = 'You must be logged in!';
        echo json_encode($response);
        exit;
    }

    $buildset = getBuildset($pdo);

    if (empty($buildset)) {
        $response['message'] = 'There\'s no components in the build!';
        echo json_encode($response);
        exit;
    }

    $components = explode(' ', trim($buildset));
    $newEntries = array_map(fn($c) => $c . '-1', $components); // append amount=1

    if (!addToUserCart($userId, $newEntries, $pdo)) {
        $response['message'] = 'Failed to add item to cart.';
        echo json_encode($response);
        exit;
    }

    saveBuildset('', $pdo);

    $response['success'] = true;
    echo json_encode($response);
}
?>