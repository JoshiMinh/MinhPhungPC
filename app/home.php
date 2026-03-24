<?php
include 'core/home_logic.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MinhPhungPC - Build Your PC</title>
    <link rel="icon" href="../icon.png" type="image/png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .modal-content {
            background-color: var(--bg-elevated);
            color: var(--text-primary);
        }
        .component-item {
            background-color: var(--bg-elevated);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
        }
        .updated-image {
            cursor: pointer;
        }
        a:hover {
            text-decoration: none;
        }
    </style>
</head>

<body>
<div class="wrapper">
    <div class="content">
        <?php include 'ui/navbar.php'; ?>
        
        <main class="container">
            <div class="text-center my-5">
                <h2>Build Your First PC!</h2>
            </div>

            <div class="container">
                <?php 
                $mapping = getProductTypeMapping($pdo); 
                foreach ($mapping as $tableName => $componentName) {
                    include 'ui/builder_card.php';
                }
                ?>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center my-4 px-2">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <h5>Total: <span id="totalAmount" class="text-success"><?= htmlspecialchars($totalAmountFormatted); ?></span></h5>
                    </div>
                    <div class="d-flex flex-column flex-md-row align-items-center">
                        <div class="d-flex mb-2 mb-md-0">
                            <form action="home.php" method="post" style="display: inline;">
                                <input type="hidden" name="action" value="clearBuildSet">
                                <button type="submit" class="btn btn-danger mr-2 mb-2 mb-md-0">Clear</button>
                            </form>
                            <button id="addToCartButton" class="btn btn-success mb-2 mb-md-0">Add to Cart</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include 'ui/builder_modals.php'; ?>
    </div>
    <?php include 'ui/footer.php'; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="../assets/js/main.js"></script>
<script src="../assets/js/builder.js"></script>

</body>
</html>