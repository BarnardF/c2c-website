<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

include('includes/header.php');

include('includes/error_msg.php');


if (!isset($_SESSION['user']) || !logged_in()) {
    header("Location: login.php");
    exit();
}


// product id from url
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// ai helped with error msg
if ($product_id <= 0) {
    echo "<div class='container'>";
    echo "<h2>Error</h2>";
    echo "<p>Invalid product ID.</p>";
    echo "<a href='browse_products.php'>Back to browsing</a>";
    echo "</div>";
    include('includes/footer.php');
    exit();
}
//get product data from db
$stmt = $conn->prepare("SELECT p.*, u.username, c.category_name 
                        FROM products p 
                        JOIN users u ON p.user_id = u.user_id 
                        JOIN category c ON p.category_id = c.category_id 
                        WHERE p.product_id = ? 
                        AND p.status = 'available'");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

// ai helped with error msg
if (!$result || $result->num_rows == 0) {
    echo "<div class='container'>";
    echo "<h2>Error</h2>";
    echo "<p>Product not found or no longer available.</p>";
    echo "<a href='browse_products.php'>Back to browsing</a>";
    echo "</div>";
    include('includes/footer.php');
    exit();
}

$product = $result->fetch_assoc();

// Check if user is trying to buy their own product
if ($_SESSION['user_id'] == $product['user_id']) {
    echo "<div class='container'>";
    echo "<h2>Error</h2>";
    echo "<p>You cannot purchase your own product.</p>";
    echo "<a href='product_details.php?id=" . $product_id . "'>Back to Product</a>";
    echo "</div>";
    include('includes/footer.php');
    exit();
}
?>


<h2>Checkout</h2>
<div class="card-container">
    <!-- Order summary -->
    <div class="card">

        <div class="image">
            <img src="<?php echo htmlspecialchars($product['image_path']); ?>" 
                alt="<?php echo htmlspecialchars($product['title']); ?>">
        </div>
        
        <div class="caption">
            <p class="title"><?php echo htmlspecialchars($product['title']); ?></p>
            <p class="username"><b>Seller: </b><?php echo htmlspecialchars($product['username']); ?></p>
            <p class="product_condition"><b>Condition: </b><?php echo htmlspecialchars($product['product_condition']); ?></p>
            <p class="location"><b>Location: </b><?php echo htmlspecialchars($product['location']); ?></p>

            <p class="shipping_options"> <b>
                <?php 
                $shipping = $product['shipping_options'];
                $shipping_display = [
                    'pickup' => 'Pickup Only',
                    'delivery' => 'Delivery Available',
                    'both' => 'Pickup or Delivery'
                ];
                echo isset($shipping_display[$shipping]) ? $shipping_display[$shipping] : ucfirst($shipping);
                ?>
                </b>
            </p>

            <p class="total">Total: R<?php echo number_format($product['price'], 2); ?></p>
        </div>

    </div>
</div>

<h2>Complete Purchase</h2>

<!-- Checkout -->
<div class="form_container">
    <h2 class="form-title">Complete Purchase</h2>
    <form action="includes/complete_purchase_logic.php" method="POST">
        
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
        
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Your first name"
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['first_name'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Your last name"
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['last_name'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Your Email Address</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com"
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['email'] ?? ''); ?>" required>
        </div>

        <button type="submit" class="button">Complete Purchase</button>
        
        <div class="links">
            No money? <a href="product_details.php?id=<?php echo $product_id; ?>">Cancel</a>
        </div>

    </form>
</div>

<script src="assets/js/scripts.js"></script>

<?php include('includes/footer.php'); ?>