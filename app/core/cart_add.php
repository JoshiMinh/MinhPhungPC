<?php
include 'config.php';
include 'helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table'], $_POST['id'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('You must be logged in to add items to the cart.');</script>";
        echo "<script>window.location.href = window.location.href;</script>";
        exit;
    }

    $type   = $_POST['table']; // component type, e.g. 'cpu'
    $id     = $_POST['id'];
    $userId = $_SESSION['user_id'];

    if ($userId && $type && $id) {
        if (addToUserCart($userId, ["$type-$id-1"], $pdo)) {
            echo "<script>window.location.href = window.location.href;</script>";
        } else {
            echo "<script>alert('Failed to add item to cart.');</script>";
            echo "<script>window.location.href = window.location.href;</script>";
        }
    }
}
?>