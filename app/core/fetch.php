<?php

include "config.php";
include "helpers.php";

if (isset($_GET["table"])) {
    $type = $_GET["table"];
    $buildset = getBuildset($pdo);
    $buildset_array = parseBuildset($buildset);

    $query = "SELECT p.*, b.name as brand FROM products p JOIN brands b ON p.brand_id = b.brand_id WHERE p.type = ?";
    $params = [$type];

    if (!empty($buildset_array)) {
        if ($type == "cpu" && isset($buildset_array["motherboard"])) {
            $query .= " AND specs->>'socket_type' = (SELECT specs->>'socket_type' FROM products WHERE product_id = ?)";
            $params[] = (int)$buildset_array["motherboard"];
        } elseif ($type == "ram" && isset($buildset_array["motherboard"])) {
            $query .= " AND specs->>'ddr' = (SELECT specs->>'ddr' FROM products WHERE product_id = ?)";
            $params[] = (int)$buildset_array["motherboard"];
        } elseif ($type == "motherboard") {
            if (isset($buildset_array["ram"])) {
                $query .= " AND specs->>'ddr' = (SELECT specs->>'ddr' FROM products WHERE product_id = ?)";
                $params[] = (int)$buildset_array["ram"];
            }
            if (isset($buildset_array["cpu"])) {
                $query .= " AND specs->>'socket_type' = (SELECT specs->>'socket_type' FROM products WHERE product_id = ?)";
                $params[] = (int)$buildset_array["cpu"];
            }
        }
    }

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $components = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            "items" => $components,
            "count" => count($components),
        ]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["items" => [], "count" => 0]);
}

?>