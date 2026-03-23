<?php
include 'config.php';
include 'helpers.php';

try {
    $search = '%' . ($_GET['search'] ?? '') . '%';
    $components = [];

    if ($search !== '%%') {
        $stmt = $pdo->prepare("
            SELECT p.*, b.name as brand, p.type AS item_table 
            FROM products p 
            JOIN brands b ON p.brand_id = b.brand_id 
            WHERE p.name LIKE :query OR b.name LIKE :query
        ");
        $stmt->execute(['query' => $search]);
        $components = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode($components);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Error fetching components: " . $e->getMessage()]);
}
?>