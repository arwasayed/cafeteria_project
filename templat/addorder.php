<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("You are not logged in. Please log in to view your orders.");
}

require_once 'businessLogic/addorder.php';
$loggedInUserId = $_SESSION['user_id'];
$orderLogic = new OrderLogic($loggedInUserId);

$products = $orderLogic->getProducts();
$latestOrders = $orderLogic->getLatestOrders();
$rooms = $orderLogic->getRooms();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $note = $_POST['notes'];
    $room_number = intval($_POST['room']);
    $selectedProducts = $_POST['products'];

    $result = $orderLogic->createOrder($note, $room_number, $selectedProducts);

    if ($result['success']) {
        $_SESSION['success'] = "Order #{$result['order_id']} created successfully!";
        header("Location: addorder.php");
        exit();
    } else {
        $errors = $result['errors'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manual Order</title>
 <link rel="stylesheet" href="css/orders.css">   
 
</head>
<body>
    <?php include_once 'userheader2.php'; ?>
    <section class="manual_order_section layout_padding">
        <div class="container">
            <h2 class="section_heading">Manual Order</h2>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            <form id="orderForm" method="post">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="order_items">
                            <?php foreach ($products as $product): ?>
                                <div class="item">
                                    <label><?php echo htmlspecialchars($product['name']); ?></label>
                                    <input type="number" class="quantity" name="products[<?php echo $product['P_id']; ?>]" value="0" min="0">
                                    <span class="price">EGP <?php echo htmlspecialchars($product['price']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="notes">
                            <textarea name="notes" placeholder="Notes"></textarea>
                        </div>
                        <div class="room">
                            <label>Room</label>
                            <select name="room">
                                <option value="">Select Room</option>
                                <?php foreach ($rooms as $room): ?>
                                    <option value="<?php echo htmlspecialchars($room['room_number']); ?>">
                                        Room <?php echo htmlspecialchars($room['room_number']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="total">
                            <span>Total: EGP <span id="totalAmount">0</span></span>
                        </div>
                        <input type="hidden" name="total_amount" id="totalAmountHidden" value="0">
                        <button type="submit" class="btn btn-primary" id="confirmOrder">Confirm</button>
                    </div>
                    <div class="col-md-6">
                        <div class="latest_order">
                            <h4>Your Latest Orders</h4>
                            <div class="items">
                                <?php if (empty($latestOrders)): ?>
                                    <p>No recent orders found.</p>
                                <?php else: ?>
                                    <?php foreach ($latestOrders as $order): ?>
                                        <div class="order-item">
                                            <h5>Order ID: <?php echo htmlspecialchars($order['order_id']); ?></h5>
                                            <p>Status: <?php echo htmlspecialchars($order['status']); ?></p>
                                            <p>Notes: <?php echo htmlspecialchars($order['notes']); ?></p>
                                            <p>User: <?php echo htmlspecialchars($order['user_name']); ?></p>
                                            <p>Room: <?php echo htmlspecialchars($order['room_number'] ?? 'Not specified'); ?></p>
                                            <p>Products: <?php echo htmlspecialchars($order['products']); ?></p>
                                            <p>Total Price: EGP <?php echo htmlspecialchars($order['order_total']); ?></p>
                                            <p>Date: <?php echo htmlspecialchars($order['date']); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
<script src="js/orders.js"></script>
    <?php include_once 'footer.php'; ?>
</body>
</html>