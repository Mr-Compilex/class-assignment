<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Fetch Latest Products
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 8");
$latest_products = $stmt->fetchAll();
?>

<!-- Hero Section -->
<div class="bg-dark text-white p-5 mb-5 rounded text-center">
    <h1>Welcome to Mr. Compilex Electronic Store</h1>
    <p class="lead">Best Electronics at Best Prices</p>
    <a href="shop.php" class="btn btn-primary btn-lg mt-3">Shop Now</a>
</div>

<!-- Latest Products -->
<h2 class="mb-4 text-center">Latest Arrivals</h2>
<div class="row">
    <?php foreach ($latest_products as $product): ?>
    <div class="col-md-3 mb-4">
        <div class="card h-100">
            <img src="assets/images/products/<?php echo $product['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>" style="height: 200px; object-fit: cover;">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                <p class="card-text text-success fw-bold">$<?php echo $product['price']; ?></p>
                <div class="mt-auto">
                    <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary w-100">View Details</a>
                </div>
            </div>
        </div>
    </div>
    <?php
endforeach; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
