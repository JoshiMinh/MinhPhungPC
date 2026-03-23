<?php
/**
 * Merges a list of new cart entries into the user's existing cart string.
 *
 * Each entry is a string like "table-id-amount".
 * Quantities for the same item are summed; the result is sorted and re-imploded.
 *
 * @param string   $currentCart  The existing cart string from the DB (may be empty).
 * @param string[] $newEntries   Array of "table-id-amount" strings to add.
 * @return string  The updated cart string.
 */
function mergeCartEntries(string $currentCart, array $newEntries): string
{
    $items = $currentCart ? explode(' ', trim($currentCart)) : [];
    foreach ($newEntries as $entry) {
        $entry = trim($entry);
        if ($entry) $items[] = $entry;
    }

    $itemCounts = [];
    foreach ($items as $item) {
        if ($item) {
            list($tableItem, $itemId, $qty) = explode('-', $item);
            $key = "$tableItem-$itemId";
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
?>
