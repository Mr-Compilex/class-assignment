<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: shop.php");
    exit;
}

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Product not found.</div></div>";
    require_once 'includes/footer.php';
    exit;
}
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['name']); ?></li>
  </ol>
</nav>

<div class="row">
    <div class="col-md-6 mb-4">
        <img src="assets/images/products/<?php echo $product['image']; ?>" class="img-fluid rounded shadow-sm" alt="<?php echo htmlspecialchars($product['name']); ?>">
    </div>
    <div class="col-md-6">
        <h1 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
        <p class="text-muted mb-2">Category: <a href="shop.php?category=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></a></p>
        <h3 class="text-success mb-3">$<?php echo $product['price']; ?></h3>
        <p class="lead"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        
        <?php if ($product['stock'] > 0): ?>
            <form action="cart.php" method="POST" class="d-flex align-items-center mt-4">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="number" name="quantity" class="form-control w-25 me-3" value="1" min="1" max="<?php echo $product['stock']; ?>">
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-cart-plus me-2"></i>Add to Cart</button>
            </form>
            <p class="text-muted mt-2"><small><?php echo $product['stock']; ?> items in stock</small></p>
        <?php
else: ?>
            <div class="alert alert-warning">Out of Stock</div>
        <?php
endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
