<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once 'businesslogic.php';
include_once 'header2.php';

$businessLogic = new BusinessLogic();

if (!isset($_GET['id'])) {
    header("Location: Users.php");
    exit();
}
$user_id = intval($_GET['id']);

if (!$businessLogic->isUserExists($user_id)) {
    die("User does not exist.");
}

$user = $businessLogic->getUserById($user_id);

if (!$user) {
    header("Location: Users.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $room = htmlspecialchars(trim($_POST['Room']));
    $ext = htmlspecialchars(trim($_POST['Ext']));
    $file = $_FILES['img'];

    $result = $businessLogic->updateUser($user_id, $name, $email, $room, $ext, $file);
    if ($result === true) {
        header("Location: Users.php");
        exit();
    } else {
        $error = $result;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Edit User</title>
</head>
<body>

    <h1 class="text-center mb-4">Edit User</h1>

    <form method="post" class="form1 text-center row g-3 needs-validation" id="editForm" novalidate enctype="multipart/form-data">
        <table class="product custom-table text-center">
            <tr>
                <th><label for="name">User Name</label></th>
                <td>
                    <input type="text" class="form-control" id="name" name="name" required value="<?php echo htmlspecialchars($user['name']); ?>">
                    <div class="invalid-feedback">Please enter a username.</div>
                </td>
            </tr>

            <tr>
                <th><label for="email">Email</label></th>
                <td>
                    <div class="input-group has-validation">
                        <span class="input-group-text">@</span>
                        <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>">
                        <div class="invalid-feedback">Please enter a valid email.</div>
                    </div>
                </td>
            </tr>

            <tr>
                <th><label for="Rno">Room No</label></th>
                <td>
                    <input type="number" id="Rno" name="Room" class="form-control" required value="<?php echo htmlspecialchars($user['rooms']); ?>">
                    <div class="invalid-feedback">Please enter a room number.</div>
                </td>
            </tr>

            <tr>
                <th><label for="Ext">Ext</label></th>
                <td>
                    <input type="number" id="Ext" name="Ext" class="form-control" required value="<?php echo htmlspecialchars($user['EXT']); ?>">
                    <div class="invalid-feedback">Please enter an extension number.</div>
                </td>
            </tr>

            <tr>
                <th><label for="img">Profile Picture</label></th>
                <td>
                    <input type="file" id="img" name="img" class="form-control" accept="image/*">
                    <?php if ($user['image_path']): ?>
                        <img src="<?php echo htmlspecialchars($user['image_path']); ?>" alt="Current Profile Picture" style="width: 50px; height: 50px; border-radius: 50%; margin-top: 10px;">
                    <?php endif; ?>
                </td>
            </tr>

            <tr class="button-row">
                <td colspan="2">
                    <div class="text-center">
                        <input type="submit" class="btn btn-success px-5" value="Update">
                        <a href="Users.php" class="btn btn-secondary px-5">Cancel</a>
                    </div>
                </td>
            </tr>
        </table>
    </form>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php include_once 'footer.php'; ?>

    <script>
    document.getElementById('editForm').addEventListener('submit', function(event) {
        let valid = true; 
        event.preventDefault(); 

        const name = document.getElementById('name');
        const email = document.getElementById('email');
        const room = document.getElementById('Rno');
        const ext = document.getElementById('Ext');

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        function checkField(field) {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                valid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        }

        checkField(name);
        checkField(email);
        checkField(room);
        checkField(ext);

        if (!emailRegex.test(email.value)) {
            email.classList.add('is-invalid');
            valid = false;
        } else {
            email.classList.remove('is-invalid');
        }

        if (valid) {
            this.submit();
        }
    });
    </script>
</body>
</html>
