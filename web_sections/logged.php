<?php

$user = [
    'name' => $_SESSION['username'],
    'email' => $_SESSION['email'],
    'date_of_birth' => $_SESSION['date_of_birth'],
    'profile_image' => $_SESSION['profile_image'],
    'address' => $_SESSION['address']
];
?>

<div class="container">
    <h2 class="text-center">Profile</h2>
    <div class="text-center mb-4">
        <img src="<?= htmlspecialchars($user['profile_image']); ?>" alt="Profile Image" class="rounded-circle" style="width: 150px; height: 150px;">
    </div>

    <form action="update_profile.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="dob">Date of Birth</label>
            <input type="text" class="form-control" id="dob" name="dob" value="<?= htmlspecialchars($user['date_of_birth']); ?>" readonly>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($user['address']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Save Changes</button>

        <h5 class="mt-4">Change Password</h5>
        <div class="form-group">
            <label for="current-password">Current Password</label>
            <input type="password" class="form-control" id="current-password" name="current-password" placeholder="Enter current password" required>
        </div>
        <div class="form-group">
            <label for="new-password">New Password</label>
            <input type="password" class="form-control" id="new-password" name="new-password" placeholder="Enter new password" required>
        </div>
        <div class="form-group">
            <label for="retype-password">Retype New Password</label>
            <input type="password" class="form-control" id="retype-password" name="retype-password" placeholder="Retype new password" required>
        </div>
        <button type="submit" class="btn btn-warning btn-block" name="change-password">Change Password</button>

        <h5 class="mt-4">Change Profile Image</h5>
        <div class="form-group">
            <label for="profile-image">Select an image (JPG, PNG)</label>
            <input type="file" class="form-control-file" id="profile-image" name="profile_image" accept=".jpg, .jpeg, .png">
        </div>
    </form>

    <form action="" method="POST" class="mt-3">
        <button type="submit" class="btn btn-danger btn-block" name="logout">Sign Out</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    }
    ?>
</div>