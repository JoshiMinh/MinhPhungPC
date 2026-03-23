<?php
include_once __DIR__ . '/config.php';
include_once __DIR__ . '/helpers.php';

$totalAmount = 0;
$buildSetComponents = [];

$userId = $_SESSION['user_id'] ?? null;
if ($userId) {
    if (isset($_COOKIE['buildset']) && $_COOKIE['buildset'] !== '') {
        echo '<script type="text/javascript">
                if (confirm("You have a local build set that exists. You can only keep one. Do you want to replace the build set in the database with the cookie one or discard the local?")) {
                    window.location.href = "index.php?action=replaceBuildSet";
                } else {
                    window.location.href = "home.php?action=discardBuildSet";
                }
              </script>';
    }

    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'replaceBuildSet') {
            saveBuildset($_COOKIE['buildset'], $pdo);
            setcookie('buildset', '', time() - 3600, '/');
        } elseif ($_GET['action'] === 'discardBuildSet') {
            setcookie('buildset', '', time() - 3600, '/');
        }
        header('Location: home.php');
        exit;
    }
}

$buildset = getBuildset($pdo);
if ($buildset) {
    $buildset_array = parseBuildset($buildset);
    $ids = array_values($buildset_array);
    
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        // We use p.type to know which category it is
        $stmt = $pdo->prepare("
            SELECT p.product_id, p.name, p.price, p.image, p.type, b.name as brand 
            FROM products p 
            JOIN brands b ON p.brand_id = b.brand_id 
            WHERE p.product_id IN ($placeholders)
        ");
        $stmt->execute($ids);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Re-index results by product_id for easy lookup
        $indexedResults = [];
        foreach ($results as $row) {
            $indexedResults[$row['product_id']] = $row;
        }

        foreach ($buildset_array as $type => $id) {
            if (isset($indexedResults[$id])) {
                $componentData = $indexedResults[$id];
                $buildSetComponents[] = [
                    'table' => $type,
                    'id' => $id,
                    'name' => $componentData['name'],
                    'price' => $componentData['price'],
                    'image' => $componentData['image'],
                    'brand' => $componentData['brand']
                ];
                $totalAmount += $componentData['price'];
            }
        }
    }
}

if (isset($_POST['action']) && $_POST['action'] === 'clearBuildSet') {
    saveBuildset('', $pdo);
    header('Location: index.php');
    exit;
}

$totalAmountFormatted = number_format($totalAmount, 0, ',', '.') . '₫';
?>
