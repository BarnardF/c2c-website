<?php
require_once 'config.php';

// define('host', 'localhost');
// define('db_user', 'root');
// define('db_password', '');
// define('db_name', 'c2c_ecommerse_bfourie');
// define('port', '3307');

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
