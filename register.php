<?php 
include('includes/header.php'); 


include('includes/error_msg.php');
?>

<!-- register form -->
<div class="form_container">
    <h2 class="form-title">Register</h2>
    <form action="includes/register_logic.php" onsubmit="return validateRegisterForm()" method="POST">
        
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
        </div>

        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Your first name" required>
        </div>

        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Your last name" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            <input type="checkbox" onclick="togglePassword()">Show Password:
        </div>

        <div class="form-group">
            <label for="confirmedPassword">Confirm Password</label>
            <input type="password" class="form-control" id="confirmedPassword" name="confirmedPassword" placeholder="Confirm Password" required>
            <input type="checkbox" onclick="toggle_confirmed_Password()">Show Password:
        </div>

        <div class="button-container">
            <button type="submit" class="button" name="register">Register</button>
        </div>
    </form>

    <div class="links">
        Already have an account? <a href="login.php">Login</a>
    </div>
</div>


<?php include('includes/footer.php'); ?>