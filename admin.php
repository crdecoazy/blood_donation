<?php
include 'includes/header.php';

// Check if user is logged in and is an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Fetch stats for the dashboard
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'User'")->fetch_assoc()['count'];
$total_requests = $conn->query("SELECT COUNT(*) as count FROM blood_requests")->fetch_assoc()['count'];
$pending_requests = $conn->query("SELECT COUNT(*) as count FROM blood_requests WHERE status = 'Pending'")->fetch_assoc()['count'];
?>

<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="admin.php" class="list-group-item list-group-item-action active">Admin Dashboard</a>
            <a href="admin_users.php" class="list-group-item list-group-item-action">Manage Users</a>
            <a href="admin_requests.php" class="list-group-item list-group-item-action">Manage Requests</a>
            <a href="admin_inventory.php" class="list-group-item list-group-item-action">Manage Inventory</a>
        </div>
    </div>
    <div class="col-md-9">
        <h2>Admin Dashboard</h2>
        <hr>
        <div class="row">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Total Users</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_users; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Total Requests</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_requests; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Pending Requests</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $pending_requests; ?></h5>
                    </div>
                </div>
            </div>
        </div>
        <p>Welcome to the admin panel. From here you can manage users, blood requests, and the blood inventory.</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>