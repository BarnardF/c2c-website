<?php

// Display session messages (success, errors)
// Call this after header.php in all pages
// ai did help with this(claude - 6 June 2025)





// Display success message from session if available
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

// Display errors from session if any
if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
    echo '<div class="alert alert-danger">';
    foreach ($_SESSION['errors'] as $error) {
        echo "<p>$error</p>";
    }
    echo '</div>';
    unset($_SESSION['errors']);
}


?>