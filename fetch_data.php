<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pcbuilding", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    exit("Connection failed: " . $e->getMessage());
}

function fetchTableData($pdo, $tableName) {
    $stmt = $pdo->prepare("SELECT * FROM `$tableName`");
    $stmt->execute();
    return $stmt->fetchAll();
}

$tableName = 'motherboard';
$data = fetchTableData($pdo, $tableName);

if ($data) {
    $columns = array_keys($data[0]);
    echo "<table class='table table-bordered'><thead><tr>";
    foreach ($columns as $column) {
        echo "<th>" . htmlspecialchars($column) . "</th>";
    }
    echo "</tr></thead><tbody>";

    foreach ($data as $row) {
        echo "<tr>";
        foreach ($columns as $column) {
            echo "<td>" . htmlspecialchars($row[$column]) . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table>";
} else {
    echo "No data found.";
}
?>