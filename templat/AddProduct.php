<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'Auth.php';
include_once 'header2.php';

include_once 'config.php';
include_once 'database.php';
include_once 'business_logic.php';


$dbConfig = new DatabaseConfig();
$db = new Database($dbConfig->getHost(), $dbConfig->getUser(), $dbConfig->getPass(), $dbConfig->getDbName());
$productObj = new Product($db);


$categories = $db->selectAll("Category");
?>

<div class="container">
    <h1 class="text-center mb-4">Add New Product</h1>

    
    <?php if (isset($_GET['success'])) : ?>
        <div class="alert alert-success text-center fw-bold" style="font-size: 18px;">
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php elseif (isset($_GET['error'])) : ?>
        <div class="alert alert-danger text-center fw-bold" style="font-size: 18px;">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <form action="validation.php" method="post" class="form1 text-center" enctype="multipart/form-data">
        <table class="product custom-table text-center">
            <tr>
                <th><label for="product">Product Name</label></th>
                <td><input type="text" id="product" name="product" class="form-control" placeholder="Enter product name" required></td>
            </tr>

            <tr>
                <th><label for="price">Price</label></th>
                <td>
                    <input type="number" id="price" name="price" class="form-control" step="0.01" required> EGP
                </td>
            </tr>

            <tr>
                <th><label for="dropdown">Category</label></th>
                <td>
                    <select id="dropdown" name="category" class="form-select" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $category) : ?>
                            <option value="<?= htmlspecialchars($category['c_id']) ?>">
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th><label for="img">Product Image</label></th>
                <td>
                    <div style="display: flex; align-items: center;">
                        <input type="file" id="img" name="img" class="form-control file-input" accept="image/*" required style="display: inline-block; width: auto; margin-right: 10px;">
                    </div>
                </td>
            </tr>
            
           
            <tr class="button-row">
                <td colspan="2">
                    <div class="text-center mt-4">
                        <input type="submit" class="btn btn-success text-white px-4 py-2.5 fs-5 rounded-0 d-inline-block mx-2" value="Save">
                        <input type="reset" class="btn btn-secondary text-white px-4 py-2.5 fs-5 rounded-0 d-inline-block mx-2" value="Reset">
                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>

<?php include_once 'footer.php'; ?>
