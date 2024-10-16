<?php
// Database connection
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password
$dbname = "pcbuilding";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch components from the database
$sql = "SELECT * FROM Users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MinhPhungPC - Build Your PC</title>
    <link rel="stylesheet" href="style.css"> <!-- Add CSS for styling -->
</head>
<body>
    <h1>Build Your Own PC</h1>
    <form action="build_pc.php" method="POST">
        <div class="components">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="component-box">
                    <label><?php echo $row['type']; ?></label>
                    <select name="<?php echo $row['type']; ?>">
                        <option value="">Select <?php echo $row['type']; ?></option>
                        <?php
                        // Fetch each component type
                        $sqlType = "SELECT * FROM components WHERE type = '" . $row['type'] . "'";
                        $resultType = $conn->query($sqlType);
                        while ($component = $resultType->fetch_assoc()):
                        ?>
                            <option value="<?php echo $component['id']; ?>">
                                <?php echo $component['name'] . " - $" . $component['price']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            <?php endwhile; ?>
        </div>
        <button type="submit">Build My PC</button>
    </form>

    <?php $conn->close(); ?>
</body>
</html>