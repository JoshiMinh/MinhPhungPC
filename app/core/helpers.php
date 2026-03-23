<?php
/**
 * Product type display mapping.
 * Maps URL/DB type slug to Display Name.
 */
function getProductTypeMapping() {
    return [
        'cpu'         => 'CPU',
        'gpu'         => 'Graphics Card',
        'ram'         => 'RAM',
        'motherboard' => 'Motherboard',
        'storage'     => 'Storage',
        'psu'         => 'Power Supply',
        'case'        => 'Case',
        'cooler'      => 'Cooler',
        'os'          => 'Operating System',
        'fan'         => 'Fan'
    ];
}

/**
 * Merges a list of new cart entries into the user's existing cart string.
 * Each entry is a string like "type-id-amount".
 */
function mergeCartEntries(string $currentCart, array $newEntries): string {
    $items = $currentCart ? explode(' ', trim($currentCart)) : [];
    foreach ($newEntries as $entry) {
        $entry = trim($entry);
        if ($entry) $items[] = $entry;
    }

    $itemCounts = [];
    foreach ($items as $item) {
        if ($item) {
            $parts = explode('-', $item);
            if (count($parts) === 3) {
                list($type, $itemId, $qty) = $parts;
                $key = "$type-$itemId";
                $itemCounts[$key] = ($itemCounts[$key] ?? 0) + (int)$qty;
            }
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
?>
