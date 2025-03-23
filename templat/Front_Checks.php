<?php require_once 'Back_Checks.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Checks</title>
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
</head>
<body>

    <section class="manual_order_section">
        <div class="container">
            <h2>Manual Order</h2>

            
            <?php if (!empty($errors)): ?>
                <div class="error-messages">
                    <?php foreach ($errors as $error): ?>
                        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form id="filterForm" method="GET">
                <label>Date from</label>
                <input type="date" name="dateFrom" value="<?= htmlspecialchars($dateFrom) ?>">
                
                <label>Date to</label>
                <input type="date" name="dateTo" value="<?= htmlspecialchars($dateTo) ?>">

                <label>User</label>
                <select name="userSelect">
                    <option value="">All Users</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['u_id'] ?>" <?= ($userSelect == $u['u_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Apply Filters</button>
            </form>

            <table>
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
                        <th>Actions</th>
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
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                        <button type="submit" name="action" value="approve">Approve</button>
                                        <button type="submit" name="action" value="reject">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9">No orders found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

</body>
</html>
