<?php

require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}

$db = new Database(new DatabaseConfig());
$stmt = $db->getConnection()->prepare("SELECT * FROM User_Table WHERE u_id = :user_id");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Define user details for display
$userName = htmlspecialchars($user['name'] ?? 'User');
$userImage = htmlspecialchars($user['image_path'] ?? 'images/default-avatar.png'); // Default avatar if no image exists
?>

<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <link rel="shortcut icon" href="images/favicon.png" type="">

  <title> Yummy </title>

  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

  <!--owl slider stylesheet -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
  <!-- nice select  -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/css/nice-select.min.css" integrity="sha512-CruCP+TD3yXzlvvijET8wV5WxxEh5H8P4cmz0RFbKK6FlZ2sYl3AEsKlLPHbniXKSrDdFewhbmBK5skbdsASbQ==" crossorigin="anonymous" />
  <!-- font awesome style -->
  <link href="css/font-awesome.min.css" rel="stylesheet" />

  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/table.css">
  <link rel="stylesheet" href="css/order.css">

</head>
<body>

  <div class="hero_area"  class="header">
   
    <!-- header section strats -->
    <header class="header_section">
      <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container ">
          <a class="navbar-brand" href="userhome.php">
            <span>
            Yummy
            </span>
          </a>

          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class=""> </span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav  mx-auto ">
              <li class="nav-item active">
                <a class="nav-link" href="userhome.php">Home <span class="sr-only">(current)</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="MyOrders.php">My orders</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="addorder.php">add orders</a>
              </li>
            </ul>
            <div class="user_option">
              <?php if (isset($_SESSION['user_id'])): ?>
                  <a class="user_link" href="#">
                    <img src="<?php echo $userImage; ?>" alt="User Image" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                    <span ><?php echo $userName; ?></span>
                  </a>
              <?php else: ?>
                <a class="order_online" href="index.php">Login</a>
              <?php endif; ?>
              <a href="logout.php" class="order_online">
                Logout
              </a>
            </div>
          </div>
        </nav>
      </div>
    </header>