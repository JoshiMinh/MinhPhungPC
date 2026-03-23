<?php
include 'config.php';
include 'cart_helper.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addToCart') {
    if (!isset($_SESSION['user_id'])) {
        $response['message'] = 'You must be logged in!';
        echo json_encode($response);
        exit;
    }

    $stmt = $pdo->prepare("SELECT buildset FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $buildset = $stmt->fetchColumn();

    if (empty($buildset)) {
        $response['message'] = 'There\'s no components in the build!';
        echo json_encode($response);
        exit;
    }

    $userId = $_SESSION['user_id'];
    $components = explode(' ', $buildset);
    $newEntries = array_map(fn($c) => $c . '-1', $components); // append amount=1

    $stmt = $pdo->prepare("SELECT cart FROM users WHERE user_id = :userId");
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    $currentCart = $stmt->fetchColumn();

    $updatedCartString = mergeCartEntries($currentCart ?: '', $newEntries);

    $stmt = $pdo->prepare("UPDATE users SET cart = :cart WHERE user_id = :userId");
    $stmt->bindParam(':cart', $updatedCartString);
    $stmt->bindParam(':userId', $userId);

    if (!$stmt->execute()) {
        $response['message'] = 'Failed to add item to cart.';
        echo json_encode($response);
        exit;
    }

    $pdo->prepare("UPDATE users SET buildset = '' WHERE user_id = ?")->execute([$userId]);

    $response['success'] = true;
    echo json_encode($response);
}
?>