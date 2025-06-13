<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

include('includes/header.php');

include('includes/error_msg.php');

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}


// $sql = "SELECT p.*, u.username, c.category_name 
//         FROM products p
//         JOIN users u ON p.user_id = u.user_id
//         JOIN category c ON p.category_id = c.category_id
//         WHERE p.status = 'available'";

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT p.*, c.category_name 
                        FROM products p
                        JOIN category c ON p.category_id = c.category_id
                        WHERE p.user_id = ? 
                        ORDER BY p.listing_created DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$products = $stmt->get_result();



// https://www.w3schools.com/php/php_mysql_delete.asp + ai helped, since its deleting the user AND their products
if (isset($_POST['delete_product']) && isset($_POST['product_id'])) {
    $product_id = (int) $_POST['product_id'];

    try {
        $check_stmt = $conn->prepare("SELECT COUNT(*) FROM purchases WHERE product_id = ?");
        $check_stmt->bind_param("i", $product_id);
        $check_stmt->execute();
        $check_stmt->bind_result($purchase_count);
        $check_stmt->fetch();
        $check_stmt->close();


        if ($purchase_count > 0) {
            $_SESSION['errors'] = ['This product has already been sold and wont be listed, dont worry.'];
        } else {
            $conn->begin_transaction();

            $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $conn->commit();
                $_SESSION['success'] = 'Product deleted successfully';
            } else {
                $conn->rollback();
                $_SESSION['errors'] = ['Failed to delete product.'];
            }

            $stmt->close();
        }

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['errors'] = ['Error: ' . $e->getMessage()];
    }

    redirect('user_listings.php');
}


?>

<!-- https://www.w3schools.com/howto/howto_css_product_card.asp -->
<h2>My Listings</h2>
<div class="card-container">
    <!-- Product cards -->
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
                    <p class="category_name"><?php echo htmlspecialchars($product['category_name']); ?></p>
                    <p class="product_condition"><b>Condition: </b> <?php echo htmlspecialchars($product['product_condition'] ?? 'N/A'); ?></p>
                    <p class="location"><b>Location: </b> <?php echo htmlspecialchars($product['location'] ?? 'Not specified'); ?></p>
                    <p class="listing_created"><b><?php echo date("j M Y", strtotime($product['listing_created'])); ?></b></p>
                    <p class="status"><b><?php echo ucfirst(htmlspecialchars($product['status'])); ?></b></p>

                    <p class="product_descr">
                        <?php 
                        $product_descr = htmlspecialchars($product['product_descr']);
                        echo strlen($product_descr) > 100 ? substr($product_descr, 0, 100) . '...' : $product_descr; 
                        ?>
                    </p>
                </div>


                <div class="card-actions">
                    <a href="product_details.php?id=<?php echo $product['product_id']; ?>">View Details</a>

                    <form method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this product?');">
                        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                        <button type="submit" name="delete_product" class="delete-btn">
                            Delete Product
                        </button>
                    </form>
                </div>


            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="card">
            <div class="caption">
                <p class="title">No Listings Found</p>
                <p class="product_descr">You didn't sell anything yet</p>
            </div>
            <a href="add_product.php">List your item</a>
        </div>
    <?php endif; ?>
</div>

<?php include('includes/footer.php'); ?>
