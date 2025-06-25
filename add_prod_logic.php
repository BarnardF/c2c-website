<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
require_once 'includes/functions.php';

if (!logged_in()) {
    redirect('../login.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = sanitize_input($_POST['title']);
    $category = sanitize_input($_POST['category']);
    $description = sanitize_input($_POST['product_descr']); 
    $price = sanitize_input($_POST['price']);
    $product_condition = sanitize_input($_POST['product_condition']);
    $location = sanitize_input($_POST['location']);
    $shipping_options = sanitize_input($_POST['shipping_options']);


    //validate
    if (empty($title) || empty($category) || empty($description) || empty($product_condition) || empty($location) || empty($shipping_options)){
        $errors[] = "All fields are required";
    }
    if ($price <= 0) {
        $errors[] = "Valid price is required";
    }


    //handling image upload, ai helped with this
    //https://www.w3schools.com/php/php_file_upload.asp
    //https://youtu.be/6iERr1ADFz8?si=QJTUdtbGkzfpL4Aj Image name rename
    //https://youtu.be/JaRq73y5MJk?si=lxL4bCXVhVU-3kq6
    //https://youtu.be/1NiJcZrPHvA?si=co7_hpa2PVGqayvF

    $image_path = null;
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 5 * 1024 * 1024; // max file size = 5MB
        
        if (!in_array($_FILES['product_image']['type'], $allowed_types)) {
            $errors[] = "Only JPG and PNG images are allowed";
        }
        
        if ($_FILES['product_image']['size'] > $max_size) {
            $errors[] = "Image size must be less than 5MB";
        }
        
        if (empty($errors)) {
            $upload_dir = 'uploads/products/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $file_extension;
            $image_path = $upload_dir . $filename;
            
            if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $image_path)) {
                $errors[] = "Failed to upload image";
            }
        }
    } else {
        $errors[] = "Product image is required";
    }

    // ai helped with this

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO products (user_id, category_id, title, product_descr, price, product_condition, location, shipping_options, image_path, listing_created, status) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'available')");
        $stmt->bind_param("iissdssss", $user_id, $category, $title, $description, $price, $product_condition, $location, $shipping_options, $image_path);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Product listed successfully!";
            redirect("user_listings.php");
        } else {
            $errors[] = "Failed to list product: " . $stmt->error;
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        redirect("add_product.php");

    }
}
?>

