<?php
include 'db.php';

$buildSetComponents = [];
$totalAmount = 0;

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("SELECT buildset FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && !empty($result['buildset'])) {
        $buildset = $result['buildset'];
    } else {
        $buildset = null;
    }
} else {
    $buildset = $_COOKIE['buildset'] ?? null;
}

if ($buildset) {
    $components = explode(' ', $buildset);

    foreach ($components as $component) {
        $parts = explode('-', $component);
        if (count($parts) === 2) {
            list($table, $id) = $parts;

            if ($id !== null) {
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
    }
}

if (isset($_POST['action']) && $_POST['action'] === 'clearBuildSet') {
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("UPDATE users SET buildset = '' WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } else {
        setcookie('buildset', '', time() - 3600, '/');
    }
    header('Location: index.php');
    exit;
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
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
                                         style="background-color: #ffffff; opacity: 0.7; transition: opacity 0.3s ease; width: 50px; padding: 10px; border-radius: 5px; width: 50px; height: 50px;">
                                </div>
                                <span class="text-muted d-none d-md-inline">
                                    <?= $selectedComponent 
                                        ? "<span style='color: white;'>" . htmlspecialchars($selectedComponent['name']) . "</span><br> - <span class='text-success'>" . number_format($selectedComponent['price']) . "₫</span>" 
                                        : "Please select a component"; ?>
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

                <script>
                    function applyImageStyles() {
                        document.querySelectorAll('.component-card img').forEach(image => {
                            if (image.src.includes('component_icons/')) return;

                        image.style.cssText = 'padding: 5px; object-fit: cover; opacity: 0.9; border-radius: 10px; width: 60px; height: 60px;';
                            image.classList.add('updated-image');

                            image.addEventListener('click', () => {
                                const modalImage = document.getElementById('modalImage');
                                modalImage.src = image.src;
                                modalImage.style.backgroundColor = 'white';
                                $('#imageModal').modal('show');
                            });
                        });
                    }

                    document.addEventListener("DOMContentLoaded", applyImageStyles);
                </script>
            </div>

            <div class="container my-4">
                <div class="d-flex justify-content-between align-items-center my-4 px-2">
                    <div class="d-flex align-items-center">
                        <h5>Total: <span id="totalAmount" class="text-success"><?= htmlspecialchars($totalAmountFormatted); ?></span></h5>
                    </div>
                    <div>
                        <form action="index.php" method="post" style="display: inline;">
                            <input type="hidden" name="action" value="clearBuildSet">
                            <button type="submit" class="btn btn-danger mr-2">Clear</button>
                        </form>
                        <button id="addToCartButton" class="btn btn-success">Add to Cart</button>
                    </div>
                </div>
            </div>

            <script>
                document.getElementById('addToCartButton').addEventListener('click', function() {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '_buildsetToCart.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            var response = JSON.parse(xhr.responseText);
                            response.success ? alert('Added All to Cart!') : alert(response.message);
                            if (response.success) location.reload();
                        } else {
                            alert('An error occurred while adding items to the cart.');
                        }
                    };
                    xhr.send('action=addToCart');
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

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="darkmode.js"></script>
<script src="scrolledPosition.js"></script>

<script>
let selectedComponent = {};

function modalFetchItems(componentName, tableName) {
	$('#componentModalLabel').text("Select " + componentName);
	$('#modalItemContainer').empty();

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
			const count = data.count || 0;
			$('#componentModalLabel').text("Select " + componentName + " - " + count + " Available");

			if (count > 0) {
				data.items.forEach(component => {
					const componentItem = `
                        <label class="component-item p-3 m-2 rounded d-flex align-items-center justify-content-between bg-white text-dark" style="height: 100%; cursor: pointer;">
                            <div class="d-flex align-items-center">
                                <img src="${component.image}" alt="${component.name}" class="img-fluid rounded p-1" 
                                    style="width: 100px; height: 100px; object-fit: cover; margin-right: 15px; background-color: white;">
                                <div class="d-flex flex-column" style="text-align: left;">
                                    <a href="item.php?table=${tableName}&id=${component.id}" target="_blank">
                                        <b class="mb-1">${component.name}</b>
                                    </a>
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
				$('#modalItemContainer').html('<p>No components available or no compatible components. <br>Maybe check your network or other components.</p>');
			}
		})
		.catch(() => {
			$('#modalItemContainer').html('<p>Error loading components.</p>');
		});

	$('#modalItemContainer').off('change').on('change', 'input[type="radio"]', function() {
		selectedComponent = {
			id: $(this).data('id'),
			name: $(this).data('name'),
			price: $(this).data('price'),
			image: $(this).data('image'),
			brand: $(this).data('brand'),
			tableName: tableName 
		};
	});
}

function confirmSelection() {
	if (selectedComponent.id && selectedComponent.tableName) {
		const componentCard = document.getElementById(selectedComponent.tableName);

		if (componentCard) {
			componentCard.querySelector('.text-muted').innerHTML = `<span style='color: white;'>${selectedComponent.name}</span> <br> - <span class='text-success'>${parseInt(selectedComponent.price).toLocaleString('vi-VN')}₫</span>`;
			const componentImage = componentCard.querySelector('img');
			componentImage.src = selectedComponent.image;
			componentImage.style.cssText = "padding: 5px; object-fit: cover; border-radius: 10px; width: 60px; height: 60px;opacity: 0.9;";
			componentImage.classList.add('updated-image');

			componentImage.addEventListener('click', function() {
				const modalImage = document.getElementById('modalImage');
				modalImage.src = componentImage.src; 
				$('#imageModal').modal('show');
			});
		}

		const confirmationText = `<span style="color: green;">Updated</span>`;
		const modalFooter = document.querySelector('.modal-footer');
		const existingMessage = modalFooter.querySelector('.updated-message');
		if (existingMessage) {
			existingMessage.remove();
		}

		const updatedMessage = document.createElement('div');
		updatedMessage.innerHTML = confirmationText;
		updatedMessage.classList.add('updated-message');
		updatedMessage.style.textAlign = 'center';
		modalFooter.insertBefore(updatedMessage, modalFooter.querySelector('button[type="button"]'));

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
					document.getElementById('totalAmount').innerText = `${parseInt(data.totalAmount).toLocaleString('vi-VN')}₫`;
				} else {
					alert('Failed to update buildset.');
				}
			},
			error: function(xhr, status, error) {
				alert('Error updating buildset.');
				console.log(error);
			}
		});
		
		selectedComponent = {};
		bootstrap.Modal.getInstance(document.getElementById('componentModal'))?.hide();
	} else {
		alert("Please select a component before confirming.");
	}
}

document.getElementById('confirmSelect').addEventListener('click', confirmSelection);
</script>
</html>