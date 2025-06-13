<?php
require_once 'config.php';
require_once 'db_connect.php';
require_once 'functions.php';

if (logged_in()) {
    redirect('../index.php');
}


$errors = [];
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitize_input($_POST["email"]);
    $password = $_POST["password"];
    

    if (empty($email) || empty($password)){
        $errors[] = "All fields are required";
    }
     

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT user_id, username, password_hash, is_admin 
                                FROM users 
                                WHERE email = ?");

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password_hash'])) {
                
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user'] = $user;
                $_SESSION['is_admin'] = $user['is_admin'];
                $_SESSION['success'] = "Login successful!";
                
                redirect('../index.php');
            } else {
                $errors[] = "Invalid email or password.";
            }
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
            
    // If there are errors, store them in session and redirect back to login page(ai helped again)
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        redirect('../login.php');
    }
} else {
    redirect('../login.php');    
}
?>




<!-- $errors = [];
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitize_input($_POST["email"]);
    $password = $_POST["password"];
    if (empty($email)) {
        $errors[] = "Email is required";
    }
     
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    // No errors, continue
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT user_id, username, password_hash, is_admin FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password_hash'])) {
             
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user'] = $user;
                $_SESSION['is_admin'] = $user['is_admin'];
                $_SESSION['success'] = "Login successful!";
             
                redirect('../index.php');
            } else {
                $errors[] = "Invalid email or password.";
            }
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
    // If there are errors, store them in session and redirect back to login page(ai helped again)
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        redirect('../login.php');
    }
} -->


