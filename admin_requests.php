<?php
include 'includes/header.php';

// Check if user is logged in and is an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Handle approve/reject actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];
    $today = date('Y-m-d');

    if ($action == 'approve') {
        // Start transaction
        $conn->begin_transaction();

        try {
            // Get blood group from request
            $stmt = $conn->prepare("SELECT blood_group FROM blood_requests WHERE id = ?");
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $request = $result->fetch_assoc();
            $blood_group = $request['blood_group'];

            // Check inventory
            $inv_stmt = $conn->prepare("SELECT units_available FROM inventory WHERE blood_group = ?");
            $inv_stmt->bind_param("s", $blood_group);
            $inv_stmt->execute();
            $inv_result = $inv_stmt->get_result();
            $inventory = $inv_result->fetch_assoc();

            if ($inventory['units_available'] > 0) {
                // Update request status
                $update_req_stmt = $conn->prepare("UPDATE blood_requests SET status = 'Approved', approval_date = ? WHERE id = ?");
                $update_req_stmt->bind_param("si", $today, $request_id);
                $update_req_stmt->execute();

                // Decrement inventory
                $update_inv_stmt = $conn->prepare("UPDATE inventory SET units_available = units_available - 1 WHERE blood_group = ?");
                $update_inv_stmt->bind_param("s", $blood_group);
                $update_inv_stmt->execute();

                $conn->commit();
            } else {
                // Not enough in stock, reject the request
                $conn->rollback();
                $update_req_stmt = $conn->prepare("UPDATE blood_requests SET status = 'Rejected', approval_date = ? WHERE id = ?");
                $update_req_stmt->bind_param("si", $today, $request_id);
                $update_req_stmt->execute();
            }
        } catch (Exception $e) {
            $conn->rollback();
            // Optional: log error $e->getMessage()
        }

    } elseif ($action == 'reject') {
        $stmt = $conn->prepare("UPDATE blood_requests SET status = 'Rejected', approval_date = ? WHERE id = ?");
        $stmt->bind_param("si", $today, $request_id);
        $stmt->execute();
    }
    // Redirect to avoid form resubmission
    header("Location: admin_requests.php");
    exit();
}

// Fetch all blood requests
$sql = "SELECT br.id, u.name, br.blood_group, br.status, br.request_date
        FROM blood_requests br
        JOIN users u ON br.user_id = u.id
        ORDER BY br.status = 'Pending' DESC, br.request_date DESC";
$requests = $conn->query($sql);

?>

<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="admin.php" class="list-group-item list-group-item-action">Admin Dashboard</a>
            <a href="admin_users.php" class="list-group-item list-group-item-action">Manage Users</a>
            <a href="admin_requests.php" class="list-group-item list-group-item-action active">Manage Requests</a>
            <a href="admin_inventory.php" class="list-group-item list-group-item-action">Manage Inventory</a>
        </div>
    </div>
    <div class="col-md-9">
        <h2>Manage Blood Requests</h2>
        <hr>
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Requester Name</th>
                            <th>Blood Group</th>
                            <th>Request Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($requests->num_rows > 0): ?>
                            <?php while($row = $requests->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['blood_group']); ?></td>
                                    <td><?php echo date('d M, Y', strtotime($row['request_date'])); ?></td>
                                    <td>
                                        <?php
                                            $status = htmlspecialchars($row['status']);
                                            $badge_class = 'badge-secondary';
                                            if ($status == 'Approved') $badge_class = 'badge-success';
                                            if ($status == 'Rejected') $badge_class = 'badge-danger';
                                            echo "<span class='badge {$badge_class}'>{$status}</span>";
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($row['status'] == 'Pending'): ?>
                                            <form action="admin_requests.php" method="post" style="display:inline-block;">
                                                <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                                            </form>
                                            <form action="admin_requests.php" method="post" style="display:inline-block;">
                                                <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                                            </form>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No blood requests found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>