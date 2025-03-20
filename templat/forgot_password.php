<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'config.php';
require_once 'database.php';
require 'vendor/autoload.php';

use SendGrid\Mail\Mail;

$db = new Database(new DatabaseConfig());
$error_message = "";
$success_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $useremail = trim($_POST['Email']);

    // Check if the email exists in the database
    $stmt = $db->getConnection()->prepare("SELECT * FROM User_Table WHERE email = :useremail");
    $stmt->bindParam(':useremail', $useremail);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate a reset token
        $reset_token = bin2hex(random_bytes(16));
        $expiry_time = date("Y-m-d H:i:s", strtotime("+24 hours"));
        // Save the token and expiry time in the database
        $stmt = $db->getConnection()->prepare("UPDATE User_Table SET reset_token = :token, reset_token_expiry = :expiry WHERE email = :useremail");
        $stmt->bindParam(':token', $reset_token);
        $stmt->bindParam(':expiry', $expiry_time);
        $stmt->bindParam(':useremail', $useremail);
        $stmt->execute();

        // Create the reset link
        $reset_link = "http://localhost/cafiteria_project/cafeteria_project-masternew/cafeteria_project-master/templat/reset_password.php?token=$reset_token";

        // Send the email using SendGrid
        $email = new Mail();
        $email->setFrom("arwasayed036@gmail.com", "arwa");
        $email->addTo($user['email'], $user['name']);
        $email->setSubject("Password Reset Request");
        $email->addContent("text/html", "Click the following link to reset your password: <a href='$reset_link'>$reset_link</a>");

        $sendgrid = new \SendGrid('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');
        try {
            $response = $sendgrid->send($email);
            if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
                $success_message = "A password reset link has been sent to your email.";
            } else {
                $error_message = "Failed to send the email. Status Code: " . $response->statusCode() . " - " . $response->body();
            }        
        } catch (Exception $e) {
            $error_message = "Failed to send the email. Error: " . $e->getMessage();
        }
    } else {
        $error_message = "No account found with this email address.";
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