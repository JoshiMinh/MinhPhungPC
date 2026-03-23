<?php
include 'config.php';
include 'cart_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table'], $_POST['id'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('You must be logged in to add items to the cart.');</script>";
        echo "<script>window.location.href = window.location.href;</script>";
        exit;
    }

    $table  = $_POST['table'];
    $id     = $_POST['id'];
    $userId = $_SESSION['user_id'];

    if ($userId && $table && $id) {
        $stmt = $pdo->prepare("SELECT cart FROM users WHERE user_id = :userId");
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        $currentCart = $stmt->fetchColumn();

        $updatedCartString = mergeCartEntries($currentCart ?: '', ["$table-$id-1"]);

        $stmt = $pdo->prepare("UPDATE users SET cart = :cart WHERE user_id = :userId");
        $stmt->bindParam(':cart', $updatedCartString);
        $stmt->bindParam(':userId', $userId);

        if ($stmt->execute()) {
            echo "<script>window.location.href = window.location.href;</script>";
        } else {
            echo "<script>alert('Failed to add item to cart.');</script>";
            echo "<script>window.location.href = window.location.href;</script>";
        }
    }
}
?>