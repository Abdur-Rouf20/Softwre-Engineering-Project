// --------------------------------------------------
// File: order_tracking.php
// --------------------------------------------------
<?php
require_once 'config.php';
requireLogin();
$pdo = getPDO();
$id = (int)($_GET['id'] ?? 0);
$order = $pdo->prepare('SELECT * FROM orders WHERE id=? AND user_id=?'); $order->execute([$id,$_SESSION['user']['id']]); $order = $order->fetch();
if(!$order){ echo 'Order not found'; exit; }
require 'header.php';
?>
<div class="container">
  <h2>Tracking for Order <?=e($order['order_number'])?></h2>
  <p>Status: <strong><?=e($order['order_status'])?></strong></p>
  <div class="timeline">
    <div>Placed: <?=e($order['created_at'])?></div>
    <?php if($order['order_status']!='pending'): ?>
      <div>Confirmed</div>
    <?php endif; ?>
    <?php if($order['order_status']=='shipped' || $order['order_status']=='delivered'): ?>
      <div>Shipped</div>
    <?php endif; ?>
    <?php if($order['order_status']=='delivered'): ?>
      <div>Delivered</div>
    <?php endif; ?>
  </div>
</div>
<?php require 'footer.php'; ?>
