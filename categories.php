<?php include 'db.php'; ?>

<?php
function fetchTableData($pdo, $tableName) {
    $stmt = $pdo->prepare("SELECT * FROM `$tableName`");
    $stmt->execute();
    return $stmt->fetchAll();
}

$category = $_GET['category'] ?? '';
$tables = [
    "Motherboard" => "motherboard",
    "CPU" => "processor",
    "RAM" => "memory",
    "Graphics Card" => "graphicscard",
    "Storage" => "storage",
    "Power Supply" => "powersupply",
    "Cooler" => "cpucooler",
    "Case Fan" => "casefan",
    "Operating System" => "operatingsystem",
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse <?php echo htmlspecialchars($category); ?></title>
    <link rel="icon" href="icon.png" type="image/png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'web_sections/navbar.php'; ?>

    <main class="container">
        <div class="row text-center my-5">
            <div class="col">
                <h2><?php echo htmlspecialchars($category); ?></h2>
            </div>
        </div>

        <?php
        if (array_key_exists($category, $tables)) {
            $tableName = $tables[$category];
            $data = fetchTableData($pdo, $tableName);

            if ($data) {
                echo "<table class='table table-bordered'><thead><tr>";
                foreach (array_keys($data[0]) as $column) {
                    echo "<th>" . htmlspecialchars($column) . "</th>";
                }
                echo "</tr></thead><tbody>";

                foreach ($data as $row) {
                    echo "<tr>";
                    foreach ($row as $cell) {
                        echo "<td>" . htmlspecialchars($cell) . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No data available for this category.</p>";
            }
        } else {
            echo "<p>Invalid category selected.</p>";
        }
        ?>
    </main>

    <?php include 'web_sections/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="darkmode.js"></script>
</body>
</html>