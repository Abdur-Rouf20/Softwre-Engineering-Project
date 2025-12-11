// --------------------------------------------------
// File: payment.php (Stripe integration skeleton)
// --------------------------------------------------
<?php
require_once 'config.php';
requireLogin();
$pdo = getPDO();
$order_id = (int)($_GET['order_id'] ?? 0);
$order = $pdo->prepare('SELECT * FROM orders WHERE id=? AND user_id=?'); $order->execute([$order_id, $_SESSION['user']['id']]); $order = $order->fetch();
if(!$order) { echo 'Order not found'; exit; }
// Use Stripe PHP library on server - placeholder
// composer require stripe/stripe-php

require 'header.php';
?>
<div class="container">
  <h2>Pay for Order <?=e($order['order_number'])?></h2>
  <p>Total: $<?=number_format($order['total_amount'],2)?></p>
  <p>Use Stripe to complete payment — configure keys in config.php.</p>
  <form action="payment_process.php" method="POST">
    <input type="hidden" name="order_id" value="<?=e($order['id'])?>">
    <button type="submit">Pay with Card (Stripe)</button>
  </form>
</div>
<?php require 'footer.php'; ?>

