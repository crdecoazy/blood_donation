<?php
include 'includes/header.php';

// Check if user is logged in and is a User
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $dob = $_POST['dob'];
    $contact = $_POST['contact'];
    $blood_group = $_POST['blood_group'];
    $willing_to_donate = $_POST['willing_to_donate'];
    $medical_condition = $_POST['medical_condition'];
    $last_donation_date = !empty($_POST['last_donation_date']) ? $_POST['last_donation_date'] : null;

    // Calculate eligibility date if last donation date is provided
    $eligible_date = null;
    if ($last_donation_date) {
        $eligible_date = date('Y-m-d', strtotime($last_donation_date . ' +3 months'));
    }

    $stmt = $conn->prepare("UPDATE user_profiles SET full_name = ?, dob = ?, contact = ?, blood_group = ?, willing_to_donate = ?, medical_condition = ?, last_donation_date = ?, eligible_date = ? WHERE user_id = ?");
    $stmt->bind_param("ssssssssi", $full_name, $dob, $contact, $blood_group, $willing_to_donate, $medical_condition, $last_donation_date, $eligible_date, $user_id);

    if ($stmt->execute()) {
        $success = 'Profile updated successfully!';
    } else {
        $error = 'Failed to update profile. Please try again.';
    }
    $stmt->close();
}

// Fetch user profile data
$stmt = $conn->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Donation eligibility logic
$is_eligible = false;
$today = date('Y-m-d');
if (empty($profile['eligible_date']) || $today >= $profile['eligible_date']) {
    $is_eligible = true;
}
?>

<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
            <a href="profile.php" class="list-group-item list-group-item-action active">Update Profile</a>
            <a href="request_blood.php" class="list-group-item list-group-item-action">Request Blood</a>
            <a href="request_history.php" class="list-group-item list-group-item-action">Request History</a>
        </div>
    </div>
    <div class="col-md-9">
        <h2>Your Profile</h2>
        <hr>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header">Donation Status</div>
            <div class="card-body">
                <h5 class="card-title">
                    <?php if ($is_eligible): ?>
                        <span class="text-success">✅ Eligible to Donate</span>
                    <?php else: ?>
                        <span class="text-danger">❌ On Cooldown</span>
                    <?php endif; ?>
                </h5>
                <?php if (!$is_eligible): ?>
                    <p class="card-text">You can donate again after: <strong><?php echo date('d M, Y', strtotime($profile['eligible_date'])); ?></strong></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Update Your Information</div>
            <div class="card-body">
                <form action="profile.php" method="post">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo htmlspecialchars($profile['full_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" name="dob" id="dob" class="form-control" value="<?php echo $profile['dob']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact Number</label>
                        <input type="text" name="contact" id="contact" class="form-control" value="<?php echo htmlspecialchars($profile['contact']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="blood_group">Blood Group</label>
                        <select name="blood_group" id="blood_group" class="form-control">
                            <option value="">Select Blood Group</option>
                            <?php $blood_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']; ?>
                            <?php foreach ($blood_groups as $bg): ?>
                                <option value="<?php echo $bg; ?>" <?php if ($profile['blood_group'] == $bg) echo 'selected'; ?>><?php echo $bg; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="willing_to_donate">Willing to Donate?</label>
                        <select name="willing_to_donate" id="willing_to_donate" class="form-control">
                            <option value="Yes" <?php if ($profile['willing_to_donate'] == 'Yes') echo 'selected'; ?>>Yes</option>
                            <option value="No" <?php if ($profile['willing_to_donate'] == 'No') echo 'selected'; ?>>No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="medical_condition">Medical Condition (if any)</label>
                        <textarea name="medical_condition" id="medical_condition" class="form-control"><?php echo htmlspecialchars($profile['medical_condition']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="last_donation_date">Last Donation Date</label>
                        <input type="date" name="last_donation_date" id="last_donation_date" class="form-control" value="<?php echo $profile['last_donation_date']; ?>">
                        <small class="form-text text-muted">Updating this will automatically set a 3-month cooldown period.</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>