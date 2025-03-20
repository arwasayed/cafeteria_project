<?php 
include_once 'loginheader.php' ?>

<div class="wrapper">
    <form class="form-signin">       
      <h2 class="form-signin-heading">Please login</h2>
      <input type="text" class="form-control" name="username" placeholder="Email Address" required="" autofocus="" />
      <input type="password" class="form-control" name="password" placeholder="Password" required=""/>      
      <label class="checkbox">
        <input type="checkbox" value="remember-me" id="rememberMe" name="rememberMe"> Remember me
      </label>
      <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>   
    </form>
  </div>
  <?php
// No need to verify username and password
// session_start();
// $_SESSION['username'] = $_POST['username'];  // Save the username in the session
// header("Location: add_user.php");
// exit();
?>

<?php include_once 'footerlogin.php' ?>