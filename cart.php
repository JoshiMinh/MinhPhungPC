<?php

include 'db.php';

$user_id = $_SESSION['user_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_all'])) {
        $stmt = $pdo->prepare("UPDATE users SET cart = '' WHERE user_id = ?");
        $stmt->execute([$user_id]);
        header("Location: cart.php");
        exit();
    }

    if (isset($_POST['remove_item'])) {
        $item_to_remove = $_POST['remove_item'];
        $stmt = $pdo->prepare("SELECT cart FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $cart_data = $stmt->fetchColumn();

        $updated_cart = '';
        $cart_items_raw = explode(' ', $cart_data);
        foreach ($cart_items_raw as $item) {
            if (strpos($item, $item_to_remove) === false) {
                $updated_cart .= $item . ' ';
            }
        }

        $updated_cart = rtrim($updated_cart);
        $stmt = $pdo->prepare("UPDATE users SET cart = ? WHERE user_id = ?");
        $stmt->execute([$updated_cart, $user_id]);
        header("Location: cart.php");
        exit();
    }

    if (isset($_POST['update_quantity'])) {
        $table = $_POST['table'];
        $id = $_POST['id'];
        $quantity = $_POST['quantity'];

        $stmt = $pdo->prepare("SELECT cart FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $cart_data = $stmt->fetchColumn();

        $updated_cart = '';
        $cart_items_raw = explode(' ', $cart_data);
        foreach ($cart_items_raw as $item) {
            $item_parts = explode('-', $item);
            if (count($item_parts) === 3 && $item_parts[0] === $table && $item_parts[1] == $id) {
                $item_parts[2] = $quantity;
            }
            $updated_cart .= implode('-', $item_parts) . ' ';
        }

        $updated_cart = rtrim($updated_cart);
        $stmt = $pdo->prepare("UPDATE users SET cart = ? WHERE user_id = ?");
        $stmt->execute([$updated_cart, $user_id]);
        echo 'Cart updated';
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="icon" href="icon.png" type="image/png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .cart-item { 
            border-radius: 5px; 
            padding: 15px; 
            margin-bottom: 20px; 
            display: flex; 
            align-items: center; 
            position: relative; 
        }
        .cart-item img { 
            max-width: 100px; 
            margin-right: 20px; 
        }
        .cart-item-details { 
            flex: 1; 
        }
        .price { 
            font-size: 110%; 
            color: lightgreen; 
            margin-bottom: 10px; 
        }
        .brand { 
            font-size: 1rem; 
            margin-bottom: 10px; 
        }
        .quantity { 
            display: flex; 
            align-items: center; 
        }
        .quantity-btn { 
            font-size: 1.5rem; 
            margin: 0 10px; 
            cursor: pointer; 
        }
        .remove-item-btn { 
            position: absolute; 
            top: -5px; 
            right: 5px; 
            background: none; 
            border: none; 
            font-size: 1.7rem; 
            color: lightcoral; 
            cursor: pointer; 
        }
        .cart-actions { 
            display: flex; 
            justify-content: flex-end; 
            gap: 10px; 
        }
        .cart-actions button { 
            font-size: 1rem; 
        }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="content">
        <?php include 'web_sections/navbar.php'; ?>
        <main class="container my-4">
            <?php
            $stmt = $pdo->prepare("SELECT cart FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $cart_data = $stmt->fetchColumn();

            $cart_items = [];
            if (!empty($cart_data)) {
                $cart_items_raw = explode(' ', $cart_data);
                foreach ($cart_items_raw as $item) {
                    $item_parts = explode('-', $item);
                    if (count($item_parts) === 3) {
                        $cart_items[] = [
                            'table' => $item_parts[0],
                            'id' => $item_parts[1],
                            'amount' => (int)$item_parts[2]
                        ];
                    }
                }

                foreach ($cart_items as $item) {
                    $stmt = $pdo->prepare("SELECT name, price, brand, image FROM {$item['table']} WHERE id = ?");
                    $stmt->execute([$item['id']]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($product) {
                        echo '<div class="cart-item h-100 bg-white text-dark">';
                        echo '<form method="post" style="position: absolute; top: -5px; right: 5px;">';
                        echo '<input type="hidden" name="remove_item" value="' . htmlspecialchars($item['table']) . '-' . htmlspecialchars($item['id']) . '">';
                        echo '<button class="remove-item-btn" type="submit">&times;</button>';
                        echo '</form>';
                        echo '<img src="' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['name']) . '">';
                        echo '<div class="cart-item-details">';
                        echo '<h5>' . htmlspecialchars($product['name']) . '</h5>';
                        echo '<p class="price">' . number_format($product['price'], 0, ',', '.') . '₫</p>';
                        echo '<p class="brand">Brand: ' . htmlspecialchars($product['brand']) . '</p>';
                        echo '</div>';
                        echo '<div class="quantity">';
                        echo '<button class="quantity-btn" onclick="updateQuantity(' . $item['id'] . ', -1, \'' . $item['table'] . '\')">-</button>';
                        echo '<span>' . (int)$item['amount'] . '</span>';
                        echo '<button class="quantity-btn" onclick="updateQuantity(' . $item['id'] . ', 1, \'' . $item['table'] . '\')">+</button>';
                        echo '</div>';
                        echo '</div>';
                    }
                }

                echo '<div class="cart-actions">';
                echo '<form method="post"><button type="submit" name="remove_all" class="btn btn-danger">Remove All</button></form>';
                echo '<button class="btn btn-success" onclick="showOrderModal()"><b>Order</b></button>';
                echo '</div>';
            } else {
                echo '<div class="alert alert-danger" style="margin: 6rem 0;">No items found in the cart.</div>';
            }
            ?>
        </main>
    </div>
    <?php include 'web_sections/footer.php'; ?>
</div>

<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel">Place Your Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="orderForm">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Shipping Address</label>
                        <textarea class="form-control" id="address" name="address" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" form="orderForm">Place Order</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="darkmode.js"></script>
<script>
    function updateQuantity(productId, delta, table) {
        const quantityElement = event.target.parentElement.querySelector('span');
        let currentQuantity = parseInt(quantityElement.innerText);
        currentQuantity += delta;

        if (currentQuantity < 1) currentQuantity = 1;

        quantityElement.innerText = currentQuantity;

        $.post('cart.php', {
            update_quantity: true,
            table: table,
            id: productId,
            quantity: currentQuantity
        }, function(response) {
            console.log(response);
        });
    }

    function showOrderModal() {
        $('#orderModal').modal('show');
    }
</script>
</body>
</html>