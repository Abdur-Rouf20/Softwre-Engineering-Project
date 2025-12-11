


// --------------------------------------------------
// File: cancel_order.php
// Buyer can request cancellation if not shipped
// --------------------------------------------------
<?php
require_once 'config.php';
requireLogin();
$pdo = getPDO();
$id = (int)($_GET['id'] ?? 0);
$order = $pdo->prepare('SELECT * FROM orders WHERE id=? AND user_id=?'); $order->execute([$id,$_SESSION['user']['id']]); $order = $order->fetch();
if(!$order){ echo 'Order not found'; exit; }
if($order['order_status']=='shipped' || $order['order_status']=='delivered'){ echo 'Cannot cancel shipped/delivered orders'; exit; }
if($_SERVER['REQUEST_METHOD']==='POST'){
    $note = trim($_POST['note'] ?? '');
    $pdo->prepare('INSERT INTO cancellations (order_id,requested_by,reason,cancel_status,created_at) VALUES (?,?,?,?,NOW())')
        ->execute([$order['id'], $_SESSION['user']['id'], $note, 'requested']);
    $pdo->prepare('UPDATE orders SET order_status="cancelled" WHERE id=?')->execute([$order['id']]);
    flash_set('success','Cancellation requested'); header('Location: buyer_dashboard.php'); exit;
}
require 'header.php';
?>
<div class="container">
  <h2>Request Cancellation for <?=e($order['order_number'])?></h2>
  <form method="post">
    <label>Note<textarea name="note"></textarea></label>
    <button type="submit">Request Cancel</button>
  </form>
</div>
<?php require 'footer.php'; ?>

