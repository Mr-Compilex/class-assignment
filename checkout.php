<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='container mt-5'><div class='alert alert-warning'>Please <a href='login.php'>login</a> to proceed to checkout.</div></div>";
    require_once 'includes/footer.php';
    exit;
}

if (empty($_SESSION['cart'])) {
    header("Location: shop.php");
    exit;
}

// Calculate Total
$total_amount = 0;
$ids = implode(',', array_keys($_SESSION['cart']));
$stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
$products = $stmt->fetchAll();

foreach ($products as $product) {
    $total_amount += $product['price'] * $_SESSION['cart'][$product['id']];
}

// Fetch User Info for Pre-filling
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<div class="row">
    <div class="col-md-8">
        <h2 class="mb-4">Checkout</h2>
        <form action="process_order.php" method="POST">
            <div class="card mb-4">
                <div class="card-header">Shipping Information</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Shipping Address</label>
                        <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Payment Method</div>
                <div class="card-body">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" value="cod" checked>
                        <label class="form-check-label">Cash on Delivery</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" value="mobile_money">
                        <label class="form-check-label">Mobile Money</label>
                    </div>
                </div>
            </div>

            <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>">
            <button type="submit" class="btn btn-success btn-lg w-100">Place Order ($<?php echo number_format($total_amount, 2); ?>)</button>
        </form>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Order Summary</div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php foreach ($products as $product): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo htmlspecialchars($product['name']); ?> 
                        <span class="badge bg-primary rounded-pill"><?php echo $_SESSION['cart'][$product['id']]; ?></span>
                        <span>$<?php echo number_format($product['price'] * $_SESSION['cart'][$product['id']], 2); ?></span>
                    </li>
                    <?php
endforeach; ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                        Total
                        <span>$<?php echo number_format($total_amount, 2); ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
