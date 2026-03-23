<?php
/**
 * Product type display mapping.
 * Maps URL/DB type slug to Display Name.
 */
function getProductTypeMapping($pdo) {
    try {
        $stmt = $pdo->query("SELECT name, name as display_name FROM product_type WHERE is_active = TRUE ORDER BY name");
        $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        return $results ?: [];
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Merges a list of new cart entries into the user's existing cart string.
 * Each entry is a string like "type-id-amount".
 */
function mergeCartEntries(string $currentCart, array $newEntries): string {
    $itemCounts = [];
    $allEntries = array_merge(
        $currentCart ? explode(' ', trim($currentCart)) : [],
        $newEntries
    );

    foreach ($allEntries as $entry) {
        $entry = trim($entry);
        if (!$entry) continue;
        
        $parts = explode('-', $entry);
        if (count($parts) === 3) {
            list($type, $itemId, $qty) = $parts;
            $key = "$type-$itemId";
            $itemCounts[$key] = ($itemCounts[$key] ?? 0) + (int)$qty;
        }
    }

    $updatedCart = [];
    foreach ($itemCounts as $key => $qty) {
        $updatedCart[] = "$key-$qty";
    }

    sort($updatedCart);
    return implode(' ', $updatedCart);
}

/**
 * Common sorting logic for product items.
 */
function sortItems(&$items, $sortOrder) {
    if (!$sortOrder) return;
    usort($items, function($a, $b) use ($sortOrder) {
        if ($sortOrder === 'highest') return $b['price'] - $a['price'];
        if ($sortOrder === 'cheapest') return $a['price'] - $b['price'];
        return 0;
    });
}

/**
 * Parse buildset string into an associative array [type => id].
 */
function parseBuildset($buildset) {
    if (empty($buildset)) return [];
    $parts = explode(' ', $buildset);
    $arr = [];
    foreach ($parts as $part) {
        $sub = explode('-', $part);
        if (count($sub) === 2) {
            $arr[$sub[0]] = $sub[1];
        }
    }
    return $arr;
}

/**
 * Format buildset array back to string.
 */
function formatBuildset(array $buildset) {
    $parts = [];
    foreach ($buildset as $type => $id) {
        $parts[] = "$type-$id";
    }
    sort($parts);
    return implode(' ', $parts);
}
function getBuildset($pdo) {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $stmt = $pdo->prepare("SELECT build_id FROM builds WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$userId]);
        $buildId = $stmt->fetchColumn();

        if ($buildId) {
            $stmt = $pdo->prepare("
                SELECT bi.product_id, p.type 
                FROM build_items bi 
                JOIN products p ON bi.product_id = p.product_id 
                WHERE bi.build_id = ?
            ");
            $stmt->execute([$buildId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $parts = [];
            foreach ($items as $item) {
                $parts[] = $item['type'] . "-" . $item['product_id'];
            }
            return implode(' ', $parts);
        }
        return '';
    }
    return $_COOKIE['buildset'] ?? '';
}

/**
 * Saves the buildset to the database (if logged in) or to cookies.
 */
function saveBuildset(string $buildset, $pdo) {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        
        // We just create a new build. Old ones stay as history or can be deleted.
        // For simplicity and matching the provided schema, we'll just insert.
        $stmt = $pdo->prepare("INSERT INTO builds (user_id, name) VALUES (?, 'Current Build')");
        $stmt->execute([$userId]);
        $buildId = $pdo->lastInsertId();
        
        // Insert items
        $parts = array_filter(explode(' ', $buildset));
        foreach ($parts as $part) {
            $sub = explode('-', $part);
            if (count($sub) === 2) {
                $stmt = $pdo->prepare("INSERT INTO build_items (build_id, product_id) VALUES (?, ?)");
                $stmt->execute([$buildId, $sub[1]]);
            }
        }
    } else {
        setcookie('buildset', $buildset, time() + 3600 * 24 * 30, '/');
    }
}

function addToUserCart(int $userId, array $newEntries, $pdo): bool {
    foreach ($newEntries as $entry) {
        $parts = explode('-', $entry);
        if (count($parts) === 3) {
            list($type, $productId, $quantity) = $parts;
            
            // Check if item already in cart
            $stmt = $pdo->prepare("SELECT cart_item_id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $newQty = $existing['quantity'] + (int)$quantity;
                $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?");
                $stmt->execute([$newQty, $existing['cart_item_id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$userId, $productId, $quantity]);
            }
        }
    }
    return true;
}
?>
