<?php

// $DB_HOST='';
// $DB_USER='';
// $DB_PASSWORD='';
// $DB_NAME='';
// $DB_PORT = ;

//im not revealing any info
define('DB_HOST', 'hostname');
define('DB_USER', 'username');
define('DB_PASSWORD', 'password');
define('DB_NAME', 'db_name');
define('DB_PORT', 'port');

define('SITE_NAME', 'BoomGom');





// Session config(ai helped)

//decided not to do this
// $cookie_name = $_SESSION['is_admin'];
// $cookie_value = $_SESSION['username'];
// setcookie($cookie_name, $cookie_value, time() + (86400 * 1), "/");



//https://www.w3schools.com/php/php_cookies.asp


ini_set('session.cookie_httponly', 1); //This makes the session cookie accessible only through HTTP(S) requests, not JavaScript. This prevents XSS attacks from stealing session cookies.
ini_set('session.use_only_cookies', 1);//this forces PHP to only use cookies for session management (not URL parameters), which is more secure.
session_start();
?>
