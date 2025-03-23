<?php
require_once 'config.php';
require_once 'Database.php';
require_once 'businessLogic/order.php';
require_once 'Auth.php';

$config = new DatabaseConfig();
$db = new Databases($config);
$orderManager = new OrderManager($db);
$error = "";

try {
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT u_id, name FROM User_Table WHERE role != 'admin'");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT P_id, name, price FROM Products WHERE available = 'available'");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $latestOrders = $orderManager->getLatestOrders(5);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $u_id = intval($_POST['user_id']);
    $statuse = 'processing'; 
    $note = trim($_POST['notes']);
    $room_number = intval($_POST['room']);
    $products = $_POST['products'];
    
    $result = $orderManager->createOrder($u_id, $room_number, $products, $note);
    if ($result['success']) {
        header("Location: Orders.php");
        exit();
    } else {
        $error = implode("<br>", $result['errors']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manual Order</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/orders.css">
</head>

<body>
    <?php include_once 'header2.php'; ?>
    <section class="manual_order_section layout_padding">
        <div class="container">
            <h2 class="section_heading">Manual Order</h2>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
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
                            <select name="room" required>
                                <option value="">Select Room</option>
                                <?php
                                try {
                                    $stmt = $conn->prepare("SELECT DISTINCT room_number FROM User_Rooms");
                                    $stmt->execute();
                                    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($rooms as $room) {
                                        echo "<option value='" . htmlspecialchars($room['room_number']) . "'>Room " . htmlspecialchars($room['room_number']) . "</option>";
                                    }
                                } catch (PDOException $e) {
                                    die("Database Error: " . $e->getMessage());
                                }
                                ?>
                            </select>
                        </div>
                        <div class="total">
                            <span>Total: EGP <span id="totalAmount">0</span></span>
                        </div>
                        <input type="hidden" name="total_amount" id="totalAmountHidden" value="0">
                        <button type="submit" class="btn btn-primary" id="confirmOrder">Confirm</button>
                    </div>
                    <div class="col-md-6">
                        <div class="add_to_user">
                            <h4>Add to user</h4>
                            <select id="userSelect" name="user_id" required>
                                <option value="">Select User</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['u_id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <br>
                        <div class="latest_order">
                            <h4>Latest Orders</h4>
                            <div class="items">
                                <?php if (empty($latestOrders)): ?>
                                    <p>No recent orders found.</p>
                                <?php else: ?>
                                    <?php foreach ($latestOrders as $order): ?>
                                        <div class="order-item">
                                            <p>Order ID: <?php echo htmlspecialchars($order['order_id']); ?></p>
                                            <p>User: <?php echo htmlspecialchars($order['user_name']); ?></p>
                                            <p>Room: <?php echo htmlspecialchars($order['room_number'] ?? 'Not specified'); ?></p>
                                            <p>Products: <?php echo htmlspecialchars($order['product_name']); ?></p>
                                            <p>Total Price: EGP <?php echo htmlspecialchars($order['order_total']); ?></p>
                                            <p>Date: <?php echo htmlspecialchars($order['order_date']); ?></p>
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