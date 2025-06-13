<?php 
include('includes/header.php'); 

include('includes/error_msg.php');
?>



<!-- login form, used bootstrap for inspo -->
<div class="form_container" >
  <h2 class="form-title">Login</h2>
  <form action="includes/login_logic.php" method="POST">

    <div class="form-group">
      <label for="email">Email address</label>
      <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
      <input type="checkbox" onclick="togglePassword()">Show Password:   
    </div>

    <button type="submit" class="button" name="signin">Login</button>

    <div class="links">
        Don't have an account?  <a href="register.php">Register</a>
    </div>

  </form>
</div>
<script src="assets/js/scripts.js"></script>




<?php include('includes/footer.php'); ?>
