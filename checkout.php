// --------------------------------------------------
// File: checkout.php
// --------------------------------------------------
<?php
require_once 'config.php';
requireLogin();
$pdo = getPDO();
$uid = $_SESSION['user']['id'];
// retrieve cart and items similar to cart.php
$cart = $pdo->prepare('SELECT * FROM carts WHERE user_id=?'); $cart->execute([$uid]); $cart = $cart->fetch();
if(!$cart){ header('Location: cart.php'); exit; }
$items = $pdo->prepare('SELECT ci.*, p.title FROM cart_items ci JOIN products p ON ci.product_id=p.id WHERE ci.cart_id=?'); $items->execute([$cart['id']]); $items = $items->fetchAll();
if(!$items){ flash_set('error','Cart empty'); header('Location: cart.php'); exit; }
$total=0; foreach($items as $it) $total += $it['quantity']*$it['price_at_added'];
$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
    $address = trim($_POST['shipping_address'] ?? '');
    $payment_method = $_POST['payment_method'] ?? 'cod';
    if($address==='') $errors['address']='Shipping address required';
    if(empty($errors)){
        // create order
        $pdo->beginTransaction();
        try{
            $orderNumber = 'ORD'.time().rand(100,999);
            $s = $pdo->prepare('INSERT INTO orders (user_id,seller_id,order_number,total_amount,shipping_address,payment_method,order_status,created_at) VALUES (?,?,?,?,?,?,"pending",NOW())');
            // seller_id: simple approach get first product's seller
            $seller_id = null;
            foreach($items as $it){ $p = $pdo->prepare('SELECT seller_id FROM products WHERE id=?'); $p->execute([$it['product_id']]); $r=$p->fetch(); if($r){ $seller_id = $r['seller_id']; break;} }
            $s->execute([$uid,$seller_id,$orderNumber,$total,$address,$payment_method]);
            $order_id = $pdo->lastInsertId();
            foreach($items as $it){
                $sub = $it['quantity']*$it['price_at_added'];
                $pdo->prepare('INSERT INTO order_items (order_id,product_id,seller_id,unit_price,quantity,subtotal,created_at) VALUES (?,?,?,?,?,?,NOW())')
                    ->execute([$order_id,$it['product_id'],$seller_id,$it['price_at_added'],$it['quantity'],$sub]);
            }
            // clear cart
            $pdo->prepare('DELETE FROM cart_items WHERE cart_id=?')->execute([$cart['id']]);
            $pdo->commit();
            // if stripe chosen, redirect to payment page
            if($payment_method==='stripe'){
                header('Location: payment.php?order_id='.$order_id); exit;
            }
            flash_set('success','Order placed successfully'); header('Location: buyer_dashboard.php'); exit;
        } catch(Exception $e){ $pdo->rollBack(); $errors['system']=$e->getMessage(); }
    }
}
require 'header.php';
?>
<div class="container">
  <h2>Checkout</h2>
  <?php if($errors) foreach($errors as $er) echo '<p class="errors">'.e($er).'</p>'; ?>
  <form method="post">
    <label>Shipping Address<textarea name="shipping_address" required><?=e($_SESSION['user']['address'] ?? '')?></textarea></label>
    <label>Payment Method
      <select name="payment_method">
        <option value="cod">Cash on Delivery</option>
        <option value="stripe">Pay with Card (Stripe)</option>
      </select>
    </label>
    <button type="submit">Place Order ($<?=number_format($total,2)?>)</button>
  </form>
</div>
<?php require 'footer.php'; ?>

