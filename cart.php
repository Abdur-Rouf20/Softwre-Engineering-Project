<?php
// --------------------------------------------------
// File: cart.php
// --------------------------------------------------
require_once 'config.php';
$pdo = getPDO();
// Simple cart stored in DB: find or create cart for user
if(!isLoggedIn()){ header('Location: login.php'); exit; }
$uid = $_SESSION['user']['id'];
// ensure cart exists
$stmt = $pdo->prepare('SELECT * FROM carts WHERE user_id=?'); $stmt->execute([$uid]); $cart = $stmt->fetch();
if(!$cart){ $pdo->prepare('INSERT INTO carts (user_id,created_at) VALUES (?,NOW())')->execute([$uid]); $cart = $pdo->query('SELECT * FROM carts WHERE user_id='.intval($uid))->fetch(); }
$cart_id = $cart['id'];
// add item
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_product_id'])){
    $pid = (int)$_POST['add_product_id']; $qty = max(1,(int)($_POST['qty'] ?? 1));
    // get price
    $p = $pdo->prepare('SELECT price FROM products WHERE id=? AND product_status="active"'); $p->execute([$pid]); $prod = $p->fetch();
    if($prod){
        // check existing
        $s=$pdo->prepare('SELECT id,quantity FROM cart_items WHERE cart_id=? AND product_id=?'); $s->execute([$cart_id,$pid]); $ci=$s->fetch();
        if($ci){ $pdo->prepare('UPDATE cart_items SET quantity=quantity+? WHERE id=?')->execute([$qty,$ci['id']]); }
        else { $pdo->prepare('INSERT INTO cart_items (cart_id,product_id,quantity,price_at_added,created_at) VALUES (?,?,?,?,NOW())')->execute([$cart_id,$pid,$qty,$prod['price']]); }
        flash_set('success','Item added to cart'); header('Location: cart.php'); exit;
    }
}
// fetch items
$items = $pdo->prepare('SELECT ci.*, p.title FROM cart_items ci JOIN products p ON ci.product_id=p.id WHERE ci.cart_id=?'); $items->execute([$cart_id]); $items = $items->fetchAll();
require 'header.php';
?>
<div class="container">
  <h2>Your Cart</h2>
  <?php if($m=flash_get('success')) echo '<div class="success">'.e($m).'</div>'; ?>
  <table>
    <tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
    <?php $total=0; foreach($items as $it): $sub=$it['quantity']*$it['price_at_added']; $total+=$sub; ?>
      <tr>
        <td><?=e($it['title'])?></td>
        <td><?=e($it['quantity'])?></td>
        <td>$<?=e($it['price_at_added'])?></td>
        <td>$<?=number_format($sub,2)?></td>
      </tr>
    <?php endforeach; ?>
  </table>
  <p>Total: $<?=number_format($total,2)?></p>
  <a href="checkout.php">Proceed to Checkout</a>
</div>
<?php require 'footer.php'; ?>

