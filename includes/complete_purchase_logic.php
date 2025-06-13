<?php
require_once 'db_connect.php';
require_once 'functions.php';

if (!logged_in()) {
    redirect('../login.php');
}


// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../browse_products.php');
}

// Get form data
$product_id = (int)($_POST['product_id'] ?? 0);
$first_name = sanitize_input($_POST['first_name'] ?? '');
$last_name = sanitize_input($_POST['last_name'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$user_id = $_SESSION['user_id'];


// Validate product ID
if ($product_id <= 0) {
    $_SESSION['errors'] = ["Invalid product ID"];
    redirect('../browse_products.php');
}

// Get product details and verify it's still available
$stmt = $conn->prepare("SELECT p.*, u.username FROM products p 
                       JOIN users u ON p.user_id = u.user_id 
                       WHERE p.product_id = ? AND p.status = 'available'");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    $_SESSION['errors'] = ["Product not found or no longer available"];
    redirect('../browse_products.php');
}

// Check if user is trying to buy their own product
if ($product['user_id'] == $user_id) {
    $_SESSION['errors'] = ["You cannot purchase your own product"];
    redirect('../product_details.php?id=' . $product_id);
}

// Get the logged-in user's email from database
$user_stmt = $conn->prepare("SELECT email FROM users WHERE user_id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_data = $user_result->fetch_assoc();

if (!$user_data) {
    $_SESSION['errors'] = ["User not found"];
    redirect('../checkout.php?id=' . $product_id);
}
$user_email = $user_data['email'];

// Validate all fields
$errors = [];

if (empty($first_name)) {
    $errors[] = "First name is required";
}

if (empty($last_name)) {
    $errors[] = "Last name is required";
}

if (empty($email)) {
    $errors[] = "Email address is required";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address";
} elseif ($email !== $user_email) {
    $errors[] = "You can only use your registered email address: ";
}


// If errors, redirect back to checkout
if (!empty($errors)) {
    $_SESSION['form_data'] = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email
    ];
    $_SESSION['errors'] = $errors;
    redirect('../checkout.php?id=' . $product_id);
}




// ai helped with this(claude - 10 June), redid my code 3 times hence why the late date
// Process the purchase
try {
    $conn->begin_transaction();

    // Insert purchase record
    $stmt = $conn->prepare("INSERT INTO purchases (user_id, product_id, price, billing_name, billing_address, payment_details, purchase_date)
                            VALUES (?, ?, ?, ?, ?, ?, NOW())");
    
    $billing_name = trim($first_name . ' ' . $last_name);
    $billing_address = $email;
    $payment_method = "online";
    $payment_details = "online purchase";
    
    $stmt->bind_param("iidsss", $user_id, $product_id, $product['price'], $billing_name, $billing_address, $payment_details);

    if (!$stmt->execute()) {
        throw new Exception("Failed to record purchase: " . $stmt->error);
    }

    //update status to sold
    $update_stmt = $conn->prepare("UPDATE products 
                                    SET status = 'sold' 
                                    WHERE product_id = ?");
    $update_stmt->bind_param("i", $product_id);
    
    if (!$update_stmt->execute()) {
        throw new Exception("Failed to update product status: " . $update_stmt->error);
    }

    $conn->commit();

    // Clear form data
    unset($_SESSION['form_data']);

    $_SESSION['success'] = "Purchase completed successfully! Your order has been processed.";
    
    redirect('../purchase_success.php');

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    $_SESSION['errors'] = ["Purchase failed: " . $e->getMessage()];
    $_SESSION['form_data'] = [
        'first_name' => $first_name, 
        'last_name' => $last_name, 
        'email' => $email
    ];
    redirect('../checkout.php?id=' . $product_id);
}
?>