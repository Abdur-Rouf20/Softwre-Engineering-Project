<?php
// --------------------------------------------------
// File: admin_dashboard.php
// --------------------------------------------------
require_once 'config.php';
requireRole('admin');
$pdo = getPDO();
// pending product list
$pending = $pdo->query("SELECT p.*, u.name as seller_name FROM products p JOIN users u ON p.seller_id=u.id WHERE product_status='pending' ORDER BY created_at DESC")->fetchAll();
// simple analytics: top sold products - requires order_items
$top = $pdo->query("SELECT p.title, SUM(oi.quantity) as sold_qty FROM order_items oi JOIN products p ON oi.product_id=p.id GROUP BY p.id ORDER BY sold_qty DESC LIMIT 5")->fetchAll();
require 'header.php';
?>
<div class="container">
  <h2>Admin Dashboard</h2>
  <section>
    <h3>Pending Products</h3>
    <table>
      <tr><th>ID</th><th>Title</th><th>Seller</th><th>Action</th></tr>
      <?php foreach($pending as $p): ?>
        <tr>
          <td><?=e($p['id'])?></td>
          <td><?=e($p['title'])?></td>
          <td><?=e($p['seller_name'])?></td>
          <td>
            <form method="post" action="admin_actions.php" style="display:inline">
              <input type="hidden" name="action" value="publish">
              <input type="hidden" name="product_id" value="<?=e($p['id'])?>">
              <button type="submit">Publish</button>
            </form>
            <form method="post" action="admin_actions.php" style="display:inline">
              <input type="hidden" name="action" value="reject">
              <input type="hidden" name="product_id" value="<?=e($p['id'])?>">
              <button type="submit">Reject</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </section>

  <section>
    <h3>Top Selling Products</h3>
    <ul>
      <?php foreach($top as $t): ?><li><?=e($t['title'])?> — <?=e($t['sold_qty'])?> sold</li><?php endforeach; ?>
    </ul>
  </section>

  <section>
    <a href="admin_add_category.php">Add Category</a>
    <a href="reports_monthly.php">Download Monthly Analytics (PDF)</a>
  </section>
</div>
<?php require 'footer.php'; ?>
