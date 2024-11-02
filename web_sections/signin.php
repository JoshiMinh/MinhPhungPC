<div class="row">
    <!-- Sign Up Section -->
    <div class="col-md-6">
        <h2>Sign Up</h2>
        <form>
            <div class="form-group">
                <label for="signup-username">Username</label>
                <input type="text" class="form-control" id="signup-username" placeholder="Enter username" required>
            </div>
            <div class="form-group">
                <label for="signup-email">Email address</label>
                <input type="email" class="form-control" id="signup-email" placeholder="Enter email" required>
            </div>
            <div class="form-group">
                <label for="signup-password">Password</label>
                <input type="password" class="form-control" id="signup-password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <label for="signup-dob">Date of Birth</label>
                <input type="date" class="form-control" id="signup-dob" required>
            </div>
            <button type="submit" class="btn btn-primary">Sign Up</button>
        </form>
    </div>

    <!-- Vertical Line -->
    <div class="col-md-1 d-none d-md-block text-center">
        <div class="vertical-line"></div>
    </div>

    <!-- Login Section -->
    <div class="col-md-5">
        <h2>Login</h2>
        <form>
            <div class="form-group">
                <label for="login-username-email">Username/Email</label>
                <input type="text" class="form-control" id="login-username-email" placeholder="Enter username or email" required>
            </div>
            <div class="form-group">
                <label for="login-password">Password</label>
                <input type="password" class="form-control" id="login-password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</div>

<style>
    .vertical-line {
        border-left: 2px solid #dee2e6; /* Bootstrap's default border color */
        height: 100%; /* Full height of the container */
        margin: 0 20px; /* Optional margin for spacing */
    }
</style>