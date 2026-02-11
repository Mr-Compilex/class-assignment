<?php
require_once 'includes/header.php';

// Handle Delete Product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Get image path to delete file
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product && $product['image']) {
        $image_path = '../assets/images/products/' . $product['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: products.php");
    exit;
}

// Fetch all products
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
$products = $stmt->fetchAll();
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2>Manage Products</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="product_form.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add New Product</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td>
                        <?php if ($product['image']): ?>
                            <img src="../assets/images/products/<?php echo $product['image']; ?>" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover;">
                        <?php
    else: ?>
                            <span class="text-muted">No Image</span>
                        <?php
    endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                    <td>$<?php echo $product['price']; ?></td>
                    <td><?php echo $product['stock']; ?></td>
                    <td>
                        <a href="product_form.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <a href="products.php?delete=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php
endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
