<?php require_once 'Back_MyOrders.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>My Orders</title>
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
            <h2 class="section_heading">My Orders</h2>

            
            <?php if (!empty($errors)): ?>
                <div class="error-messages">
                    <?php foreach ($errors as $error): ?>
                        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="index.php" method="GET">
                <label>Date from:</label>
                <input type="date" name="dateFrom" value="<?= htmlspecialchars($dateFrom) ?>">
                <label>Date to:</label>
                <input type="date" name="dateTo" value="<?= htmlspecialchars($dateTo) ?>">
                <button type="submit">Apply Filters</button>
            </form>

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
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleDetails = document.querySelectorAll('.toggle-details');
        toggleDetails.forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                const orderDetails = this.nextElementSibling;
                orderDetails.style.display = orderDetails.style.display === "none" ? "block" : "none";
                this.textContent = this.textContent === "+" ? "-" : "+";
            });
        });
    });
    </script>

    <?php include_once 'footer.php'; ?>
</body>
</html>