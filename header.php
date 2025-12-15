
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>NextGen Electronics</title>
  <link rel="stylesheet" href="assets/main.css">
</head>
<body>
<header>
  <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $c) {
        $cart_count += $c['quantity'];
    }
}

$cart_count = 0;

if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $c) {
        $cart_count += $c['quantity'];
    }
}
?>
  <nav>
    <a href="http://localhost/software/Softwre-Engineering-Project">Home</a>
    <?php if(!isLoggedIn()): ?>
      <a href="login.php">Login / Register</a>
    <?php else: ?>
      <?php if($_SESSION['user']['role']==='admin'): ?><a href="admin_dashboard.php">Admin Dashboard</a><?php endif; ?>
      <?php if($_SESSION['user']['role']==='seller'): ?><a href="seller_dashboard.php">Seller Dashboard</a><?php endif; ?>
      <?php if($_SESSION['user']['role']==='buyer'): ?><a href="buyer_dashboard.php">My Account Dashboard</a><?php endif; ?>
      <a href="Orders/cart.php">Cart</a>
      <a href="logout.php">Logout</a>
    <?php endif; ?>
  </nav>
</header>
<main>

