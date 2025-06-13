<?php

// $DB_HOST='localhost';
// $DB_USER='root';
// $DB_PASSWORD='';
// $DB_NAME='c2c_ecommerse_bfourie';
// $DB_PORT = 3307;

// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASSWORD', '');
// define('DB_NAME', 'c2c_ecommerce_bfourie');
// define('DB_PORT', 3307);



define('DB_HOST', 'sql107.infinityfree.com');
define('DB_USER', 'if0_39210162');
define('DB_PASSWORD', 'CNplpVi8L8Zja');
define('DB_NAME', 'if0_39210162_boomgom_db');
define('DB_PORT', '3306');



// Website config
define('SITE_NAME', 'BoomGom');

// Session config(ai helped)
ini_set('session.cookie_httponly', 1); //This makes the session cookie accessible only through HTTP(S) requests, not JavaScript. This prevents XSS attacks from stealing session cookies.
ini_set('session.use_only_cookies', 1);//this forces PHP to only use cookies for session management (not URL parameters), which is more secure.
session_start();
?>
