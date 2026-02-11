<?php
require_once 'includes/header.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: orders.php");
    exit;
}

// Update Status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    $message = '<div class="alert alert-success">Order status updated successfully.</div>';
}

// Fetch Order Details
$stmt = $pdo->prepare("SELECT o.*, u.name, u.email, u.phone, u.address FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: orders.php");
    exit;
}

// Fetch Order Items
$stmt = $pdo->prepare("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt->execute([$id]);
$items = $stmt->fetchAll();
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2>Order Details #<?php echo $order['id']; ?></h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="orders.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Orders</a>
    </div>
</div>

<?php if (isset($message))
    echo $message; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">Items</div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <img src="../assets/images/products/<?php echo $item['image']; ?>" width="50" class="me-2">
                                <?php echo htmlspecialchars($item['name']); ?>
                            </td>
                            <td>$<?php echo $item['price']; ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                        <?php
endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Grand Total</td>
                            <td class="fw-bold">$<?php echo $order['total_amount']; ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">Customer Info</div>
            <div class="card-body">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($order['name'] ?? 'Guest'); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email'] ?? '-'); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone'] ?? '-'); ?></p>
                <p><strong>Shipping Address:</strong><br> <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Update Status</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Current Status: <strong><?php echo ucfirst($order['status']); ?></strong></label>
                        <select name="status" class="form-select">
                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="paid" <?php echo $order['status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" name="update_status" class="btn btn-primary w-100">Update Status</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
