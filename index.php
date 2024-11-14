<?php
include 'db.php';

$buildset = $_SESSION['user_id'] ?? $_COOKIE['buildset'] ?? null;
$buildSetComponents = [];
$totalAmount = 0;

if ($buildset) {
    $components = explode(' ', $buildset);

    foreach ($components as $component) {
        list($table, $id) = explode('-', $component);

        $stmt = $pdo->prepare("SELECT name, price, image, brand FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        $componentData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($componentData) {
            $buildSetComponents[] = [
                'table' => $table,
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

$totalAmountFormatted = number_format($totalAmount, 0, ',', '.') . '₫';
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
        <?php include 'web_sections/navbar.php'; ?>
        <?php include 'scripts/categoryMap.php'; ?>

        <main class="container">
            <div class="text-center my-5">
                <h2>Build Your First PC!</h2>
            </div>

            <div class="container">
                <?php foreach ($categoryMap as $componentName => $tableName): ?>
                    <?php
                    $selectedComponent = null;
                    foreach ($buildSetComponents as $component) {
                        if ($component['table'] === $tableName) {
                            $selectedComponent = $component;
                            break;
                        }
                    }
                    ?>

                    <div class="component-card my-4 shadow-sm bg-white text-dark rounded" id="<?= htmlspecialchars($tableName); ?>">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3 w-100 w-md-auto">
                                <span class="text-center" style="flex-shrink: 0; width: 120px;">
                                    <?= htmlspecialchars($componentName); ?>
                                </span>
                                <div class="p-2">
                                    <img src="<?= $selectedComponent ? htmlspecialchars($selectedComponent['image']) : 'component_icons/' . htmlspecialchars($tableName . '.png'); ?>"
                                         alt="<?= htmlspecialchars($componentName); ?>"
                                         id="componentImage-<?= htmlspecialchars($tableName); ?>"
                                         class="component-image"
                                         style="background-color: #ffffff; opacity: 0.7; transition: opacity 0.3s ease; width: 50px; padding: 10px; border-radius: 5px;">
                                </div>
                                <span class="text-muted d-none d-md-inline">
                                    <?= $selectedComponent ? htmlspecialchars($selectedComponent['name']) . " <br> - " . number_format($selectedComponent['price']) . "₫" : "Please select a component"; ?>
                                </span>
                            </div>

                            <button class="btn btn-primary px-4 select-btn mt-2 mt-md-0"
                                    onclick="modalFetchItems('<?= htmlspecialchars($componentName); ?>', '<?= htmlspecialchars($tableName); ?>')"
                                    data-toggle="modal"
                                    data-target="#componentModal">
                                <?= $selectedComponent ? "Change" : "Select"; ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>

//WHERE WE ARE AT
<script>
    // Function to apply image styling and click event to only selected components
    function applyImageStyles() {
    const selectedComponentImages = document.querySelectorAll('.component-card img');

    selectedComponentImages.forEach(function(componentImage) {
        // Exclude components that have the default component image path (component_icons/)
        if (componentImage.src.includes('component_icons/')) {
            return; // Skip applying styles or click events for non-buildset components
        }
        
        // Apply styles only to the images that are part of the buildset
        componentImage.style.width = "60px";
        componentImage.style.height = "60px";
        componentImage.style.padding = "5px";
        componentImage.style.objectFit = "cover";
        componentImage.classList.add('updated-image');
        
        // Add the click event for zooming in the modal
        componentImage.addEventListener('click', function() {
            const modalImage = document.getElementById('modalImage');
            modalImage.src = componentImage.src;
            modalImage.style.backgroundColor = 'white';  // Add white background to the modal image
            $('#imageModal').modal('show');
        });
    });
}


    // Apply image styles and click event after the page is fully loaded
    document.addEventListener("DOMContentLoaded", function() {
        applyImageStyles();
    });
</script>

            </div>


            <div class="container my-4">
    <div class="d-flex justify-content-between align-items-center my-4 px-2">
        <div class="d-flex align-items-center">
        <h5>Total: <span id="totalAmount" class="text-success"><?= htmlspecialchars($totalAmountFormatted); ?></span></h5>
        </div>

        <button id="addToCartButton" class="btn btn-success">Add to Cart</button>
    </div>
</div>

<script>
    document.getElementById('addToCartButton').addEventListener('click', function() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '_buildsetToCart.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        // Data to send
        var data = 'action=addToCart';

        // Handle response
        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert('Added All to Cart!');
                    window.location.reload(); // Reload to reflect the cart changes
                } else {
                    alert(response.message); // Display failure message
                }
            } else {
                alert('An error occurred while adding items to the cart.');
            }
        };

        xhr.send(data); // Send the request
    });
</script>




        </main>

        <div class="modal fade" id="componentModal" tabindex="-1" aria-labelledby="componentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="componentModalLabel">Select Component</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body text-center" style="max-height: 400px; overflow-y: auto;">
                        <div id="modalItemContainer" class="d-flex flex-column justify-content-start"></div>
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

<!-- Full-resolution image modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Component Image" class="img-fluid" style="max-width: 100%; height: auto;">
            </div>
        </div>
    </div>
</div>




</body>
<!-- jQuery Full Version (no slim version) -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="darkmode.js"></script>
<script src="scrolledPosition.js"></script>

