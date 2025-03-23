<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once 'indexValidation.php';
require_once 'indexLogic.php';

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $useremail = trim($_POST['Email']);
    $password = trim($_POST['passwd']);

    // Validate input
    if (!InputValidator::validateEmail($useremail)) {
        $error_message = "Invalid email format.";
    } elseif (!InputValidator::validatePassword($password)) {
        $error_message = "Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number.";
    } else {
        // Authenticate user
        $authLogic = new AuthLogic();
        $user = $authLogic->authenticateUser($useremail, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['u_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];

            // Redirect based on user role
            if ($user['role'] === 'admin') {
                header('Location: home.php');
            } else {
                header('Location: userhome.php');
            }
            exit();
        } else {
            $error_message = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Log In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="./css/log_in.css" />
</head>
<body>
    <div class="container">
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <h1>LOG IN</h1>
        <hr />
        <form id="loginForm" method="post">
            <div class="col-md-12">
                <label for="validationCustomUsername" class="form-label">Email</label>
                <div class="input-group has-validation">
                    <span class="input-group-text" id="inputGroupPrepend">@</span>
                    <input type="email" name="Email" class="form-control" id="validationCustomUsername" aria-describedby="inputGroupPrepend" required />
                    <div class="invalid-feedback">Please enter a valid email.</div>
                </div>
            </div>

            <div class="col-md-12 mt-3">
                <label for="validationCustom03" class="form-label">Password</label>
                <input type="password" name="passwd" class="form-control" id="validationCustom03" required />
                <div class="invalid-feedback">The password that you've entered is incorrect.</div>
            </div>

            <div class="col-9 mt-4 md-3">
                <button class="btn btn-success submit-btn-contact rounded-pill" type="submit">Log in</button>
            </div>

            <div class="link col-12 mt-2">
                <a href="forgot_password.php" style="text-decoration: none">Forgot your password?</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById("loginForm").addEventListener("submit", function(event) {
            const formData = {
                email: document.getElementById("validationCustomUsername").value,
                password: document.getElementById("validationCustom03").value,
            };

            const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!passwordRegex.test(formData.password)) {
                document.getElementById("validationCustom03").classList.add("is-invalid");
                event.preventDefault();
                return;
            }

            if (!emailRegex.test(formData.email)) {
                document.getElementById("validationCustomUsername").classList.add("is-invalid");
                event.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>
