<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("You are not logged in. Please log in to view your orders.");
}
require_once 'config.php';
require_once 'database.php';
$config = new DatabaseConfig();
$db = new Database($config);
$conn = $db->getConnection();
$loggedInUserId = intval($_SESSION['user_id']);

try {
    
    $stmt = $conn->prepare("SELECT P_id, name, price FROM Products WHERE available = 'available'");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

try {
    
    $stmt = $conn->prepare("
        SELECT 
            o.O_id AS order_id,                      
            o.statuse AS status,               
            o.note AS notes,                    
            o.date AS date,                     
            u.name AS user_name,          
            o.room_number,  
            SUM(oc.amount * p.price) AS order_total,  
            GROUP_CONCAT(CONCAT(oc.amount, ' x ', p.name) SEPARATOR ', ') AS products  
        FROM Orders o
        JOIN User_Table u ON o.u_id = u.u_id  
        JOIN Order_Contents oc ON o.O_id = oc.o_id  
        JOIN Products p ON oc.P_id = p.P_id  
        WHERE o.u_id = :u_id
        GROUP BY o.O_id, o.room_number  
        ORDER BY o.O_id DESC
        LIMIT 5;
    ");
    $stmt->execute([':u_id' => $loggedInUserId]);
    $latestOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// Handle form submission for creating a new order
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $statuse = 'processing'; 
    $note = htmlspecialchars(trim($_POST['notes']));
    $room_number = intval($_POST['room']);
    $products = $_POST['products'];

    try {
        // Insert the new order into the Orders table
        $stmt = $conn->prepare("
            INSERT INTO Orders (u_id, statuse, note, room_number)
            VALUES (:u_id, :statuse, :note, :room_number)
        ");
        $stmt->execute([
            ':u_id' => $loggedInUserId,
            ':statuse' => $statuse,
            ':note' => $note,
            ':room_number' => !empty($room_number) ? $room_number : NULL
        ]);
        $order_id = $conn->lastInsertId();

        // Insert the selected products into the Order_Contents table
        foreach ($products as $product_id => $quantity) {
            if ($quantity > 0) {
                $stmt = $conn->prepare("
                    INSERT INTO Order_Contents (o_id, P_id, amount)
                    VALUES (:o_id, :P_id, :amount)
                ");
                $stmt->execute([
                    ':o_id' => $order_id,
                    ':P_id' => $product_id,
                    ':amount' => $quantity
                ]);
            }
        }

        // Redirect back to the same page after saving the order
        header("Location:addorder.php");
        exit();
    } catch (PDOException $e) {
        die("Error saving order: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manual Order</title>
    <link rel="stylesheet" href="path_to_your_styles.css">
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
}
</style>
<body>
    <?php include_once 'userheader2.php'; ?>
    <section class="manual_order_section layout_padding">
        <div class="container">
            <h2 class="section_heading">Manual Order</h2>
            <!-- Order Form -->
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
                                <?php
                                try {
                                    $stmt = $conn->prepare("SELECT DISTINCT room_number FROM User_Rooms WHERE u_id = :u_id");
                                    $stmt->execute([':u_id' => $loggedInUserId]);
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
                        <!-- Hidden Total Amount Field -->
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
                                            <div class="item-info">
                                                <h5>Order ID: <?php echo htmlspecialchars($order['order_id']); ?></h5>
                                                <p>Status: <?php echo htmlspecialchars($order['status']); ?></p>
                                                <p>Notes: <?php echo htmlspecialchars($order['notes']); ?></p>
                                                <p>User: <?php echo htmlspecialchars($order['user_name']); ?></p>
                                                <p>Room: <?php echo htmlspecialchars($order['room_number']); ?></p>
                                                <p>Products: <?php echo htmlspecialchars($order['products']); ?></p>
                                                <p>Total Price: EGP <?php echo htmlspecialchars($order['order_total']); ?></p>
                                                <p>Date: <?php echo htmlspecialchars($order['date']); ?></p>
                                            </div>
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderItems = document.querySelectorAll('.order_items .item');
        const totalAmountDisplay = document.getElementById('totalAmount');
        const totalAmountHidden = document.getElementById('totalAmountHidden');
        let totalAmount = 0;
        orderItems.forEach(function(item) {
            const quantityInput = item.querySelector('.quantity');
            const priceSpan = item.querySelector('.price');
            const price = parseFloat(priceSpan.textContent.replace('EGP ', ''));
            quantityInput.addEventListener('input', function() {
                updateTotal();
            });
        });
        function updateTotal() {
            totalAmount = 0;
            orderItems.forEach(function(item) {
                const quantityInput = item.querySelector('.quantity');
                const priceSpan = item.querySelector('.price');
                const price = parseFloat(priceSpan.textContent.replace('EGP ', ''));
                totalAmount += parseInt(quantityInput.value) * price;
            });
            totalAmountDisplay.textContent = totalAmount.toFixed(2);
            totalAmountHidden.value = totalAmount.toFixed(2); // Update hidden input
        }
    });
    </script>
    <?php include_once 'footer.php'; ?>
</body>
</html>
