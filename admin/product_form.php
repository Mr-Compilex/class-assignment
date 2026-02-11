<?php
require_once 'includes/header.php';

$id = $_GET['id'] ?? null;
$product = null;
$errors = [];

// Fetch categories for dropdown
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// If editing, fetch product data
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if (!$product) {
        header("Location: products.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $category_id = $_POST['category_id'] ?: null;
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];
    $slug = strtolower(str_replace(' ', '-', $name)) . '-' . rand(1000, 9999); // Simple unique slug

    // Image Upload
    $image_name = $product['image'] ?? null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = '../assets/images/products/' . $new_filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_name = $new_filename;
                // Delete old image if exists
                if ($product && $product['image'] && file_exists('../assets/images/products/' . $product['image'])) {
                    unlink('../assets/images/products/' . $product['image']);
                }
            }
            else {
                $errors[] = "Failed to upload image.";
            }
        }
        else {
            $errors[] = "Invalid file type. Only JPG, PNG, GIF allowed.";
        }
    }

    if (empty($errors)) {
        if ($id) {
            // Update
            $sql = "UPDATE products SET name=?, category_id=?, price=?, stock=?, description=?, image=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $category_id, $price, $stock, $description, $image_name, $id]);
        }
        else {
            // Insert
            $sql = "INSERT INTO products (name, slug, category_id, price, stock, description, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $slug, $category_id, $price, $stock, $description, $image_name]);
        }
        header("Location: products.php");
        exit;
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <?php echo $id ? 'Edit Product' : 'Add New Product'; ?>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $err)
        echo $err . "<br>"; ?>
                    </div>
                <?php
endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo($product && $product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php
endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $product['price'] ?? ''; ?>" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock" class="form-control" value="<?php echo $product['stock'] ?? ''; ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="5"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        <input type="file" name="image" class="form-control">
                        <?php if ($product && $product['image']): ?>
                            <div class="mt-2">
                                <small>Current Image:</small><br>
                                <img src="../assets/images/products/<?php echo $product['image']; ?>" width="100">
                            </div>
                        <?php
endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary"><?php echo $id ? 'Update Product' : 'Save Product'; ?></button>
                    <a href="products.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
