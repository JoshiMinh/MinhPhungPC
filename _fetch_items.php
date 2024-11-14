<?php

include 'db.php';

if (isset($_GET['table'])) {
    $query = "SELECT id, name, brand, image, price FROM `{$_GET['table']}`";
    
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $stmt = $pdo->prepare("SELECT buildset FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $buildset = $stmt->fetchColumn();
    } else {
        $buildset = $_COOKIE['buildset'] ?? '';
    }

    if (empty($buildset)) {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    $buildset_parts = explode(' ', $buildset);
    $buildset_array = [];
    foreach ($buildset_parts as $part) {
        list($key, $value) = explode('-', $part);
        $buildset_array[$key] = $value;
    }

    $where_clause = [];

    if ($_GET['table'] == 'processor') {
        if (isset($buildset_array['motherboard'])) {
            $motherboard_id = (int)$buildset_array['motherboard'];
            $where_clause[] = "socket_type = (SELECT socket_type FROM motherboard WHERE id = ?)";
        }
    } elseif ($_GET['table'] == 'memory') {
        if (isset($buildset_array['motherboard'])) {
            $motherboard_id = (int)$buildset_array['motherboard'];
            $where_clause[] = "ddr = (SELECT ddr FROM motherboard WHERE id = ?)";
        }
    } elseif ($_GET['table'] == 'motherboard') {
        if (isset($buildset_array['memory']) && isset($buildset_array['processor'])) {
            $where_clause[] = "socket_type = (SELECT socket_type FROM processor WHERE id = ?)
                               AND ddr = (SELECT ddr FROM memory WHERE id = ?)";
        }
    }

    if (!empty($where_clause)) {
        $query .= " WHERE " . implode(" AND ", $where_clause);
    }

    try {
        $stmt = $pdo->prepare($query);

        if ($_GET['table'] == 'processor' && isset($motherboard_id)) {
            $stmt->execute([$motherboard_id]);
        } elseif ($_GET['table'] == 'memory' && isset($motherboard_id)) {
            $stmt->execute([$motherboard_id]);
        } elseif ($_GET['table'] == 'motherboard' && isset($buildset_array['processor']) && isset($buildset_array['memory'])) {
            $stmt->execute([$buildset_array['processor'], $buildset_array['memory']]);
        } else {
            $stmt->execute();
        }

        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error']);
    }

} else {
    echo json_encode([]);
}

?>