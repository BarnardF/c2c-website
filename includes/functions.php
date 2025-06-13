<?php

require_once 'config.php';
require_once 'db_connect.php';


function sanitize_input($data) {
    $data = trim($data);//remose whitepscaes
    $data = stripslashes($data);//'Un-quotes a quoted string'
    $data = htmlspecialchars($data);//'Convert special characters to HTML entities'
    return $data;
}


function display_error_msg($message) {
    return "<div class='alert alert-danger'>$message</div>";
}


function display_success_msg($message) {
    return "<div class='alert alert-success'>$message</div>";
}

function redirect($url) {
    header("Location: $url");
    exit;
}


function logged_in() {
    return isset($_SESSION['user_id']);
}

function get_current_logged_user() {
    if (!logged_in()) return null;

    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?? null;
}

?>