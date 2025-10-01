<?php
include 'includes/header.php';

// Check if user is logged in and is an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Handle inventory update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['blood_group'])) {
    $blood_group = $_POST['blood_group'];
    $units = (int)$_POST['units'];

    if ($units >= 0) {
        $stmt = $conn->prepare("UPDATE inventory SET units_available = ? WHERE blood_group = ?");
        $stmt->bind_param("is", $units, $blood_group);
        $stmt->execute();
        $stmt->close();
    }
    // Redirect to avoid form resubmission
    header("Location: admin_inventory.php");
    exit();
}

// Fetch inventory data
$inventory = $conn->query("SELECT * FROM inventory ORDER BY blood_group");
?>

<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="admin.php" class="list-group-item list-group-item-action">Admin Dashboard</a>
            <a href="admin_users.php" class="list-group-item list-group-item-action">Manage Users</a>
            <a href="admin_requests.php" class="list-group-item list-group-item-action">Manage Requests</a>
            <a href="admin_inventory.php" class="list-group-item list-group-item-action active">Manage Inventory</a>
        </div>
    </div>
    <div class="col-md-9">
        <h2>Manage Blood Inventory</h2>
        <hr>
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Blood Group</th>
                            <th>Units Available</th>
                            <th>Update Units</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($inventory->num_rows > 0): ?>
                            <?php while($row = $inventory->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['blood_group']); ?></td>
                                    <td><?php echo $row['units_available']; ?></td>
                                    <td>
                                        <form action="admin_inventory.php" method="post" class="form-inline">
                                            <input type="hidden" name="blood_group" value="<?php echo $row['blood_group']; ?>">
                                            <div class="form-group">
                                                <input type="number" name="units" class="form-control form-control-sm" style="width: 80px;" value="<?php echo $row['units_available']; ?>" min="0">
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm ml-2">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>