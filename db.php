<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pcbuilding", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    exit("Connection failed: " . $e->getMessage());
}
?>