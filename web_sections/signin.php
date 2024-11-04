<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['signup'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $dob = $_POST['dob'];

        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE name = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Username or email already exists.');</script>";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, date_of_birth) VALUES (:name, :email, :password_hash, :dob)");
            $success = $stmt->execute(['name' => $username, 'email' => $email, 'password_hash' => $passwordHash, 'dob' => $dob]);
            echo "<script>alert('" . ($success ? "Registration successful!" : "Error. Please try again.") . "');</script>";
        }
    }

    if (isset($_POST['login'])) {
        $loginInput = trim($_POST['login-username-email']);
        $stmt = $pdo->prepare("SELECT user_id, name, password_hash, profile_image, date_of_birth, address, email FROM users WHERE name = :username OR email = :email");
        $stmt->execute(['username' => $loginInput, 'email' => $loginInput]);
        $user = $stmt->fetch();

        if ($user && password_verify($_POST['login-password'], $user['password_hash'])) {
            session_start();
            $_SESSION = array_merge($user, ['username' => $user['name']]);
            echo "<script>alert('Login successful!'); window.location.href='index.php';</script>";
            exit();
        } else {
            echo "<script>alert('Invalid username or password.');</script>";
        }
    }
}
?>

<div class="row">
    <div class="col-md-6">
        <h2>Sign Up</h2>
        <form method="POST">
            <div class="form-group">
                <label for="signup-username">Username</label>
                <input type="text" class="form-control" id="signup-username" name="username" placeholder="Enter username" required>
            </div>
            <div class="form-group">
                <label for="signup-email">Email address</label>
                <input type="email" class="form-control" id="signup-email" name="email" placeholder="Enter email" required>
            </div>
            <div class="form-group">
                <label for="signup-password">Password</label>
                <input type="password" class="form-control" id="signup-password" name="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <label for="signup-dob">Date of Birth</label>
                <input type="date" class="form-control" id="signup-dob" name="dob" required>
            </div>
            <button type="submit" name="signup" class="btn btn-primary">Sign Up</button>
        </form>
    </div>
    <div class="col-md-1 d-none d-md-block text-center">
        <div class="vertical-line"></div>
    </div>
    <div class="col-md-5">
        <h2>Login</h2>
        <form method="POST">
            <div class="form-group">
                <label for="login-username-email">Username/Email</label>
                <input type="text" class="form-control" id="login-username-email" name="login-username-email" placeholder="Enter username or email" required>
            </div>
            <div class="form-group">
                <label for="login-password">Password</label>
                <input type="password" class="form-control" id="login-password" name="login-password" placeholder="Password" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary">Login</button>
        </form>
    </div>
</div>