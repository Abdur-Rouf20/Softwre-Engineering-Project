<?php
session_start();
unset($_SESSION['cart']); // clear session cart after order
?>
<h2>Order Successful!</h2>
<p>Your order has been placed successfully.</p>
<a href="index.php" class="btn">Continue Shopping</a>
