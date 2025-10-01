<?php
include 'includes/header.php';

// Check if user is logged in and is a User
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user profile data
$stmt = $conn->prepare("SELECT full_name, blood_group, eligible_date FROM user_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($full_name, $blood_group, $eligible_date);
$stmt->fetch();
$stmt->close();

// Donation eligibility logic
$is_eligible = false;
$today = date('Y-m-d');
if ($eligible_date === null || $today >= $eligible_date) {
    $is_eligible = true;
}
?>

<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="dashboard.php" class="list-group-item list-group-item-action active">Dashboard</a>
            <a href="profile.php" class="list-group-item list-group-item-action">Update Profile</a>
            <a href="request_blood.php" class="list-group-item list-group-item-action">Request Blood</a>
            <a href="request_history.php" class="list-group-item list-group-item-action">Request History</a>
        </div>
    </div>
    <div class="col-md-9">
        <h2>Welcome, <?php echo htmlspecialchars($full_name); ?>!</h2>
        <hr>
        <h4>Your Dashboard</h4>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Donation Eligibility Status</h5>
                <p class="card-text">
                    <?php if ($is_eligible): ?>
                        <span class="text-success">✅ You are eligible to donate blood.</span>
                    <?php else: ?>
                        <span class="text-danger">❌ You are on a cooldown period.</span>
                        <br>
                        <small>You can donate again after: <?php echo date('d M, Y', strtotime($eligible_date)); ?></small>
                    <?php endif; ?>
                </p>
                <a href="profile.php" class="btn btn-primary">View/Update Profile</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>