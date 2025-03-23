<?php
require_once 'checksBusinessLogic.php';
require_once 'Auth.php';

$config = new DatabaseConfig();
$db = new Database($config);

$order = new Order($db);
$user = new User($db);
$product = new Product($db);

$errors = [];
$dateFrom = $_GET['dateFrom'] ?? '';
$dateTo = $_GET['dateTo'] ?? '';
$userSelect = $_GET['userSelect'] ?? '';


if (!empty($dateFrom) && !empty($dateTo)) {
    if ($dateFrom > $dateTo) {
        $errors[] = "The 'Date from' cannot be later than 'Date to'.";
    }
}


$userIds = array_column($user->getUsers(), 'u_id'); 
if (!empty($userSelect) && !in_array($userSelect, $userIds)) {
    $errors[] = "Invalid user selected.";
}


$filteredOrders = empty($errors) ? $order->getOrders($dateFrom, $dateTo, $userSelect) : [];


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $orderId = intval($_POST['order_id']);
    $action = $_POST['action'];

    
    $validOrderIds = array_column($order->getOrders(), 'order_id');
    if (!in_array($orderId, $validOrderIds)) {
        $errors[] = "Invalid order ID.";
    } else {
        
        if ($action === 'approve') {
            $order->updateOrderStatus($orderId, 'Approved');
        } elseif ($action === 'reject') {
            $order->updateOrderStatus($orderId, 'Rejected');
        }
        header("Location: index.php");
        exit();
    }
}


$users = $user->getUsers();
$products = $product->getAvailableProducts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manual Order</title>
</head>
<style>
.latest_order {
    margin-top: 20px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.latest_order h4 {
    font-size: 20px;
    margin-bottom: 15px;
    font-weight: bold;
    color: #333;
}
.latest_order .items {
    display: flex;
    flex-direction: column;
    gap: 15px; 
}
.latest_order .order-item {
    background-color: #ffffff;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}
.latest_order .order-item:hover {
    background-color: #f1f1f1; 
}
.latest_order .order-item h5 {
    margin: 0;
    font-size: 18px;
    font-weight: bold;
    color: #333;
}
.latest_order .order-item p {
    margin: 5px 0;
    font-size: 14px;
    color: #555;
}
.latest_order .order-item p strong {
    font-weight: bold;
    color: #333;
}
</style>
<body>
    <?php include_once 'header2.php'; ?>

    <section class="manual_order_section layout_padding">
        <div class="container">
            <h2 class="section_heading">Manual Order</h2>

            
            <form id="filterForm" method="GET">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="dateFrom">Date from</label>
                        <input type="date" class="form-control" id="dateFrom" name="dateFrom" value="<?= htmlspecialchars($dateFrom) ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="dateTo">Date to</label>
                        <input type="date" class="form-control" id="dateTo" name="dateTo" value="<?= htmlspecialchars($dateTo) ?>">
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="userSelect">User</label>
                        <select class="form-control" id="userSelect" name="userSelect">
                            <option value="">All Users</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['u_id'] ?>" <?= ($user['u_id'] == $userSelect) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Apply Filters</button>
            </form>

            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th>User</th>
                            <th>Room</th>
                            <th>Products</th>
                            <th>Total Price</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($filteredOrders)): ?>
                            <?php foreach ($filteredOrders as $order): ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['order_id']) ?></td>
                                    <td><?= htmlspecialchars($order['statuse']) ?></td>
                                    <td><?= htmlspecialchars($order['notes']) ?></td>
                                    <td><?= htmlspecialchars($order['user_name']) ?></td>
                                    <td><?= htmlspecialchars($order['room_number']) ?></td>
                                    <td><?= htmlspecialchars($order['products']) ?></td>
                                    <td>EGP <?= htmlspecialchars($order['order_total']) ?></td>
                                    <td><?= htmlspecialchars($order['date']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8">No orders found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateFromInput = document.getElementById('dateFrom');
        const dateToInput = document.getElementById('dateTo');
        const userSelect = document.getElementById('userSelect');

        
        dateFromInput.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
        dateToInput.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
        userSelect.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
    </script>

    <?php include_once 'footer.php'; ?>
</body>
</html>
