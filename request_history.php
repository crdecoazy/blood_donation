<?php
include 'includes/header.php';

// Check if user is logged in and is a User
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch request history
$stmt = $conn->prepare("SELECT blood_group, status, request_date, approval_date FROM blood_requests WHERE user_id = ? ORDER BY request_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$requests = $stmt->get_result();
$stmt->close();
?>

<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
            <a href="profile.php" class="list-group-item list-group-item-action">Update Profile</a>
            <a href="request_blood.php" class="list-group-item list-group-item-action">Request Blood</a>
            <a href="request_history.php" class="list-group-item list-group-item-action active">Request History</a>
        </div>
    </div>
    <div class="col-md-9">
        <h2>Your Blood Request History</h2>
        <hr>
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Blood Group</th>
                            <th>Status</th>
                            <th>Request Date</th>
                            <th>Approval/Rejection Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($requests->num_rows > 0): ?>
                            <?php while ($row = $requests->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['blood_group']); ?></td>
                                    <td>
                                        <?php
                                            $status = htmlspecialchars($row['status']);
                                            $badge_class = 'badge-secondary';
                                            if ($status == 'Approved') {
                                                $badge_class = 'badge-success';
                                            } elseif ($status == 'Rejected') {
                                                $badge_class = 'badge-danger';
                                            }
                                            echo "<span class='badge {$badge_class}'>{$status}</span>";
                                        ?>
                                    </td>
                                    <td><?php echo date('d M, Y', strtotime($row['request_date'])); ?></td>
                                    <td><?php echo $row['approval_date'] ? date('d M, Y', strtotime($row['approval_date'])) : 'N/A'; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">You have not made any requests yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>