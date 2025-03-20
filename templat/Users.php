<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'Auth.php';
require_once 'config.php';
require_once 'database.php';

$config = new DatabaseConfig();


$db = new Database($config);
$conn = $db->getConnection();

try {
    
    $stmt = $conn->prepare("
        SELECT u.u_id, u.name, u.email, u.image_path, u.role, 
               GROUP_CONCAT(ur.room_number SEPARATOR ', ') AS rooms, ur.EXT
        FROM User_Table u
        LEFT JOIN User_Rooms ur ON u.u_id = ur.u_id
        WHERE u.role != 'admin'  
        GROUP BY u.u_id, u.name, u.email, u.image_path, u.role, ur.EXT
        ORDER BY u.u_id DESC
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}


$base_path = './images/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Users</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans:400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Great+Vibes" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .containerr {
            padding: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        .table-header {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        .header__item {
            padding: 15px;
            text-align: left;
        }
        .filter__link {
            color: white;
            text-decoration: none;
            display: block;
        }
        .filter__link:hover {
            text-decoration: underline;
        }
        .table-content {
            background-color: white;
        }
        .table-row {
            border-bottom: 1px solid #ddd;
            transition: background-color 0.3s;
        }
        .table-row:hover {
            background-color: #f9f9f9;
        }
        .table-data {
            padding: 15px;
            text-align: left;
        }
        .table-data img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #007bff;
            transition: transform 0.3s;
        }
        .table-data img:hover {
            transform: scale(1.1);
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .action-buttons a {
            text-decoration: none;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .action-buttons a.edit {
            background-color: #007bff;
        }
        .action-buttons a.edit:hover {
            background-color: #0056b3;
        }
        .action-buttons a.delete {
            background-color: #dc3545;
        }
        .action-buttons a.delete:hover {
            background-color: #a71d2a;
        }
        .add-user-btn {
            float: right;
            margin-right: 30px;
            font-size: 20px;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        .add-user-btn:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include_once 'header2.php'; ?>

    <h1 class="head">All Users</h1>
    <div class="addProduct">
        <a class="add-user-btn" href="AddUser.php">Add User</a>
    </div>

    <div class="containerr">
        <div class="table">
            <div class="table-header">
                <div class="header__item"><a id="name" class="filter__link" href="#">Name</a></div>
                <div class="header__item"><a id="room" class="filter__link filter__link--number" href="#">Room</a></div>
                <div class="header__item"><a id="image" class="filter__link filter__link--number" href="#">Image</a></div>
                <div class="header__item"><a id="ext" class="filter__link filter__link--number" href="#">Ext</a></div>
                <div class="header__item"><a id="action" class="filter__link filter__link--number" href="#">Action</a></div>
            </div>
            <div class="table-content">
                <?php if (empty($users)): ?>
                    <div class="table-row">
                        <div class="table-data" colspan="5">No users found.</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <div class="table-row">
                            <div class="table-data"><?php echo htmlspecialchars($user['name']); ?></div>
                            <div class="table-data"><?php echo htmlspecialchars($user['rooms'] ?: 'None'); ?></div>
                            <div class="table-data">
                                <?php
                                $profile_picture = $user['image_path'];
                                $absolute_path = realpath(dirname(__FILE__)) . '/' . $profile_picture;
                                if ($profile_picture && file_exists($absolute_path)): ?>
                                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="<?php echo htmlspecialchars($user['name']); ?>">
                                <?php else: ?>
                                    <img src="./images/default.jpg" alt="Default User">
                                <?php endif; ?>
                            </div>
                            <div class="table-data"><?php echo htmlspecialchars($user['EXT'] ?: 'N/A'); ?></div>
                            <div class="table-data">
                                <div class="action-buttons">
                                    <a href="edit_user.php?id=<?php echo $user['u_id']; ?>" class="edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete_user.php?id=<?php echo $user['u_id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this user?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include_once 'footer.php'; ?>

</body>
</html>