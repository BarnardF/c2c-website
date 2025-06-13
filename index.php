<?php 
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

include('includes/header.php'); 

require_once('includes/error_msg.php');



$sql = "SELECT p.*, u.username, c.category_name FROM products p 
        JOIN users u ON p.user_id = u.user_id 
        JOIN category c ON p.category_id = c.category_id 
        WHERE p.status = 'available' 
        ORDER BY p.listing_created DESC";

$all_products = $conn->query($sql);


?>

<!-- homescreen welcome screen(used bootstrap) -->
<section>
    <div class="jumbotron">
        <div class="container">
            <h1 class="display-4">Welcome to <?php echo SITE_NAME ?></h1>
            <p class="lead">Your South African Customer-to-Customer E-commerce Platform.</p>
            <hr class="my-4">
            <p>Buy and sell items directly with other users in a safe and secure environment.</p>
        </div>
    </div>
</section>


<div class="redirect">
    <a href="add_product.php">Sell Item</a>
    <a href="browse_products.php">Browse Products</a>
    <?php if (isset($_SESSION['user'])): ?>
        <a href="user_listings.php">My Listings</a>
    <?php endif; ?>
</div>

<h2>Featured Products</h2>

<!-- https://www.w3schools.com/howto/howto_css_product_card.asp -->
<div class="card-container">

    <?php if (mysqli_num_rows($all_products) > 0): ?>

        <!-- Product cards -->
        <?php while($row = mysqli_fetch_assoc($all_products)): ?>

            <div class="card">
                <div class="image">
                    <img src="<?php echo htmlspecialchars($row["image_path"]) ?>" 
                         alt="<?php echo htmlspecialchars($row["title"]); ?>">
                </div>

                <div class="caption">
                    <p class="title"><?php echo htmlspecialchars($row["title"]) ?></p>
                    <p class="price">R<?php echo number_format($row["price"], 2) ?></p>
                    <p class="category_name"><?php echo htmlspecialchars($row["category_name"]) ?></p>
                    <p class="product_condition"><b>Condition: </b><?php echo htmlspecialchars($row["product_condition"]) ?></p>
                    <p class="location"><b>Location: </b><?php echo htmlspecialchars($row["location"]) ?></p>
                    <p class="username"><b>Seller: </b><?php echo htmlspecialchars($row["username"]) ?></p>
                    <p class="listing_created"><b><?php echo date("j M Y", strtotime($row['listing_created'])); ?></b></p>
                    <p class="product_descr">
                        <?php 
                        $product_descr = htmlspecialchars($row['product_descr']);
                        echo strlen($product_descr) > 100 ? substr($product_descr, 0, 100) . '...' : $product_descr; 
                        ?>
                    </p>
                </div>

                <a href="product_details.php?id=<?php echo $row['product_id'] ?>">View Details</a>
            </div>

        <?php endwhile; ?>
    <?php endif; ?>

</div>

<?php if (mysqli_num_rows($all_products) === 0): ?>

    <div class="no-products-msg" >
        <p><strong>No products available at the moment.</strong></p>
    </div>

<?php endif; ?>


<?php include('includes/footer.php'); ?>
