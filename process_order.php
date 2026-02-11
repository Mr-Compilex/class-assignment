<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id']) && !empty($_SESSION['cart'])) {
    $user_id = $_SESSION['user_id'];
    $total_amount = $_POST['total_amount'];
    $payment_method = $_POST['payment_method'];
    $shipping_address = $_POST['address']; // You might want to combine name/phone/address properly or just store address

    // Append name and phone to address for simplicity in this schema
    $full_shipping_info = "Name: " . $_POST['name'] . "\nPhone: " . $_POST['phone'] . "\nAddress: " . $shipping_address;

    try {
        $pdo->beginTransaction();

        // 1. Insert Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, payment_method, shipping_address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $total_amount, $payment_method, $full_shipping_info]);
        $order_id = $pdo->lastInsertId();

        // 2. Insert Order Items
        $ids = implode(',', array_keys($_SESSION['cart']));
        $stmt_products = $pdo->query("SELECT id, price FROM products WHERE id IN ($ids)");
        $products = $stmt_products->fetchAll(PDO::FETCH_KEY_PAIR); // [id => price]

        $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");

        foreach ($_SESSION['cart'] as $product_id => $qty) {
            if (isset($products[$product_id])) {
                $price = $products[$product_id];
                $stmt_item->execute([$order_id, $product_id, $qty, $price]);
            }
        }

        // 3. Clear Cart
        unset($_SESSION['cart']);

        $pdo->commit();

        // Redirect to success/history
        header("Location: user/orders.php?success=1");
        exit;

    }
    catch (Exception $e) {
        $pdo->rollBack();
        die("Order processing failed: " . $e->getMessage());
    }
}
else {
    header("Location: index.php");
    exit;
}
?>
