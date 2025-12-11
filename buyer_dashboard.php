// --------------------------------------------------
// File: buyer_dashboard.php
// --------------------------------------------------
<?php
require_once 'config.php';
requireRole('buyer');
$pdo = getPDO();
$uid = $_SESSION['user']['id'];
$orders = $pdo->prepare('SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC'); $orders->execute([$uid]); $orders = $orders->fetchAll();
require 'header.php';
?>
<div class="container">
  <h2>My Account</h2>
  <a href="edit_profile.php">Edit Profile</a>
  <h3>Orders</h3>
  <table>
    <tr><th>Order#</th><th>Total</th><th>Status</th><th>Action</th></tr>
    <?php foreach($orders as $o): ?>
      <tr>
        <td><?=e($o['order_number'])?></td>
        <td>$<?=e($o['total_amount'])?></td>
        <td><?=e($o['order_status'])?></td>
        <td>
          <a href="order_tracking.php?id=<?=e($o['id'])?>">Track</a>
          <?php if($o['order_status']!='shipped' && $o['order_status']!='delivered'): ?>
            <a href="cancel_order.php?id=<?=e($o['id'])?>">Request Cancel</a>
          <?php endif; ?>
          <a href="download_invoice.php?id=<?=e($o['id'])?>">Download Invoice</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php require 'footer.php'; ?>

