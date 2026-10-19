<?php
session_start();

// Load database connection
require_once __DIR__ . '/config.php';

if (!isset($pdo)) {
    die("System Error: Database connection failed. Please check config.php");
}

// Verify admin login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// ========== CATEGORY MANAGEMENT ==========

// Add Category
if (isset($_POST['add_category'])) {
    $new_category = trim($_POST['new_category']);
    if (!empty($new_category)) {
        $check = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE category_name = ?");
        $check->execute([$new_category]);
        if ($check->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO categories (category_name) VALUES (?)");
            $stmt->execute([$new_category]);
            $cat_success = "Category '$new_category' added successfully!";
        } else {
            $cat_error = "Category already exists!";
        }
    }
}

// Edit Category
if (isset($_POST['edit_category'])) {
    $cat_id = $_POST['cat_id'];
    $cat_name = trim($_POST['cat_name']);
    if (!empty($cat_name)) {
        $stmt = $pdo->prepare("UPDATE categories SET category_name = ? WHERE category_id = ?");
        $stmt->execute([$cat_name, $cat_id]);
        $cat_success = "Category updated successfully!";
    }
}

// Delete Category
if (isset($_GET['delete_cat'])) {
    $cat_id = $_GET['delete_cat'];
    $check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category = (SELECT category_name FROM categories WHERE category_id = ?)");
    $check->execute([$cat_id]);
    if ($check->fetchColumn() > 0) {
        $cat_error = "Cannot delete category because it is used by some products!";
    } else {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE category_id = ?");
        $stmt->execute([$cat_id]);
        $cat_success = "Category deleted successfully!";
    }
}

// Get all categories
$categories_list = $pdo->query("SELECT * FROM categories ORDER BY category_name")->fetchAll();

if (empty($categories_list)) {
    $default_cats = ['Fiction', 'Comedy', 'Novels', 'Fantasy', 'Romance', 'Mystery', 'Travel', 'Notebook', 'Reference', 'Textbook'];
    foreach ($default_cats as $cat) {
        $pdo->prepare("INSERT INTO categories (category_name) VALUES (?)")->execute([$cat]);
    }
    $categories_list = $pdo->query("SELECT * FROM categories ORDER BY category_name")->fetchAll();
}

// ========== PRODUCT MANAGEMENT ==========

// Handle Delete
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    try {
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM order_items WHERE product_id = ?");
        $check_stmt->execute([$delete_id]);
        $exists_in_orders = $check_stmt->fetchColumn();
       
        if ($exists_in_orders > 0) {
            $error_msg = "Cannot delete this product because it has been ordered by customers.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
            $stmt->execute([$delete_id]);
            $success_msg = "Product deleted successfully!";
        }
    } catch (PDOException $e) {
        $error_msg = "Delete failed: " . $e->getMessage();
    }
}

// Handle Add/Edit
$edit_product = null;
if (isset($_GET['edit_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->execute([(int)$_GET['edit_id']]);
        $edit_product = $stmt->fetch();
    } catch (PDOException $e) {
        $error_msg = "Failed to load product: " . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $title = trim($_POST['title']);
    $author = trim($_POST['author'] ?? '');
    $price = (float)$_POST['price'];
    $category = $_POST['category'];
    
    // ========== IMAGE UPLOAD HANDLING ==========
    $image_url = '';
    
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $upload_dir = 'images/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        
        if (in_array($file_ext, $allowed)) {
            $new_filename = time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            $target_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_path)) {
                $image_url = $new_filename;
            }
        }
    }
    
    if ($edit_product && empty($image_url)) {
        $image_url = $edit_product['image_url'];
    }
    
    if (empty($image_url)) {
        $image_url = 'https://via.placeholder.com/150x200?text=Book+Cover';
    }
   
    try {
        if ($_POST['action'] === 'add') {
            $stmt = $pdo->prepare("INSERT INTO products (title, author, price, category, image_url) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $author, $price, $category, $image_url]);
            $success_msg = "Product added successfully!";
        }
        elseif ($_POST['action'] === 'edit' && isset($_POST['product_id'])) {
            $product_id = (int)$_POST['product_id'];
            $stmt = $pdo->prepare("UPDATE products SET title = ?, author = ?, price = ?, category = ?, image_url = ? WHERE product_id = ?");
            $stmt->execute([$title, $author, $price, $category, $image_url, $product_id]);
            $success_msg = "Product updated successfully!";
            $edit_product = null;
        }
    } catch (PDOException $e) {
        $error_msg = "Operation failed: " . $e->getMessage();
    }
}

