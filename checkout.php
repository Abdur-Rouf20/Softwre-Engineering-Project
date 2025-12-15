<?php
require_once 'config.php';

requireLogin(); // make sure user is logged in

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    echo "<h2>Your cart is empty.</h2>";
    exit;
}

$total = 0;
foreach ($cart as $item) $total += $item['price'] * $item['quantity'];

require 'header.php';
?>
<div class="container">
<h2>Checkout</h2>

<form action="place_order.php" method="POST">
<h3>Shipping Address</h3>
<textarea name="shipping_address" required><?= e($_SESSION['user']['address'] ?? '') ?></textarea>

<h3>Payment Method</h3>
<label><input type="radio" name="payment_method" value="cod" checked> Cash on Delivery</label><br>
<label><input type="radio" name="payment_method" value="stripe"> Pay with Card (Stripe)</label><br><br>

<h3>Order Summary</h3>
<?php foreach($cart as $item): ?>
    <p><?= e($item['title']) ?> × <?= $item['quantity'] ?> = $<?= number_format($item['price']*$item['quantity'],2) ?></p>
<?php endforeach; ?>
<p><strong>Total: $<?= number_format($total,2) ?></strong></p>

<button type="submit" class="btn">Place Order</button>
</form>
</div>
<?php require 'footer.php'; ?>
