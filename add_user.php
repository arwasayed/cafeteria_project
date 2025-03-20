<?php
session_start();
include_once 'add_user_Header.php'; 
?>
<div class="wrapper">
  <form class="form-register">
    <h2 class="form-register-heading">Add User</h2>
    
    <input type="text" class="form-control" name="name" placeholder="Full Name" required autofocus />
    <input type="email" class="form-control" name="email" placeholder="Email Address" required />
    <input type="password" class="form-control" name="password" placeholder="Password" required />
    <input type="password" class="form-control" name="confirmPassword" placeholder="Confirm Password" required />
    <input type="number" class="form-control" name="roomNumber" placeholder="Room Number" required />
    <input type="number" class="form-control" name="extensionNumber" placeholder="Extension Number" required />
    
    <label for="imageUpload" class="btn btn-lg btn-secondary btn-block">
      Upload Image
      <input type="file" class="form-control-file" name="image" id="imageUpload" accept="image/*" required />
    </label>
    
    <button class="btn btn-lg btn-primary btn-block" type="submit">Register</button>
  </form>
</div>


  <?php
// session_start();

// if (!isset($_SESSION['username'])) {
//     header("Location: login.html");
//     exit();
// }

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $name = $_POST['name'];
//     $email = $_POST['email'];
//     $password = $_POST['password'];
//     $confirm_password = $_POST['confirm_password'];
//     $numRome = $_POST['numRome'];
//     $ext = $_POST['ext'];

//     if ($password != $confirm_password) {
//         echo "Passwords do not match.";
//     } else {
//         $target_dir = "uploads/";
//         $target_file = $target_dir . basename($_FILES["img"]["name"]);
//         move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);

//         echo "User added successfully! Welcome, " . $_SESSION['username'];
//     }
// }
?>

<?php include 'footer.php'; ?>
