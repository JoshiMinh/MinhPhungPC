<?php
try {
    $pdo = new PDO("pgsql:host=127.0.0.1;port=5432;dbname=minhphungpc", "postgres", "siu", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    exit("Connection failed: " . $e->getMessage());
}

session_start();

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    
    if (!$stmt->fetchColumn()) {
        session_unset();
        session_destroy();
        echo "<script>location.reload();</script>";
        exit();
    }
}
?>