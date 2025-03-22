<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'Auth.php';
include_once 'config.php';
include_once 'database.php'; 
include_once 'business_logic.php';  

$db = new Database(new DatabaseConfig());
$productObj = new Product($db);
$products = $productObj->getAll();
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
<?php include_once 'header2.php';?>

<h1 class="head">All Products</h1>

<div class="addProduct">
    <a style="float:right; margin-right:30px; font-size:20px; color:blue; text-decoration:underline;" href="AddProduct.php">
        Add Product
    </a>
</div>

<div class="containerr">
    <div class="table">
        <div class="table-header" style="background-color: #000; color: #fff;">
            <div class="header__item">Product ID</div>
            <div class="header__item">Product Name</div>
            <div class="header__item">Product Price</div>
            <div class="header__item">Product Image</div>
            <div class="header__item">Actions</div>
        </div>
        <div class="table-content">
            <?php foreach ($products as $product): ?>
                <div class='table-row'>
                    <div class='table-data'>
                        <?= isset($product['P_id']) ? htmlspecialchars($product['P_id']) : 'N/A' ?>
                    </div>
                    <div class='table-data'><?= htmlspecialchars($product['name']) ?></div>
                    <div class='table-data'><?= htmlspecialchars($product['price']) ?> EGP</div>
                    <div class="table-data">
                                <?php
                                $profile_picture = $product['image_path'];
                                $absolute_path = realpath(dirname(__FILE__)) . '/' . $profile_picture;
                                if ($profile_picture && file_exists($absolute_path)): ?>
                                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="<?php echo htmlspecialchars($user['name']); ?>">
                                <?php else: ?>
                                    <img src="./images/default.jpg" alt="Default User">
                                <?php endif; ?>
                            </div>
                    <div class='table-data' style='display: flex; gap: 5px; justify-content: center;'>
                        <!-- Update Link -->
                        <a href="update_product.php?P_id=<?= isset($product['P_id']) ? htmlspecialchars($product['P_id']) : '' ?>"
                           class="btn btn-primary" style="border-radius: 20px; padding: 5px 10px; font-size: 14px; text-decoration: none; color: white;">
                            Update
                        </a>

                        <!-- Delete Link -->
                        <a href="deleted_product.php?P_id=<?= isset($product['P_id']) ? htmlspecialchars($product['P_id']) : '' ?>"
                           class="btn btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this product?');"
                           style="border-radius: 20px; padding: 5px 10px; font-size: 14px; text-decoration: none; color: white;">
                            Delete
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?> 
