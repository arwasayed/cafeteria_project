<?php
session_start();
require_once 'config.php';
require_once 'database.php';
require_once 'Auth.php';

$config = new DatabaseConfig();


$db = new Database($config);
$conn = $db->getConnection();


try {
    $stmt = $conn->prepare("SELECT u_id, name FROM User_Table WHERE role != 'admin'");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

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
    MAX(o.statuse) AS statuse,               
    MAX(o.note) AS notes,                    
    MAX(o.date) AS date,                     
    ANY_VALUE(u.name) AS user_name,          
    ur.room_number,                           
    SUM(oc.amount * p.price) AS order_total,  
    GROUP_CONCAT(CONCAT(oc.amount, ' ', p.name) SEPARATOR ', ') AS products  
    FROM Orders o
    JOIN User_Table u ON o.u_id = u.u_id
    JOIN User_Rooms ur ON u.u_id = ur.u_id  
    JOIN Order_Contents oc ON o.O_id = oc.o_id
    JOIN Products p ON oc.P_id = p.P_id
    GROUP BY o.O_id, ur.room_number   
    ORDER BY o.O_id DESC
    LIMIT 5;



  ");
  $stmt->execute();
  $latestOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Database Error: " . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $u_id = intval($_POST['user_id']);
    $statuse = 'processing'; 
    $note = htmlspecialchars(trim($_POST['notes']));
    $room_number = intval($_POST['room']);
    $products = $_POST['products'];

    try {
        $stmt = $conn->prepare("SELECT u_id FROM User_Table WHERE u_id = :u_id");
        $stmt->execute([':u_id' => $u_id]);
        $userExists = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userExists) {
            die("Error: User does not exist.");
        }
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }

    try {
        $stmt = $conn->prepare("
            INSERT INTO Orders (u_id, statuse, note)
            VALUES (:u_id, :statuse, :note)
        ");
        $stmt->execute([
            ':u_id' => $u_id,
            ':statuse' => $statuse,
            ':note' => $note
        ]);

        $order_id = $conn->lastInsertId();
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


        header("Location: Orders.php");
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

.latest_order .order-item .item-info {
    margin-bottom: 10px;
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

                        <!-- Hidden Total Amount Field -->
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
                        <br>
                        <div class="latest_order">
                                  <h4>Latest Orders</h4>
                                  <div class="items">
                                      <?php foreach ($latestOrders as $order): ?>
                                          <div class="order-item">
                                              <div class="item-info">
                                                  <h5>Order ID: <?php echo htmlspecialchars($order['order_id']); ?></h5>
                                                  <p>Status: <?php echo htmlspecialchars($order['statuse']); ?></p>
                                                  <p>Notes: <?php echo htmlspecialchars($order['notes']); ?></p>
                                                  <p>User: <?php echo htmlspecialchars($order['user_name']); ?></p>
                                                  <p>Room: <?php echo htmlspecialchars($order['room_number']); ?></p>
                                                  <p>Products: <?php echo htmlspecialchars($order['products']); ?></p> 
                                                  <p>Total Price: EGP <?php echo htmlspecialchars($order['order_total']); ?></p>
                                                  <p>Date: <?php echo htmlspecialchars($order['date']); ?></p>
                                              </div>
                                          </div>
                                      <?php endforeach; ?>
                                  </div>
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
