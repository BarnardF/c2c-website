<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';


include('includes/header.php'); 
include('includes/error_msg.php');



$is_admin_logged_in = logged_in() && ($_SESSION['is_admin'] == 1);
if (!$is_admin_logged_in) {
    $_SESSION['errors'] = ['You must be an administrator to access this page.'];
    redirect('login.php');
}

if (isset($_GET['admin_logout'])) {
    session_destroy();
    redirect('login.php');
}




$stats = [];
$user_product_summary = [];
$errors = [];

try {
    $stats = [
        'total_users'=> $conn->query("SELECT COUNT(*) FROM users WHERE is_admin = 0")->fetch_row()[0],
        'total_products'=> $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0],
        'available_products'=> $conn->query("SELECT COUNT(*) FROM products WHERE status = 'available'")->fetch_row()[0],
        'sold_products'=> $conn->query("SELECT COUNT(*) FROM products where status = 'sold'")->fetch_row()[0],
        'total_purchases'=> $conn->query("SELECT COUNT(*) From purchases")->fetch_row()[0],
    ];

    $summary_sql = "
        SELECT u.user_id, u.username, u.email,
            COUNT(p.product_id) AS total_products,
            COUNT(CASE WHEN p.status = 'available' THEN 1 END) AS available_products,
            COUNT(CASE WHEN p.status = 'sold' THEN 1 END) AS sold_products,
            COALESCE(SUM(CASE WHEN p.status = 'sold' THEN p.price END), 0) AS total_earnings
        FROM users u
        LEFT JOIN products p ON u.user_id = p.user_id
        WHERE u.is_admin = 0
        GROUP BY u.user_id
    ";


    $user_product_summary = $conn->query($summary_sql)->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $errors[] = "Error: " . $e->getMessage();
}


// https://www.w3schools.com/php/php_mysql_delete.asp + ai helped, since its deleting the user AND their products AND purchases
if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];

    try {
        $conn->begin_transaction();


        //Delete all purchases made BY this user
        $stmt_uB = $conn->prepare("DELETE FROM purchases WHERE user_id = ?");
        $stmt_uB->bind_param("i", $user_id);
        $stmt_uB->execute();        

        //delete all purchases related to this user's products
        $stmt_uP = $conn->prepare("
            DELETE purchases FROM purchases 
            INNER JOIN products ON purchases.product_id = products.product_id 
            WHERE products.user_id = ?
        ");
        $stmt_uP->bind_param("i", $user_id);
        $stmt_uP->execute();
        
        //delete all products belonging to this user
        $stmt_uPr = $conn->prepare("DELETE FROM products WHERE user_id = ?");
        $stmt_uPr->bind_param("i", $user_id);
        $stmt_uPr->execute();

        //finally delete the user
        $stmt_U = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt_U->bind_param("i", $user_id);
        $stmt_U->execute();

        if ($stmt_U->affected_rows > 0) {
            $conn->commit();
            $_SESSION['success'] = 'User and all related data have been deleted successfully';
        } else {
            $conn->rollback();
            $_SESSION['errors'] = ['Failed to delete user (user may not exist)'];
        }

        $stmt_uB->close();
        $stmt_uP->close();
        $stmt_uPr->close();
        $stmt_U->close();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['errors'] = ['Error deleting user: ' . $e->getMessage()];
    }

    redirect('admin_page.php');
}




?>


<!-- used bootstrap + ai to structure and style this -->
<div class="jumbotron">
    <div class = "container">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h1>
        <p class="lead">Admin Dashboard</p>
    </div>
</div>

<section class="product-details">
    <h2>System Statistics</h2>
    <div class="grid">

        <div class="order_summary">
            <h3>Users</h3>
            <p><b><?= $stats['total_users'] ?></b> Total</p>
        </div>

        <div class="order_summary">
            <h3>Products</h3>
            <p>Total <b><?= $stats['total_products'] ?></b> </p>
            <p>Sold <b><?= $stats['sold_products'] ?></b> </p>
            <p>Available <b><?= $stats['available_products'] ?></b> </p>
        </div>

    </div>
</section>

<section class="product-details">
    <h2>Users</h2>

    <div class="table-wrapper">
        <table class="summary-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Total Posts</th>
                    <th>Delete</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($user_product_summary as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td class="center"><?= $u['total_products'] ?></td>
                        <td class="center">
                            <form method="POST" style="display: inline-block;" 
                                  onsubmit="return confirm('Are you sure you want to delete user <?= htmlspecialchars($u['username']) ?>? This will also delete all their products and related purchsaas and cannot be undone.');">
                                <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger btn-sm">
                                    Delete User
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>
    
</section>


<?php include('includes/footer.php'); ?>