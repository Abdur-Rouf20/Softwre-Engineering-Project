// --------------------------------------------------
// File: seller_dashboard.php
// --------------------------------------------------
<?php
require_once 'config.php';
requireRole('seller');
$pdo = getPDO();
$uid = $_SESSION['user']['id'];
$orders = $pdo->prepare('SELECT o.* FROM orders o WHERE o.seller_id=? ORDER BY created_at DESC'); $orders->execute([$uid]);
$orders = $orders->fetchAll();
// seller analytics: product counts
$top = $pdo->prepare('SELECT p.title, SUM(oi.quantity) as sold_qty FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE p.seller_id=? GROUP BY p.id ORDER BY sold_qty DESC LIMIT 5'); $top->execute([$uid]); $top = $top->fetchAll();
require 'header.php';
?>
<div class="container">
  <h2>Seller Dashboard</h2>
  <a href="seller_add_product.php">Add Product</a>
  <section>
    <h3>Orders</h3>
    <table>
      <tr><th>Order#</th><th>Buyer</th><th>Total</th><th>Status</th><th>Action</th></tr>
      <?php foreach($orders as $o): ?>
        <tr>
          <td><?=e($o['order_number'])?></td>
          <td>Buyer ID: <?=e($o['user_id'])?></td>
          <td>$<?=e($o['total_amount'])?></td>
          <td><?=e($o['order_status'])?></td>
          <td>
            <?php if($o['order_status']=='confirmed'): ?>
              <form method="post" action="seller_actions.php"><input type="hidden" name="action" value="ship"><input type="hidden" name="order_id" value="<?=e($o['id'])?>"><button type="submit">Mark Shipped</button></form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </section>

  <section>
    <h3>Top Products</h3>
    <ul><?php foreach($top as $t) echo '<li>'.e($t['title']).' — '.e($t['sold_qty']).' sold</li>'; ?></ul>
  </section>
</div>
<?php require 'footer.php'; ?>