// Get search keyword
$search_keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch products with search filter
if (!empty($search_keyword)) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE title LIKE ? OR author LIKE ? OR category LIKE ? ORDER BY category ASC, title ASC");
    $search_term = "%$search_keyword%";
    $stmt->execute([$search_term, $search_term, $search_term]);
    $products = $stmt->fetchAll();
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY category ASC, title ASC");
    $products = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - COM3 2O BUY</title>
    <link rel="icon" type="image/png" href="logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/inventory.css">
</head>
<body>

<div class="sidebar">
    <h3>COM3 2O BUY</h3>
    <div class="section-label">Admin Control</div>
    <a href="admin_dashboard.php" class="nav-item">
        <i class="fas fa-chart-line"></i> Dashboard
    </a>
    <a href="manage_users.php" class="nav-item">
        <i class="fas fa-users-cog"></i> Manage Users
    </a>
    <a href="inventory.php" class="nav-item active">
        <i class="fas fa-boxes-stacked"></i> Inventory
    </a>
    <a href="admin_orders.php" class="nav-item">
        <i class="fas fa-receipt"></i> Orders
    </a>

    <div class="section-label">My Account</div>
    <a href="profile.php" class="nav-item">
        <i class="fas fa-user-circle"></i> My Profile
    </a>
    <a href="order_history.php" class="nav-item">
        <i class="fas fa-clock-rotate-left"></i> My History
    </a>
    <a href="logout.php" class="nav-item logout-link">Logout</a>
</div>

