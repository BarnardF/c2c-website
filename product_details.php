<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

include('includes/header.php');

include('includes/error_msg.php');


//fetch id from url(ai helped with error msg - 3 June)
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($product_id <= 0) {
    echo "<div class='container'>";
    echo "<h2>Error</h2>";
    echo "<p>Invalid product ID.</p>";
    echo "<a href='browse_products.php'>Back to Browse Products</a>";
    echo "</div>";
    include('includes/footer.php');
    exit();
}


$stmt = $conn->prepare("SELECT p.*, u.username, c.category_name 
                        FROM products p 
                       JOIN users u ON p.user_id = u.user_id 
                       JOIN category c ON p.category_id = c.category_id 
                       WHERE p.product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();


if ($result && $result->num_rows > 0) {
    $product = $result->fetch_assoc();
    ?>

    <!-- Product card -->
    <div class="card">
        
        <div class="image">
            <img src="<?php echo htmlspecialchars($product['image_path']); ?>" 
                alt="<?php echo htmlspecialchars($product['title']); ?>">
        </div>
        
        <div class="caption">

            <p class="title"><?php echo htmlspecialchars($product['title']); ?></p>
            <p class="price">R<?php echo number_format($product['price'], 2); ?></p>
            <p class="category_name"><?php echo isset($product['category_name']) ? htmlspecialchars($product['category_name']) : 'Unknown'; ?></p>
            <p class="product_condition"><b>Condition: </b> <?php echo htmlspecialchars($product['product_condition']); ?></p>
            <p class="location"><b>Location: </b> <?php echo htmlspecialchars($product['location']); ?></p>
            <p class="username"><b>Seller: </b> <?php echo isset($product['username']) ? htmlspecialchars($product['username']) : 'Unknown'; ?></p>
            <p class="listing_created"><b><?php echo date("j M Y", strtotime($product['listing_created'])); ?></b></p>

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

            <p class="product_descr">
                <?php 
                $product_descr = htmlspecialchars($product['product_descr']);
                echo strlen($product_descr) > 100 ? substr($product_descr, 0, 100) . '...' : $product_descr; 
                ?>
            </p>

            <?php if (isset($_SESSION['user']) && $_SESSION['user_id'] == $product['user_id']): ?>
                <p class="ownership-status"><b>This is your listing</b></p>
            <?php endif; ?>
        
        </div>

        <!-- buttons -->
        <div class="button-container">
            <?php if (isset($_SESSION['user']) && $_SESSION['user_id'] != $product['user_id']): ?>
                <a href="checkout.php?id=<?php echo $product['product_id']; ?>">
                    Buy now - R<?php echo number_format($product['price'], 2); ?>
                </a>
            <?php elseif (!isset($_SESSION['user'])): ?>
                <a href="login.php">Login to buy</a>
            <?php endif; ?>
            
            <a href="browse_products.php">Back to scrolling</a>
        </div>

    </div>

    <?php
} else {
    ?>
    <div class="card">
        <div class="caption">
            <p class="title">SOLD</p>
            <p class="product_descr">You dont have it anymore</p>
        </div>
        <a href="browse_products.php">Back to browsing</a>
        <a href="index.php">Back home</a>
    </div>
    <?php
}

include('includes/footer.php');
?>
