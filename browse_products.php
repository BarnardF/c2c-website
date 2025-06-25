<?php 
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

include('includes/header.php'); 
include('includes/error_msg.php');

//ai helped with the session msg
// Display success message from session if available
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

// Display errors from session if any
if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
    echo '<div class="alert alert-danger">';
    foreach ($_SESSION['errors'] as $error) {
        echo "<p>$error</p>";
    }
    echo '</div>';
    unset($_SESSION['errors']);
}

// filter inputs - ai helped
$sql = "SELECT p.*, u.username, c.category_name 
        FROM products p
        JOIN users u ON p.user_id = u.user_id
        JOIN category c ON p.category_id = c.category_id
        WHERE p.status = 'available'";

$params = [];

$category_id = $_GET['category'] ?? '';
$sort_order = $_GET['sort'] ?? ''; 

if (!empty($category_id)) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
}
$sql .= " ORDER BY p.price " . ($sort_order === 'asc' ? 'ASC' : 'DESC');

$stmt = $conn->prepare($sql);


if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}


$stmt->execute();
$products = $stmt->get_result();
?>


<!-- Filters -->
<div class="form_container">
    <h2 class="form-title">Filter Products</h2>
    <form method="GET" action="browse_products.php">

        <div class="form-group">
            <label for="category">Category</label>
            <select name="category" id="category" class="form-control">
                <option value="">All Categories</option>
                <?php
                $catStmt = $conn->prepare("SELECT * FROM category");
                $catStmt->execute();
                $catResult = $catStmt->get_result();
                while ($cat = $catResult->fetch_assoc()) {
                    $selected = ($category_id == $cat['category_id']) ? 'selected' : '';
                    echo "<option value='{$cat['category_id']}' $selected>" . htmlspecialchars($cat['category_name']) . "</option>";
                }
                ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="sort">Sort by Price</label>
            <select name="sort" id="sort" class="form-control">
                <option value="">Select sorting</option>
                <option value="desc" <?php if ($sort_order == 'desc') echo 'selected'; ?>>Highest to Lowest</option>
                <option value="asc" <?php if ($sort_order == 'asc') echo 'selected'; ?>>Lowest to Highest</option>
            </select>
        </div>
        <div class="button-container">
            <button type="submit" class="button">Apply Filters</button>
        </div>
    </form>
</div>

<!-- https://www.w3schools.com/howto/howto_css_product_card.asp -->
<h2>Product Listings</h2>
<div class="card-container">
    <!--product card -->
    <?php if ($products->num_rows > 0): ?>
        <?php while ($product = $products->fetch_assoc()): ?>
            <div class="card">

                <div class="image">
                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" 
                        alt="<?php echo htmlspecialchars($product['title']); ?>">
                </div>
                
                <div class="caption">
                    <p class="title"><?php echo htmlspecialchars($product['title']); ?></p>
                    <p class="price">R<?php echo number_format($product['price'], 2); ?></p>
                    <!--<p class="category_name"><?php echo htmlspecialchars($product['category_name']); ?></p>-->
                    <!--<p class="product_condition"><b>Condition: </b><?php echo htmlspecialchars($product['product_condition'] ?? 'N/A'); ?></p>-->
                    
                    <!--<p class="username"><b>Seller: </b><?php echo htmlspecialchars($product['username']); ?></p>-->
                    
                    <p class="product_descr">
                        <?php 
                        $product_descr = htmlspecialchars($product['product_descr']);
                        echo strlen($product_descr) > 100 ? substr($product_descr, 0, 100) . '...' : $product_descr; 
                        ?>
                    </p>
                    <p class="location"><b>Location: </b><?php echo htmlspecialchars($product['location'] ?? 'Not specified'); ?></p>
                    <p class="listing_created"><b>Listed: </b><?php echo date('j M Y', strtotime($product['listing_created'])); ?></p>
                </div>

                <a href="product_details.php?id=<?php echo $product['product_id']; ?>">View Details</a>

            </div>

        <?php endwhile; ?>
    <?php else: ?>

        <div class="card">
            <div class="caption">
                <p class="title">No Products Found</p>
                <p class="product_descr">No products found. Try changing your filters.</p>
            </div>
            <a href="add_product.php">List a Product</a>
        </div>

    <?php endif; ?>
</div>


<?php include('includes/footer.php'); ?>