<div class="main-content">
    <!-- PAGE HEADER WITH SEARCH AND BUTTONS -->
    <div class="page-header">
        <h1><i class="fas fa-boxes-stacked"></i> Inventory Management</h1>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <form method="GET" class="search-section">
                <input type="text" name="search" placeholder="🔍 Search by title, author, or category..." value="<?php echo htmlspecialchars($search_keyword); ?>">
                <button type="submit"><i class="fas fa-search"></i> Search</button>
                <?php if (!empty($search_keyword)): ?>
                    <a href="inventory.php" class="clear-search"><i class="fas fa-times"></i> Clear</a>
                <?php endif; ?>
            </form>
            <a href="#" class="add-category-btn" onclick="showAddCategoryModal()">
                <i class="fas fa-plus-circle"></i> Add Category
            </a>
            <a href="?add=true" class="add-btn">
                <i class="fas fa-plus-circle"></i> Add New Product
            </a>
        </div>
    </div>

    <?php if (isset($success_msg)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>
    <?php if (isset($error_msg)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>
    <?php if (isset($cat_success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($cat_success); ?></div>
    <?php endif; ?>
    <?php if (isset($cat_error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($cat_error); ?></div>
    <?php endif; ?>

    <?php if (!empty($search_keyword)): ?>
        <div class="search-result-info">
            <i class="fas fa-search"></i> Search results for: <strong>"<?php echo htmlspecialchars($search_keyword); ?>"</strong>
            (<?php echo count($products); ?> products found)
        </div>
    <?php endif; ?>

    <!-- CATEGORY TAGS SECTION -->
    <div class="category-tags-section">
        <h3><i class="fas fa-tags"></i> Categories</h3>
        <div class="category-list">
            <?php foreach ($categories_list as $cat): ?>
                <div class="category-tag">
                    <span><?php echo htmlspecialchars($cat['category_name']); ?></span>
                    <a href="#" onclick="editCategory(<?php echo $cat['category_id']; ?>, '<?php echo htmlspecialchars($cat['category_name']); ?>')" class="edit-cat">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="?delete_cat=<?php echo $cat['category_id']; ?>" class="delete-cat" onclick="return confirm('Delete this category?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ADD/EDIT PRODUCT FORM -->
    <?php if (isset($_GET['add']) || $edit_product): ?>
        <div class="form-card">
            <h3><?php echo $edit_product ? 'Edit Product' : 'Add New Product'; ?></h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?php echo $edit_product ? 'edit' : 'add'; ?>">
                <?php if ($edit_product): ?>
                    <input type="hidden" name="product_id" value="<?php echo $edit_product['product_id']; ?>">
                <?php endif; ?>
               
                <div class="form-grid">
                    <div class="form-group">
                        <label>Title *</label>
                        <input type="text" name="title" value="<?php echo $edit_product ? htmlspecialchars($edit_product['title']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Author</label>
                        <input type="text" name="author" value="<?php echo $edit_product ? htmlspecialchars($edit_product['author']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Price *</label>
                        <input type="number" step="0.01" name="price" value="<?php echo $edit_product ? $edit_product['price'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories_list as $cat): ?>
                                <option value="<?php echo $cat['category_name']; ?>" <?php echo ($edit_product && $edit_product['category'] == $cat['category_name']) ? 'selected' : ''; ?>>
                                    <?php echo $cat['category_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Product Image</label>
                        <input type="file" name="product_image" accept="image/jpeg,image/png,image/jpg,image/webp">
                        <small style="font-size: 11px; color: #888;">Upload JPG, PNG, or WEBP (Max 2MB)</small>
                        <?php if ($edit_product && !empty($edit_product['image_url']) && file_exists('images/' . $edit_product['image_url'])): ?>
                            <div style="margin-top: 8px;">
                                <img src="images/<?php echo $edit_product['image_url']; ?>" width="50" height="70" style="border-radius: 5px;">
                                <small>Current image</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
               
                <div>
                    <button type="submit" class="btn-submit"><?php echo $edit_product ? 'Update Product' : 'Add Product'; ?></button>
                    <a href="inventory.php" class="cancel-btn">Cancel</a>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- PRODUCTS TABLE -->
    <div class="products-table">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            <i class="fas fa-box-open" style="font-size: 2rem; color: #ccc;"></i>
                            <p>No products found.</p>
                            <?php if (!empty($search_keyword)): ?>
                                <p style="margin-top: 10px;">Try a different search term or <a href="inventory.php">clear search</a>.</p>
                            <?php else: ?>
                                <p>Click "Add New Product" to get started.</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['product_id']; ?></td>
                            <td>
                                <?php
                                $imgPath = 'images/' . $product['image_url'];
                                if (!empty($product['image_url']) && file_exists($imgPath)) {
                                    echo '<img src="' . $imgPath . '" class="product-img-small" alt="Cover">';
                                } else {
                                    echo '<div class="product-img-small" style="background: #92A8D1; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-book" style="font-size: 24px; color: white;"></i>
                                          </div>';
                                }
                                ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($product['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($product['author'] ?? '-'); ?></td>
                            <td><span class="category-badge"><?php echo htmlspecialchars($product['category']); ?></span></td>
                            <td style="color: var(--serenity-dark); font-weight: bold;">RM<?php echo number_format($product['price'], 2); ?></td>
                            <td class="action-buttons">
                                <a href="?edit_id=<?php echo $product['product_id']; ?>" class="edit-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="?delete_id=<?php echo $product['product_id']; ?>"
                                   class="delete-btn"
                                   onclick="return confirm('Are you sure you want to delete this product?');">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="modal">
    <div class="modal-content">
        <h4>Add New Category</h4>
        <form method="POST">
            <input type="text" name="new_category" placeholder="Enter category name..." required>
            <button type="submit" name="add_category" class="btn-submit" style="margin-top: 10px;">Add Category</button>
            <button type="button" onclick="closeAddModal()" class="cancel-btn">Cancel</button>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <h4>Edit Category</h4>
        <form method="POST">
            <input type="hidden" name="cat_id" id="edit_cat_id">
            <input type="text" name="cat_name" id="edit_cat_name" required>
            <button type="submit" name="edit_category" class="btn-submit">Save Changes</button>
            <button type="button" onclick="closeModal()" class="cancel-btn">Cancel</button>
        </form>
    </div>
</div>

<script>
    function showAddCategoryModal() {
        document.getElementById('addCategoryModal').style.display = 'flex';
    }
   
    function closeAddModal() {
        document.getElementById('addCategoryModal').style.display = 'none';
    }
   
    function editCategory(id, name) {
        document.getElementById('edit_cat_id').value = id;
        document.getElementById('edit_cat_name').value = name;
        document.getElementById('editModal').style.display = 'flex';
    }
   
    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }
   
    window.onclick = function(event) {
        let addModal = document.getElementById('addCategoryModal');
        let editModal = document.getElementById('editModal');
        if (event.target == addModal) {
            addModal.style.display = 'none';
        }
        if (event.target == editModal) {
            editModal.style.display = 'none';
        }
    }
</script>

</body>
</html>