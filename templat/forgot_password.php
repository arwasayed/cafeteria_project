<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once 'passwordValidation.php';
require_once 'passwordLogic.php';

$error_message = "";
$success_message = "";

$passwordLogic = new PasswordLogic();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $useremail = trim($_POST['Email']);

    // Validate email format
    if (!PasswordValidator::validateEmail($useremail)) {
        $error_message = "Invalid email format.";
    } else {
        // Check if the email exists in the database
        $user = $passwordLogic->checkEmailExists($useremail);

        if ($user) {
            // Generate reset token and expiry time
            $reset_token = bin2hex(random_bytes(16));
            $expiry_time = date("Y-m-d H:i:s", strtotime("+24 hours"));

            // Save reset token and expiry time in the database
            if ($passwordLogic->saveResetToken($useremail, $reset_token, $expiry_time)) {
                // Create the reset link
                $reset_link = "http://localhost/cafiteria_project/cafeteria_project-masternew/cafeteria_project-master/templat/reset_password.php?token=$reset_token";

                // Send the password reset email
                try {
                    $passwordLogic->sendResetEmail($user['email'], $user['name'], $reset_link);
                    $success_message = "A password reset link has been sent to your email.";
                } catch (Exception $e) {
                    $error_message = "Failed to send the email. Error: " . $e->getMessage();
                }
            } else {
                $error_message = "Failed to save reset token.";
            }
        } else {
            $error_message = "No account found with this email address.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="./css/log_in.css" />
    <title>Forgot Password</title>
</head>
<body>
    <div class="container">
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <h1>FORGOT PASSWORD</h1>
        <hr />
        <form id="forgotPasswordForm" method="post">
            <div class="col-md-12">
                <label for="validationCustomUsername" class="form-label">Email</label>
                <div class="input-group has-validation">
                    <span class="input-group-text" id="inputGroupPrepend">@</span>
                    <input type="email" name="Email" class="form-control" id="validationCustomUsername" aria-describedby="inputGroupPrepend" required />
                    <div class="invalid-feedback">Please enter a valid email.</div>
                </div>
            </div>

            <div class="col-9 mt-4 md-3">
                <button class="btn btn-primary submit-btn-contact rounded-pill" type="submit">Send Reset Link</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById("forgotPasswordForm").addEventListener("submit", function(event) {
            const formData = {
                email: document.getElementById("validationCustomUsername").value,
            };

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailRegex.test(formData.email)) {
                document.getElementById("validationCustomUsername").classList.add("is-invalid");
                event.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>
