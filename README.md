# BoomGom E-commerce Platform - User Manual(ai did help make this)

## Table of Contents
1. [Project Overview](#project-overview)
2. [System Requirements](#system-requirements)
3. [Installation Guide](#installation-guide)
4. [Database Structure](#database-structure)
5. [File Structure](#file-structure)
6. [User Guide](#user-guide)
7. [Admin Guide](#admin-guide)
8. [Features](#features)
9. [Security Features](#security-features)

---

## Project Overview

**BoomGom** is a South African Customer-to-Customer (C2C) e-commerce platform built with PHP, MySQL, HTML, CSS, and JavaScript. It enables users to buy and sell items directly with each other in a secure environment.

### Key Features
- User registration and authentication
- Product listing and browsing
- Category-based filtering
- "Secure purchase system"
- Admin dashboard
- Responsive design
- Image upload functionality

---

## System Requirements

### Server Requirements
XAMPP for MySQL and Apache

---

## Installation Guide

### 1. Database Setup
Create a MySQL database and import the following tables:

```sql
-- Users table
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Category table
CREATE TABLE category (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL
);

-- Products table
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    title VARCHAR(200) NOT NULL,
    product_descr TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INT,
    product_condition ENUM('new', 'like-new', 'good', 'fair', 'poor'),
    location VARCHAR(100),
    shipping_options ENUM('pickup', 'delivery', 'both'),
    image_path VARCHAR(500),
    status ENUM('available', 'sold') DEFAULT 'available',
    listing_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (category_id) REFERENCES category(category_id)
);

-- Purchases table
CREATE TABLE purchases (
    purchase_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT,
    price DECIMAL(10,2),
    billing_name VARCHAR(100),
    billing_address VARCHAR(255),
    payment_details VARCHAR(255),
    purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);
```

### 2. Configuration Files

Create `includes/config.php`:
```php
<?php
session_start();
define('SITE_NAME', 'BoomGom');
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'your_database_name');
?>
```

Create `includes/db_connect.php`:
```php
<?php
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

### 3. Product Listing Logic
Create `add_prod_logic.php` in the root directory to handle product listing submissions:

```php
<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (!logged_in()) {
    redirect('login.php');
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

    // Validate required fields
    if (empty($title) || empty($category) || empty($description) || 
        empty($product_condition) || empty($location) || empty($shipping_options)) {
        $errors[] = "All fields are required";
    }
    if ($price <= 0) {
        $errors[] = "Valid price is required";
    }

    // Handle image upload
    $image_path = null;
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 5 * 1024 * 1024; // 5MB max file size
        
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

    // Insert product if no errors
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

    // Handle errors
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        redirect("add_product.php");
    }
}
?>
```

---

## Database Structure

### Tables 

#### users
- Stores user account information
- Includes admin flag for administrative access
- Passwords are hashed using PHP's password_hash()

#### category
- Product categories (Electronics, Clothing, etc.)
- Referenced by products table

#### products
- Product listings with details
- Links to users (seller) and categories
- Tracks availability status

#### purchases
- Transaction records
- Links buyers to purchased products
- Stores billing information

---

## File structure

```
project-root/
├── assets/
│   ├── css/
│   │   └── styles.css          # Main stylesheet
│   └── js/
│       └── scripts.js          # JavaScript functions
├── includes/
│   ├── config.php              # Configuration settings
│   ├── db_connect.php          # Database connection
│   ├── functions.php           # Helper functions
│   ├── header.php              # Common header
│   ├── footer.php              # Common footer
│   ├── error_msg.php           # Error/success messages
│   ├── login_logic.php         # Login processing
│   ├── register_logic.php      # Registration processing
│   └── complete_purchase_logic.php # Purchase processing
├── add_prod_logic.php          # Product listing processing
├── uploads/                    # Product images (create this directory)
├── index.php                   # Homepage
├── login.php                   # Login page
├── register.php                # Registration page
├── logout.php                  # Logout script
├── browse_products.php         # Product browsing
├── add_product.php             # Product listing form
├── product_details.php         # Individual product view
├── checkout.php                # Checkout process
├── purchase_success.php        # Purchase confirmation
├── user_listings.php           # User's own products
└── admin_page.php              # Admin dashboard
```

---

## User Guide

### Getting Started

#### 1. Registration
1. Click "Register" in the navigation
2. Fill in required information:
   - Username (unique)
   - Email address (unique)
   - First and Last name
   - Password (minimum 8 characters)
   - Confirm password
3. Click "Register"

#### 2. Login
1. Click "Login" in the navigation
2. Enter email and password
3. Use "Show Password" checkbox if needed
4. Click "Login"

### Selling Items

#### 1. List a Product
1. Ensure you're logged in
2. Click "Sell Item" or navigate to add_product.php
3. Fill in product details:
   - **Title**: Clear, descriptive product name
   - **Category**: Select appropriate category
   - **Description**: Detailed product description
   - **Price**: In South African Rand (ZAR)
   - **Condition**: Select from New to Poor
   - **Location**: Your city/area
   - **Shipping**: Pickup only, delivery, or both
   - **Image**: Upload clear product photo (max 5MB)
4. Click "List Product"

#### 2. Manage Your Listings
1. Click "My Listings" in navigation
2. View all your products
3. Edit or remove listings as needed

### Buying Items

#### 1. Browse Products
1. Click "Browse Products" or "Products" in navigation
2. Use filters to narrow search:
   - **Category**: Filter by product type
   - **Price**: Sort low to high or high to low
3. Click "View Details" on products of interest

#### 2. Purchase Process
1. From product details page, click "Buy Now"
2. Review order summary on checkout page
3. Enter billing information:
   - First and Last name
   - Email address (must match your account)
4. Click "Complete Purchase"
5. Confirmation page will appear

### Navigation Features

#### Homepage
- Welcome section with site overview
- Quick action buttons (Sell, Browse, My Listings)
- Featured products display

#### Product Browsing
- Grid layout of available products
- Filter by category and price
- Search functionality
- Product cards show key information

---

## Admin Guide

### Accessing Admin Features
1. Admin accounts must be set manually in database (is_admin = 1)
2. Login with admin account
3. "Admin Dashboard" link appears in navigation

### Admin Dashboard Features

#### System Statistics
- Total users count
- Total products (available/sold)
- Total purchases

#### User Product Summary
- Comprehensive table showing:
  - Username and email
  - Product counts by user

---

## Features

### Security Features
- **Password Hashing**: Uses PHP's password_hash() with default algorithm
- **SQL Injection Prevention**: Prepared statements throughout
- **XSS Protection**: htmlspecialchars() on all user inputs
- **Input Validation**: Server-side validation for all forms
- **File Upload Security**: Type and size restrictions on images

### User Experience Features
- **Responsive Design**: Works on desktop, tablet, and mobile
- **Image Preview**: Shows selected image before upload
- **Success/Error Messages**: Clear feedback for all actions
- **Password Toggle**: Show/hide password functionality

### Product Listing Backend Logic

The `add_prod_logic.php` file handles the server-side processing for product listings with the following features:

#### Authentication Check
- Verifies user is logged in before processing
- Redirects to login page if not authenticated

#### Input Validation
- **Required Fields**: All form fields must be completed
- **Price Validation**: Ensures price is greater than 0
- **Data Sanitization**: Uses `sanitize_input()` function for all inputs

#### Image Upload Processing
- **File Type Validation**: Only JPG and PNG images allowed
- **Size Limit**: Maximum 5MB file size
- **Directory Creation**: Automatically creates `uploads/products/` if it doesn't exist
- **Unique Naming**: Uses `uniqid()` to prevent filename conflicts
- **Security**: Validates file type and size before processing

#### Database Operations
- **Prepared Statements**: Uses parameterized queries to prevent SQL injection
- **Automatic Timestamps**: Sets `listing_created` to current timestamp
- **Status Setting**: Automatically sets product status to 'available'

#### Error Handling
- **Session Storage**: Stores errors and form data in session variables
- **User Feedback**: Provides specific error messages for different failure scenarios
- **Form Retention**: Preserves user input when validation fails

#### Success Flow
- **Database Insert**: Adds product to database with all validated information
- **Success Message**: Sets success message in session
- **Redirect**: Takes user to their listings page after successful submission

This backend logic ensures data integrity, security, and proper user experience during the product listing process.

---
