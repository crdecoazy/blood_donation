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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $blood_group = $_POST['blood_group'];
    $patient_name = $_POST['patient_name'];
    $patient_condition = $_POST['patient_condition'];
    $patient_contact = $_POST['patient_contact'];
    $hospital_details = $_POST['hospital_details'];

    if (empty($blood_group) || empty($patient_name)) {
        $error = 'Blood group and patient name are required.';
    } else {
        // Insert the request into the database
        $stmt = $conn->prepare("INSERT INTO blood_requests (user_id, blood_group, patient_name, patient_condition, patient_contact, hospital_details) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $user_id, $blood_group, $patient_name, $patient_condition, $patient_contact, $hospital_details);

        if ($stmt->execute()) {
            $success = 'Your blood request has been submitted successfully.';
        } else {
            $error = 'Failed to submit request. Please try again.';
        }
        $stmt->close();
    }
}
?>

<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
            <a href="profile.php" class="list-group-item list-group-item-action">Update Profile</a>
            <a href="request_blood.php" class="list-group-item list-group-item-action active">Request Blood</a>
            <a href="request_history.php" class="list-group-item list-group-item-action">Request History</a>
        </div>
    </div>
    <div class="col-md-9">
        <h2>Request Blood</h2>
        <hr>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">Submit a New Request</div>
            <div class="card-body">
                <form action="request_blood.php" method="post">
                    <div class="form-group">
                        <label for="blood_group">Blood Group</label>
                        <select name="blood_group" id="blood_group" class="form-control" required>
                            <option value="">Select Blood Group</option>
                            <?php $blood_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']; ?>
                            <?php foreach ($blood_groups as $bg): ?>
                                <option value="<?php echo $bg; ?>"><?php echo $bg; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="patient_name">Patient Name</label>
                        <input type="text" name="patient_name" id="patient_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="patient_condition">Patient Condition</label>
                        <textarea name="patient_condition" id="patient_condition" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="patient_contact">Patient Contact</label>
                        <input type="text" name="patient_contact" id="patient_contact" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="hospital_details">Admitted Hospital Details</label>
                        <textarea name="hospital_details" id="hospital_details" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>