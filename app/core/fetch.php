<?php

include "config.php";
include "helpers.php";

if (isset($_GET["table"])) {
    $type = $_GET["table"];
    $query = "SELECT p.*, b.name as brand FROM products p JOIN brands b ON p.brand_id = b.brand_id WHERE p.type = :type";
    $params = [':type' => $type];

    if (isset($_SESSION["user_id"])) {
        $user_id = $_SESSION["user_id"];
        $stmt = $pdo->prepare("SELECT buildset FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $buildset = $stmt->fetchColumn();
    } else {
        $buildset = $_COOKIE["buildset"] ?? "";
    }

    if (empty($buildset)) {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $components = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode([
            "items" => $components,
            "count" => count($components),
        ]);
        exit();
    }

    $buildset_array = parseBuildset($buildset);
    $where_clause = [];

    if ($type == "cpu" && isset($buildset_array["motherboard"])) {
        $motherboard_id = (int) $buildset_array["motherboard"];
        $where_clause[] = "JSON_EXTRACT(specs, '$.socket_type') = (SELECT JSON_EXTRACT(specs, '$.socket_type') FROM products WHERE product_id = ?)";
        $params[] = $motherboard_id;
    } elseif ($type == "ram" && isset($buildset_array["motherboard"])) {
        $motherboard_id = (int) $buildset_array["motherboard"];
        $where_clause[] = "JSON_EXTRACT(specs, '$.ddr') = (SELECT JSON_EXTRACT(specs, '$.ddr') FROM products WHERE product_id = ?)";
        $params[] = $motherboard_id;
    } elseif ($type == "motherboard") {
        if (isset($buildset_array["ram"])) {
            $memory_id = (int) $buildset_array["ram"];
            $where_clause[] = "JSON_EXTRACT(specs, '$.ddr') = (SELECT JSON_EXTRACT(specs, '$.ddr') FROM products WHERE product_id = ?)";
            $params[] = $memory_id;
        }
        if (isset($buildset_array["cpu"])) {
            $processor_id = (int) $buildset_array["cpu"];
            $where_clause[] = "JSON_EXTRACT(specs, '$.socket_type') = (SELECT JSON_EXTRACT(specs, '$.socket_type') FROM products WHERE product_id = ?)";
            $params[] = $processor_id;
        }
    }

    if (!empty($where_clause)) {
        $query .= " AND " . implode(" AND ", $where_clause);
    }

    try {
        $stmt = $pdo->prepare($query);
        // We need to merge params correctly. $params already has ':type'.
        // Positional params (?) and named params (:type) don't mix well in some PDO versions.
        // Let's use named params for all or positional for all.
        
        $finalParams = [$type];
        // Re-extract positional params from $params excluding the first one if we use positional
        // Actually, it's easier to just use positional everywhere.
        
        $queryPos = "SELECT p.*, b.name as brand FROM products p JOIN brands b ON p.brand_id = b.brand_id WHERE p.type = ?";
        if (!empty($where_clause)) {
            $queryPos .= " AND " . implode(" AND ", $where_clause);
        }
        
        $stmt = $pdo->prepare($queryPos);
        
        // Build positional params array
        $posParams = [$type];
        if ($type == "cpu" && isset($buildset_array["motherboard"])) {
            $posParams[] = (int) $buildset_array["motherboard"];
        } elseif ($type == "ram" && isset($buildset_array["motherboard"])) {
            $posParams[] = (int) $buildset_array["motherboard"];
        } elseif ($type == "motherboard") {
            if (isset($buildset_array["ram"])) $posParams[] = (int) $buildset_array["ram"];
            if (isset($buildset_array["cpu"])) $posParams[] = (int) $buildset_array["cpu"];
        }

        $stmt->execute($posParams);
        $components = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode([
            "items" => $components,
            "count" => count($components),
        ]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["items" => [], "count" => 0]);
}

?>