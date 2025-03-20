<?php
require_once 'Auth.php';
require_once 'config.php';
require_once 'database.php';

// Initialize database connection
$config = new DatabaseConfig();
$db = new Database($config);
$conn = $db->getConnection();

try {
    // Fetch users (excluding admin)
    $stmt = $conn->prepare("SELECT u_id, name FROM User_Table WHERE role != 'admin'");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

try {
    // Fetch available products
    $stmt = $conn->prepare("SELECT P_id, name, price FROM Products WHERE available = 'available'");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// Default filter values
$dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : '';
$dateTo = isset($_GET['dateTo']) ? $_GET['dateTo'] : '';
$userSelect = isset($_GET['userSelect']) ? intval($_GET['userSelect']) : '';

// Build the base query
$query = "
    SELECT 
        o.O_id AS order_id,                      
        MAX(o.statuse) AS statuse,               
        MAX(o.note) AS notes,                    
        MAX(o.date) AS date,                     
        u.name AS user_name,          
        o.room_number,  
        SUM(oc.amount * p.price) AS order_total,  
        GROUP_CONCAT(CONCAT(oc.amount, ' x ', p.name) SEPARATOR ', ') AS products  
    FROM Orders o
    JOIN User_Table u ON o.u_id = u.u_id  
    JOIN Order_Contents oc ON o.O_id = oc.o_id  
    JOIN Products p ON oc.P_id = p.P_id  
    WHERE 1=1
";

$params = [];

// Add date filters if provided
if (!empty($dateFrom)) {
    $query .= " AND o.date >= :dateFrom";
    $params[':dateFrom'] = $dateFrom . ' 00:00:00';
}
if (!empty($dateTo)) {
    $query .= " AND o.date <= :dateTo";
    $params[':dateTo'] = $dateTo . ' 23:59:59';
}

// Add user filter if provided
if (!empty($userSelect)) {
    $query .= " AND u.u_id = :u_id";
    $params[':u_id'] = $userSelect;
}

$query .= " GROUP BY o.O_id, o.room_number, u.name ORDER BY o.O_id DESC";

try {
    $stmt = $conn->prepare($query);
    foreach ($params as $key => &$value) {
        $stmt->bindParam($key, $value);
    }
    $stmt->execute();
    $filteredOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
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
    color: #333;
}
</style>
<body>
    <?php include_once 'header2.php'; ?>

    <section class="manual_order_section layout_padding">
        <div class="container">
            <h2 class="section_heading">Manual Order</h2>

            <!-- Filters -->
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

            <!-- Filtered Orders Table -->
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

        // Automatically submit the form when filters change
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