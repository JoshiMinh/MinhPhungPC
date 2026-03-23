let selectedComponent = {};

function applyImageStyles() {
    document.querySelectorAll('.component-card img').forEach(image => {
        if (image.src.includes('storage/images/')) return;

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

function modalFetchItems(componentName, tableName) {
	$('#componentModalLabel').text(`Select ${componentName}`);
	$('#modalItemContainer').empty();

	const modalFooter = $('.modal-footer');
	modalFooter.find('.updated-message').remove();

	fetch(`core/fetch.php?table=${tableName}`)
		.then(response => {
			if (!response.ok) throw new Error('Network response was not ok');
			return response.json();
		})
		.then(data => {
			const count = data.count || 0;
			$('#componentModalLabel').text(`Select ${componentName} - ${count} Available`);

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
            componentCard.querySelector('.text-muted').innerHTML = `
                <span class='text-dark'>${selectedComponent.name}</span><br> 
                - <span class='text-success'>${parseInt(selectedComponent.price).toLocaleString('vi-VN')}₫</span>
            `;
            
            const componentImage = componentCard.querySelector('img');
            componentImage.src = selectedComponent.image;
            componentImage.style.cssText = "padding: 5px; object-fit: cover; border-radius: 10px; width: 60px; height: 60px; opacity: 0.9;";
            componentImage.classList.add('updated-image');

            componentImage.addEventListener('click', () => {
                const modalImage = document.getElementById('modalImage');
                modalImage.src = componentImage.src;
                modalImage.style.backgroundColor = 'white';
                $('#imageModal').modal('show');
            });

            const existingRemoveButton = componentCard.querySelector('.btn-danger');
            if (existingRemoveButton) {
                existingRemoveButton.remove();
            }

            const removeButton = `
                <form method="post" action="core/remove.php" style="display: inline;">
                    <input type="hidden" name="table" value="${selectedComponent.tableName}">
                    <button type="submit" class="btn btn-danger px-2 mt-2 mt-md-0 mx-1">Remove</button>
                </form>
            `;
            const changeButton = `
                <button class="btn btn-primary px-3 select-btn mt-2 mt-md-0 mx-1"
                        onclick="modalFetchItems('${selectedComponent.name}', '${selectedComponent.tableName}')"
                        data-toggle="modal"
                        data-target="#componentModal">Change</button>
            `;

            const selectButton = componentCard.querySelector('.select-btn');
            if (selectButton) {
                selectButton.outerHTML = removeButton + changeButton;
            }
        }

        const confirmationText = `<span style="color: green;">Updated</span>`;
        const modalFooter = document.querySelector('.modal-footer');
        modalFooter.querySelector('.updated-message')?.remove();

        const updatedMessage = document.createElement('div');
        updatedMessage.innerHTML = confirmationText;
        updatedMessage.classList.add('updated-message');
        updatedMessage.style.textAlign = 'center';
        modalFooter.insertBefore(updatedMessage, modalFooter.querySelector('button[type="button"]'));

        $.ajax({
            url: 'core/builder.php',
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

document.addEventListener("DOMContentLoaded", () => {
    applyImageStyles();
    
    const confirmSelectBtn = document.getElementById('confirmSelect');
    if (confirmSelectBtn) {
        confirmSelectBtn.addEventListener('click', confirmSelection);
    }

    const addToCartBtn = document.getElementById('addToCartButton');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'core/build_cart.php', true);
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
    }
});
