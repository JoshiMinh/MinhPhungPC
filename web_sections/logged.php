<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: account.php");
    exit();
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT name, email, date_of_birth, profile_image, address, password_hash FROM users WHERE user_id = :user_id");
$stmt->execute(['user_id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC) ?: [
    'name' => '',
    'email' => '',
    'date_of_birth' => '',
    'profile_image' => 'default.jpg',
    'address' => '',
    'password_hash' => ''
];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save-changes'])) {
        $newUsername = $_POST['username'] ?? '';
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE name = :name AND user_id != :user_id");
        $stmt->execute(['name' => $newUsername, 'user_id' => $userId]);
        
        if ($stmt->fetchColumn()) {
            echo '<div class="alert alert-warning" style="margin-top: 4rem; margin-bottom: 4rem;">Username already exists. Please choose a different one.</div>';
        } else {
            $uploadDir = 'profile_images/';
            $oldProfileImage = $user['profile_image'];
            
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                if ($oldProfileImage != 'default.jpg' && file_exists($oldProfileImage)) {
                    unlink($oldProfileImage);
                }

                $fileExtension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
                $newFileName = $newUsername . '.' . $fileExtension;

                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadDir . $newFileName)) {
                    $profileImagePath = $uploadDir . $newFileName;
                    $stmt = $pdo->prepare("UPDATE users SET name = :name, date_of_birth = :dob, address = :address, profile_image = :profile_image WHERE user_id = :user_id");
                    $stmt->execute([
                        'name' => $newUsername,
                        'dob' => $_POST['dob'] ?? $user['date_of_birth'],
                        'address' => $_POST['address'] ?? $user['address'],
                        'profile_image' => $profileImagePath,
                        'user_id' => $userId
                    ]);
                    $_SESSION['profile_image'] = $profileImagePath;
                } else {
                    echo "Error uploading file.";
                }
            } else {
                $stmt = $pdo->prepare("UPDATE users SET name = :name, date_of_birth = :dob, address = :address WHERE user_id = :user_id");
                $stmt->execute([
                    'name' => $newUsername,
                    'dob' => $_POST['dob'] ?? $user['date_of_birth'],
                    'address' => $_POST['address'] ?? $user['address'],
                    'user_id' => $userId
                ]);
            }

            echo "<script>window.location.href = window.location.href;</script>";
            exit();
        }
    }

    if (isset($_POST['change-password'])) {
        $currentPassword = $_POST['current-password'] ?? '';
        $newPassword = $_POST['new-password'] ?? '';
        $retypePassword = $_POST['retype-password'] ?? '';

        if (strlen($newPassword) < 8 || !preg_match('/[A-Za-z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword)) {
            echo '<div class="alert alert-warning">New password must be at least 8 characters long and include both letters and numbers.</div>';
        } elseif ($newPassword !== $retypePassword) {
            echo '<div class="alert alert-warning">New passwords do not match. Please try again.</div>';
        } else {
            $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $userId]);
            $storedPasswordHash = $stmt->fetchColumn();

            if (password_verify($currentPassword, $storedPasswordHash)) {
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password_hash = :password WHERE user_id = :user_id");
                $stmt->execute(['password' => $newPasswordHash, 'user_id' => $userId]);

                echo '<div class="alert alert-success">Password changed successfully!</div>';
            } else {
                echo '<div class="alert alert-warning">Current password is incorrect.</div>';
            }
        }
    }

    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        echo "<script>window.location.href = 'login.php';</script>";
        exit();
    }
}
?>

<div class="container">
    <div class="text-center mb-4">
        <img src="<?= htmlspecialchars($user['profile_image']); ?>" alt="Profile Image" class="rounded-circle" style="width: 150px; height: 150px;">
    </div>

    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" id="info-tab" data-bs-toggle="tab" href="#info">Info</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="orders-tab" data-bs-toggle="tab" href="#orders">Orders</a>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <div class="tab-pane fade show active" id="info">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" class="form-control" id="dob" name="dob" value="<?= htmlspecialchars($user['date_of_birth']); ?>">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($user['address']); ?>">
                </div>
                <div class="form-group">
                    <label for="profile-image">Profile Image</label>
                    <input type="file" class="form-control-file" id="profile-image" name="profile_image">
                </div>
                <button type="submit" class="btn btn-primary btn-block" name="save-changes">Save Changes</button>
            </form>

            <h5 class="mt-4">Change Password</h5>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="current-password">Current Password</label>
                    <input type="password" class="form-control" id="current-password" name="current-password">
                </div>
                <div class="form-group">
                    <label for="new-password">New Password</label>
                    <input type="password" class="form-control" id="new-password" name="new-password">
                </div>
                <div class="form-group">
                    <label for="retype-password">Retype New Password</label>
                    <input type="password" class="form-control" id="retype-password" name="retype-password">
                </div>
                <button type="submit" class="btn btn-warning btn-block" name="change-password">Change Password</button>
            </form>
        </div>

        <div class="tab-pane fade" id="orders">
            <h1 class="mb-4">Order History for <?= htmlspecialchars($user['name']); ?></h1>
            <?php if ($orders): ?>
                <?php foreach ($orders as $index => $order): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title">Order #<?= count($orders) - $index; ?></h4>
                            <p><strong>Date:</strong> <?= htmlspecialchars($order['order_date']); ?></p>
                            <p><strong>Status:</strong> <?= ucfirst(htmlspecialchars($order['status'])); ?></p>
                            <p><strong>Total:</strong> <?= number_format($order['total_amount'], 0, ',', '.'); ?>₫</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No orders found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>