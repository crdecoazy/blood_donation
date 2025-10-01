<?php include 'includes/header.php'; ?>

<div class="jumbotron text-center">
    <h1 class="display-4">Welcome to the Blood Bank Management System</h1>
    <p class="lead">Your one-stop solution for managing blood donations and requests.</p>
    <hr class="my-4">
    <p>Whether you are looking to donate blood or are in need, we are here to help.</p>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a class="btn btn-primary btn-lg" href="<?php echo $_SESSION['role'] === 'Admin' ? 'admin.php' : 'dashboard.php'; ?>" role="button">Go to Your Dashboard</a>
    <?php else: ?>
        <a class="btn btn-primary btn-lg" href="login.php" role="button">Login</a>
        <a class="btn btn-secondary btn-lg" href="register.php" role="button">Register</a>
    <?php endif; ?>
</div>

<div class="row">
    <div class="col-md-4">
        <h4>For Donors</h4>
        <p>Register with us, keep your profile updated, and see when you are eligible to donate next. Your contribution can save lives.</p>
    </div>
    <div class="col-md-4">
        <h4>For Recipients</h4>
        <p>Quickly request the blood group you need. Our system will process your request and you can track its status through your dashboard.</p>
    </div>
    <div class="col-md-4">
        <h4>Admin Control</h4>
        <p>Administrators have a comprehensive dashboard to manage users, approve requests, and maintain an accurate inventory of available blood units.</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>