<script>
let selectedComponent = {}; // Store the selected component details

function modalFetchItems(componentName, tableName) {
    $('#componentModalLabel').text("Select " + componentName);
    $('#modalItemContainer').empty();

    // Remove any "Updated" message if it exists before reopening the modal
    const modalFooter = $('.modal-footer');
    const updatedMessage = modalFooter.find('.updated-message');
    if (updatedMessage.length > 0) {
        updatedMessage.remove();
    }

    fetch(`_fetch_items.php?table=${tableName}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.length > 0) {
                data.forEach(component => {
                    const componentItem = 
                    
                    // problem at a tag makes it disstorted
                    `
                        <label class="component-item p-3 m-2 rounded d-flex align-items-center justify-content-between bg-white text-dark" style="height: 100%; cursor: pointer;">
                            <div class="d-flex align-items-center">



                                

                                
            <img src="${component.image}" alt="${component.name}" class="img-fluid rounded p-1" 
                                    style="width: 100px; height: 100px; object-fit: cover; margin-right: 15px; background-color: white;">
    

                                


                                <div class="d-flex flex-column" style="text-align: left;">
                                    <a href="item.php?table=${tableName}&id=${component.id}" target="_blank">
<b class="mb-1">${component.name}</b></a>
                                    <p class="mb-1"><strong>Brand:</strong> ${component.brand}</p>
                                    <p class="mb-1"><strong>Price:</strong> ${parseInt(component.price).toLocaleString('vi-VN')}₫</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center" style="margin-left: auto;">
                                <input type="radio" class="form-check-input" name="componentSelection" id="component-${component.id}" 
                                    data-id="${component.id}" data-name="${component.name}" data-price="${component.price}" 
                                    data-image="${component.image}" data-brand="${component.brand}">
                            </div>
                        </label>
                    `;
                    $('#modalItemContainer').append(componentItem);
                });
            } else {
                $('#modalItemContainer').html('<p>No components available or no compatible compoents. <br>Maybe check your network or other components.</p>');
            }
        })
        .catch(() => {
            $('#modalItemContainer').html('<p>Error loading components.</p>');
        });

    // Update selectedComponent when a radio button is clicked
    $('#modalItemContainer').off('change').on('change', 'input[type="radio"]', function() {
        selectedComponent = {
            id: $(this).data('id'),
            name: $(this).data('name'),
            price: $(this).data('price'),
            image: $(this).data('image'),
            brand: $(this).data('brand'),
            tableName: tableName // Store tableName to identify the correct component card
        };
    });
}

// Function to handle Confirm Selection
function confirmSelection() {
    if (selectedComponent.id && selectedComponent.tableName) {
        // Set the selected component's details in the main section by tableName ID
        const componentCard = document.getElementById(selectedComponent.tableName);

        if (componentCard) {
            // Update the name and price in .text-muted element
            componentCard.querySelector('.text-muted').innerHTML = `${selectedComponent.name} <br> - ${parseInt(selectedComponent.price).toLocaleString('vi-VN')}₫`;

            // Update the image with 120% width and height auto, keeping a 1:1 aspect ratio
            const componentImage = componentCard.querySelector('img');
            componentImage.src = selectedComponent.image;
            componentImage.style.width = "60px"; // Set maximum width
            componentImage.style.height = "60px"; // Set height to match width for 1:1 aspect ratio
            componentImage.style.padding = "5px";
            componentImage.style.objectFit = "cover"; // Ensure image is cropped to fill the 1:1 box

            componentImage.classList.add('updated-image');

            // Add a click event to the component image for zooming in the modal
            componentImage.addEventListener('click', function() {
                const modalImage = document.getElementById('modalImage');
                modalImage.src = componentImage.src;  // Copy the full-size image URL to modal
                $('#imageModal').modal('show');
            });
        }

        const confirmationText = `<span style="color: green;">Updated</span>`;
const modalFooter = document.querySelector('.modal-footer');

// Remove any existing updated-message elements before adding the new one
const existingMessage = modalFooter.querySelector('.updated-message');
if (existingMessage) {
    existingMessage.remove();
}

const updatedMessage = document.createElement('div');
updatedMessage.innerHTML = confirmationText;
updatedMessage.classList.add('updated-message');
updatedMessage.style.textAlign = 'center';

// Append the new message
modalFooter.insertBefore(updatedMessage, modalFooter.querySelector('button[type="button"]'));


        // Send only the table name and component ID to _buildset.php
        $.ajax({
            url: '_buildset.php',
            type: 'POST',
            data: {
                component_id: selectedComponent.id,
                table_name: selectedComponent.tableName
            },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    // Update total amount on the page
                    document.getElementById('totalAmount').innerText = `${parseInt(data.totalAmount).toLocaleString('vi-VN')}₫`;
                    $('#componentModal').modal('hide'); // Close the modal after successful update
                } else {
                    alert('Failed to update buildset.');
                }
            },
            error: function(xhr, status, error) {
                alert('Error updating buildset.');
                console.log(error);
            }
        });

        // Clear the selection after closing the modal
        selectedComponent = {};
    } else {
        alert("Please select a component before confirming.");
    }
}



// Attach confirmSelection to Confirm Selection button
document.getElementById('confirmSelect').addEventListener('click', confirmSelection);

</script>
</html>