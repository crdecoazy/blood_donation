<?php
include 'includes/header.php';

// Check if user is logged in and is an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Fetch all users and their profiles
$sql = "SELECT u.id, u.name, u.email, up.full_name, up.blood_group, up.willing_to_donate, up.eligible_date
        FROM users u
        LEFT JOIN user_profiles up ON u.id = up.user_id
        WHERE u.role = 'User'";
$users = $conn->query($sql);
?>

<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="admin.php" class="list-group-item list-group-item-action">Admin Dashboard</a>
            <a href="admin_users.php" class="list-group-item list-group-item-action active">Manage Users</a>
            <a href="admin_requests.php" class="list-group-item list-group-item-action">Manage Requests</a>
            <a href="admin_inventory.php" class="list-group-item list-group-item-action">Manage Inventory</a>
        </div>
    </div>
    <div class="col-md-9">
        <h2>Manage Users</h2>
        <hr>
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Blood Group</th>
                            <th>Willing to Donate</th>
                            <th>Eligibility</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users->num_rows > 0): ?>
                            <?php while($row = $users->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['full_name'] ?: $row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['blood_group'] ?: 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($row['willing_to_donate']); ?></td>
                                    <td>
                                        <?php
                                            $is_eligible = false;
                                            $today = date('Y-m-d');
                                            if (empty($row['eligible_date']) || $today >= $row['eligible_date']) {
                                                $is_eligible = true;
                                            }
                                            echo $is_eligible ? '<span class="text-success">Eligible</span>' : '<span class="text-danger">On Cooldown</span>';
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>