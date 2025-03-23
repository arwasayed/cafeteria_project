<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'Auth.php';
include_once 'header2.php';
include_once 'config.php';
include_once 'database.php';
include_once 'business_logic.php';

$dbConfig = new DatabaseConfig();
$db = new Database($dbConfig->getHost(), $dbConfig->getUser(), $dbConfig->getPass(), $dbConfig->getDbName());
$productObj = new Product($db);

if (!isset($_GET['P_id']) && !isset($_POST['P_id'])) {
    header("Location: products.php?error=Product ID is required.");
    exit();
}

$productId = isset($_GET['P_id']) ? (int)$_GET['P_id'] : (int)$_POST['P_id'];
$product = $productObj->getById($productId);

if (!$product) {
    header("Location: products.php?error=Product not found.");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? '';
    $imagePath = $product['image_path']; 

    if (empty($name) || empty($price)) {
        header("Location: update_product.php?P_id=$productId&error=Please fill in all required fields.");
        exit();
    }

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "images/";
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $imagePath = $targetFilePath; 
        } else {
            die("Error uploading file: " . $_FILES['image']['error']);
        }
    }

    $updated = $productObj->update($productId, $name, $price, $imagePath);

    if ($updated) {
        header("Location:Products.php");
        exit();
    } else {
        header("Location: update_product.php?P_id=$productId&error=Failed to update product. Please try again.");
        exit();
    }
}
?>

<div class="container">
    <h1 class="text-center mb-4">Update Product</h1>

    <?php if (isset($_GET['success'])) : ?>
        <div class="alert alert-success text-center fw-bold" style="font-size: 18px;">
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php elseif (isset($_GET['error'])) : ?>
        <div class="alert alert-danger text-center fw-bold" style="font-size: 18px;">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <form action="update_product.php?P_id=<?= $productId ?>" method="POST" enctype="multipart/form-data" class="form1 text-center">
        <input type="hidden" name="P_id" value="<?= $productId ?>">
        <table class="product custom-table text-center">
            <tr>
                <th><label for="name">Product Name</label></th>
                <td><input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required></td>
            </tr>

            <tr>
                <th><label for="price">Price</label></th>
                <td>
                    <input type="number" id="price" name="price" class="form-control" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required> EGP
                </td>
            </tr>

            <tr>
                <th><label for="image">Product Image</label></th>
                <td>
                    <input type="file" id="image" name="image" class="form-control">
                    <br>
                    <?php if (!empty($product['image_path'])): ?>
                        <img src="images/<?= htmlspecialchars($product['image_path']) ?>" alt="Product Image" width="100">
                    <?php else: ?>
                        <img src="images/default.png" alt="Default" width="100">
                    <?php endif; ?>
                </td>
            </tr>

            <tr class="button-row">
                <td colspan="2">
                    <div class="text-center mt-4">
                        <button type="submit" name="update" class="btn btn-primary px-4 py-2.5 fs-5 rounded-0 d-inline-block">Update</button>
                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>

<?php include_once 'footer.php'; ?>
<?php ob_end_flush(); ?>
