<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Initialize Cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $product_id = $_POST['product_id'];

    if ($action == 'add') {
        $quantity = $_POST['quantity'];
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        }
        else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        echo "<script>window.location.href='cart.php';</script>";
        exit;
    }
    elseif ($action == 'update') {
        $quantity = $_POST['quantity'];
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        else {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    elseif ($action == 'remove') {
        unset($_SESSION['cart'][$product_id]);
    }
}

// Fetch Cart Products
$cart_items = [];
$total_price = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        $product['qty'] = $_SESSION['cart'][$product['id']];
        $product['subtotal'] = $product['price'] * $product['qty'];
        $total_price += $product['subtotal'];
        $cart_items[] = $product;
    }
}
?>

<h2 class="mb-4">Shopping Cart</h2>

<?php if (empty($cart_items)): ?>
    <div class="alert alert-info">Your cart is empty. <a href="shop.php">Start shopping</a></div>
<?php
else: ?>
    <div class="row">
        <div class="col-md-9">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td>
                            <img src="assets/images/products/<?php echo $item['image']; ?>" width="50" class="me-2">
                            <a href="product.php?id=<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['name']); ?></a>
                        </td>
                        <td>$<?php echo $item['price']; ?></td>
                        <td>
                            <form method="POST" class="d-flex">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['qty']; ?>" min="1" class="form-control form-control-sm w-50 me-2" onchange="this.form.submit()">
                            </form>
                        </td>
                        <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php
    endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">Cart Summary</div>
                <div class="card-body">
                    <h5 class="card-title">Total: $<?php echo number_format($total_price, 2); ?></h5>
                    <p class="card-text text-muted">Shipping calculated at checkout.</p>
                    <a href="checkout.php" class="btn btn-success w-100">Proceed to Checkout</a>
                    <a href="shop.php" class="btn btn-outline-secondary w-100 mt-2">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>
<?php
endif; ?>

<?php require_once 'includes/footer.php'; ?>
