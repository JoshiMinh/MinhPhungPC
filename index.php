<?php
include 'db.php';

function fetchItems($tableName) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, name, price, brand, image FROM $tableName");
    $stmt->execute();
    return $stmt->fetchAll() ?: [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MinhPhungPC - Build Your PC</title>
    <link rel="icon" href="icon.png" type="image/png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="wrapper">
    <div class="content">
        <?php include 'web_sections/navbar.php'; ?>
        <?php include 'web_sections/categoryMap.php'; ?>

        <main class="container">
            <div class="text-center my-5">
                <h2>Build Your First PC!</h2>
            </div>

            <div class="container">
                <?php foreach ($categoryMap as $componentName => $tableName): ?>
                    <div class="component-card my-4 shadow-sm bg-white text-dark rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-center" style="width: 120px;"><?= htmlspecialchars($componentName); ?></span>
                                <div class="p-2">
                                    <img src="component_icons/<?= htmlspecialchars($tableName . '.png'); ?>"
                                         alt="<?= htmlspecialchars($componentName); ?>"
                                         style="background-color: #ffffff; opacity: 0.7; transition: opacity 0.3s ease; width: 50px; padding: 10px; border-radius: 5px;">
                                </div>
                                <span class="text-muted">Please select a component</span>
                            </div>
                            <button class="btn btn-primary px-4 select-btn"
                                    data-toggle="modal"
                                    data-target="#componentModal"
                                    data-component-name="<?= htmlspecialchars($componentName); ?>"
                                    data-component-icon="<?= htmlspecialchars($tableName . '.png'); ?>"
                                    data-table="<?= htmlspecialchars($tableName); ?>">Select</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>

        <div class="modal fade" id="componentModal" tabindex="-1" aria-labelledby="componentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="componentModalLabel">Select Component</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body text-center">
                        <img id="modalComponentIcon" src="" alt="" width="80" class="mb-3">
                        <h6 id="modalComponentName">Component Name</h6>
                        <div id="modalItemContainer" class="d-flex flex-wrap justify-content-start">
                            <!-- Items will be dynamically loaded here -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="confirmSelect">Confirm Selection</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'web_sections/footer.php'; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="darkmode.js"></script>
<script>
    $(function() {
        $('.select-btn').on('click', function() {
            const componentName = $(this).data('component-name');
            const componentIcon = $(this).data('component-icon');
            const tableName = $(this).data('table');

            $('#modalComponentName').text(componentName);
            $('#modalComponentIcon').attr('src', 'component_icons/' + componentIcon);

            const modalContainer = $('#modalItemContainer').empty();

            <?php if (isset($_GET['table'])): ?>
                const table = '<?= $_GET['table']; ?>';
                const items = <?php echo json_encode(fetchItems($_GET['table'])); ?>;
                items.forEach(item => {
                    const itemBox = `
                        <div class="col-md-3 mb-3">
                            <div class="h-100 bg-white text-dark p-3 shadow-sm rounded">
                                <img src="${item.image}" alt="${item.name}" class="img-fluid mb-2">
                                <h6>${item.name}</h6>
                                <p>Brand: ${item.brand}</p>
                                <p>Price: $${item.price}</p>
                            </div>
                        </div>
                    `;
                    modalContainer.append(itemBox);
                });
            <?php endif; ?>
        });
    });
</script>
</body>
</html>