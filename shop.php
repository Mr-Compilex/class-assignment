<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

$category_id = $_GET['category'] ?? null;

// Fetch Categories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// Fetch Products based on Category
if ($category_id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $category_name = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $category_name->execute([$category_id]);
    $current_category = $category_name->fetchColumn();
}
else {
    $stmt = $pdo->query("SELECT * FROM products");
    $current_category = 'All Products';
}
$products = $stmt->fetchAll();
?>

<div class="row">
    <!-- Sidebar -->
    <div class="col-md-3">
        <div class="list-group">
            <a href="shop.php" class="list-group-item list-group-item-action <?php echo !$category_id ? 'active' : ''; ?>">All Categories</a>
            <?php foreach ($categories as $cat): ?>
                <a href="shop.php?category=<?php echo $cat['id']; ?>" class="list-group-item list-group-item-action <?php echo $category_id == $cat['id'] ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            <?php
endforeach; ?>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="col-md-9">
        <h2 class="mb-4"><?php echo htmlspecialchars($current_category); ?></h2>
        
        <?php if (count($products) > 0): ?>
            <div class="row">
                <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="assets/images/products/<?php echo $product['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text text-muted small"><?php echo substr($product['description'], 0, 50); ?>...</p>
                            <p class="card-text text-success fw-bold">$<?php echo $product['price']; ?></p>
                            <div class="mt-auto">
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary w-100">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
    endforeach; ?>
            </div>
        <?php
else: ?>
            <div class="alert alert-info">No products found in this category.</div>
        <?php
endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
