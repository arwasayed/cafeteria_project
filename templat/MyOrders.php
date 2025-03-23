<?php
session_start();
require_once 'config.php';
require_once 'database.php';
require_once 'MyOrdersBusinessLogic.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$config = new DatabaseConfig();
$db = new Database($config);

$orderObj = new Order($db);
$userObj = new User($db);

$userId = $_SESSION['user_id'];
$user = $userObj->getUserById($userId);

if (!$user) {
    die("Error: User not found.");
}


$dateFrom = $_GET['dateFrom'] ?? '';
$dateTo = $_GET['dateTo'] ?? '';
$errors = [];


if (!empty($dateFrom) && !empty($dateTo) && $dateFrom > $dateTo) {
    $errors[] = "The 'Date from' cannot be later than 'Date to'.";
}


$orders = empty($errors) ? $orderObj->getOrders($userId, $dateFrom, $dateTo) : [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>My Orders</title>
</head>
<style>
.my_orders_section {
    margin-top: 20px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.05);
}
.toggle-details {
    cursor: pointer;
    font-weight: bold;
    color: blue;
}
.order-details {
    margin-left: 20px;
    display: none;
}
.order_items {
    display: flex;
    gap: 15px;
}
.order_items .item {
    display: flex;
    align-items: center;
    gap: 10px;
}
.order_items img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 5px;
}
.total {
    margin-top: 10px;
    font-size: 16px;
    font-weight: bold;
}
</style>
<body>
    <?php include_once 'userheader2.php'; ?>


    <section class="my_orders_section layout_padding">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="section_heading">My Orders</h2>
                </div>
            </div>

            
            <form method="GET">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label for="dateFrom" class="col-sm-3 col-form-label">Date from</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="dateFrom" name="dateFrom" value="<?= htmlspecialchars($dateFrom) ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="dateTo" class="col-sm-3 col-form-label">Date to</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="dateTo" name="dateTo" value="<?= htmlspecialchars($dateTo) ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </div>
            </form>

            
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Order Date</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($orders)): ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>
                                            <?= htmlspecialchars($order['order_date']) ?>
                                            <span class="toggle-details" data-toggle="+">+</span>
                                            <div class="order-details">
                                                <div class="row order_details">
                                                    <div class="col-md-12">
                                                        <div class="order_items">
                                                            <?php
                                                            $products = explode(', ', $order['products']);
                                                            foreach ($products as $product) {
                                                                list($quantity, $name) = explode(' x ', $product);
                                                                echo '
                                                                    <div class="item">
                                                                        <img src="images/default_product.png" alt="' . htmlspecialchars($name) . '">
                                                                        <div class="item_info">
                                                                            <h5>' . htmlspecialchars($name) . '</h5>
                                                                            <p>Quantity: ' . htmlspecialchars($quantity) . '</p>
                                                                        </div>
                                                                    </div>
                                                                ';
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="total">
                                                            <h4>Total: EGP <?= htmlspecialchars($order['total_price']) ?></h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($order['status']) ?></td>
                                        <td>EGP <?= htmlspecialchars($order['total_price']) ?></td>
                                        <td>
                                            <?php if ($order['status'] === 'processing'): ?>
                                                <button class="btn btn-danger cancel-order" data-order-id="<?= $order['order_id'] ?>">CANCEL</button>
                                            <?php else: ?>
                                                <button class="btn btn-secondary" disabled>CANCEL</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4">No orders found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleDetails = document.querySelectorAll('.toggle-details');

        toggleDetails.forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                const orderDetails = this.nextElementSibling;
                if (orderDetails.style.display === "none") {
                    orderDetails.style.display = "block";
                    this.setAttribute('data-toggle', '-');
                    this.textContent = '-';
                } else {
                    orderDetails.style.display = "none";
                    this.setAttribute('data-toggle', '+');
                    this.textContent = '+';
                }
            });
        });

        
        const cancelButtons = document.querySelectorAll('.cancel-order');
        cancelButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const orderId = this.getAttribute('data-order-id');
                if (confirm('Are you sure you want to cancel this order?')) {
                    fetch('cancel_order.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ order_id: orderId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Order canceled successfully.');
                            location.reload();
                        } else {
                            alert('Failed to cancel order.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while canceling the order.');
                    });
                }
            });
        });
    });
    </script>

    <?php include_once 'footer.php'; ?>
</body>
</html>
