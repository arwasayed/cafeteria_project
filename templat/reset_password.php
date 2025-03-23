<?php
session_start();
require_once 'config.php';
require_once 'database.php';
require_once 'resetpasswordblogic.php';
require_once 'resetpasswordvalidation.php';

$error_message = "";
$success_message = "";

// Initialize business logic class
$resetPasswordLogic = new ResetPasswordLogic();

if (!isset($_GET['token'])) {
    die("Invalid reset link.");
}

$reset_token = $_GET['token'];
$user = $resetPasswordLogic->validateResetToken($reset_token);

if (!$user) {
    die("Invalid or expired reset link.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate passwords
    $validationError = ResetPasswordValidation::validatePasswords($new_password, $confirm_password);
    if ($validationError) {
        $error_message = $validationError;
    } else {
        // Reset password in the database
        if ($resetPasswordLogic->resetPassword($reset_token, $new_password)) {
            $success_message = "Your password has been reset successfully.";
        } else {
            $error_message = "An error occurred while resetting your password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="./css/log_in.css" />
    <title>Reset Password</title>
</head>
<body>
    
    <div class="container">
        <h1>Reset Password</h1>

        <?php if (!empty($error_message)): ?>
            <div class="message error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="message success-message"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <form id="resetPasswordForm" method="post">
            <div class="col-md-12">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" name="new_password" id="new_password" class="form-control" required />
                <div class="invalid-feedback">Please enter a valid password.</div>
            </div>

            <div class="col-md-12 mt-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required />
                <div class="invalid-feedback">Passwords do not match.</div>
            </div>

            <div class="col-9 mt-4 md-3">
                <button class="btn btn-success submit-btn-contact rounded-pill" type="submit">Reset Password</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById("resetPasswordForm").addEventListener("submit", function(event) {
            const newPassword = document.getElementById("new_password").value;
            const confirmPassword = document.getElementById("confirm_password").value;

            // Check if passwords match
            if (newPassword !== confirmPassword) {
                document.getElementById("confirm_password").classList.add("is-invalid");
                event.preventDefault();
                return;
            }

            // Optional: Add client-side password strength validation
            const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
            if (!passwordRegex.test(newPassword)) {
                document.getElementById("new_password").classList.add("is-invalid");
                event.preventDefault();
                return;
            }
        });
    </script>
</body>

</html>
