<?php
require_once 'config.php';

// define('host', '');
// define('db_user', '');
// define('db_password', '');
// define('db_name', '');
// define('port', '');

function get_database_conn() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

    //check connection 3
    if($conn->connect_error){
        die("connection failed: " .$conn->connect_error);
        echo "Connection error";
    }
    return $conn;
}

$conn = get_database_conn();
?>
