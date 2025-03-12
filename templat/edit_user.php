<?php
session_start();
require_once 'config.php';
require_once 'database.php';
require_once 'Auth.php';


$config = new DatabaseConfig();


$db = new Database($config);
$conn = $db->getConnection();


if (!isset($_GET['id'])) {
    header("Location: Users.php");
    exit();
}
$user_id = intval($_GET['id']);


try {
    $stmt = $conn->prepare("
        SELECT u.u_id, u.name, u.email, u.image_path, u.role, 
               GROUP_CONCAT(ur.room_number SEPARATOR ', ') AS rooms, ur.EXT
        FROM User_Table u
        LEFT JOIN User_Rooms ur ON u.u_id = ur.u_id
        WHERE u.u_id = :user_id
        GROUP BY u.u_id, u.name, u.email, u.image_path, u.role, ur.EXT
    ");
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: Users.php");
        exit();
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $room = htmlspecialchars(trim($_POST['Room']));
    $ext = htmlspecialchars(trim($_POST['Ext']));

 
    $profile_picture = $user['image_path'];
    if (isset($_FILES['img']) && $_FILES['img']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = './images/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $profile_picture = $upload_dir . uniqid() . '-' . basename($_FILES['img']['name']);
        if (!move_uploaded_file($_FILES['img']['tmp_name'], $profile_picture)) {
            $error = "Failed to upload profile picture.";
        }
    }

    try {
        $conn->beginTransaction();


        $stmt = $conn->prepare("
            UPDATE User_Table 
            SET name = :name, email = :email, image_path = :image_path 
            WHERE u_id = :user_id
        ");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':image_path' => $profile_picture,
            ':user_id' => $user_id
        ]);

        $stmt = $conn->prepare("
            UPDATE User_Rooms 
            SET room_number = :room_number, EXT = :ext 
            WHERE u_id = :user_id
        ");
        $stmt->execute([
            ':room_number' => $room,
            ':ext' => $ext,
            ':user_id' => $user_id
        ]);

        if ($conn->commit()) {
            header("Location: Users.php");
            exit();
        } else {
            $error = "Failed to commit transaction.";
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        $error = "Error updating user: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Edit User </title>
</head>
<body>
    <?php include_once 'header2.php'; ?>

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
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php include_once 'footer.php'; ?>

    <script>
    document.getElementById('editForm').addEventListener('submit', function(event) {
        let valid = true; 
        event.preventDefault(); 

        // Get input values
        const name = document.getElementById('name');
        const email = document.getElementById('email');
        const room = document.getElementById('Rno');
        const ext = document.getElementById('Ext');

        // Regex patterns
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Validate required fields
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

        // Validate email
        if (!emailRegex.test(email.value)) {
            email.classList.add('is-invalid');
            valid = false;
        } else {
            email.classList.remove('is-invalid');
        }

        // Submit the form if valid
        if (valid) {
            this.submit();
        }
    });
    </script>
</body>
</html>