

// --------------------------------------------------
// File: payment_process.php (server side process)
// --------------------------------------------------
<?php
require_once 'config.php';
requireLogin();
$pdo = getPDO();
if($_SERVER['REQUEST_METHOD']!=='POST') exit;
$order_id = (int)($_POST['order_id'] ?? 0);
$order = $pdo->prepare('SELECT * FROM orders WHERE id=? AND user_id=?'); $order->execute([$order_id, $_SESSION['user']['id']]); $order = $order->fetch();
if(!$order) { flash_set('error','Order not found'); header('Location: buyer_dashboard.php'); exit; }

// Server-side create Stripe PaymentIntent - pseudo code
// 
// \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
// $intent = \Stripe\PaymentIntent::create([
//   'amount' => intval($order['total_amount']*100),
//   'currency' => 'usd',
//   'metadata' => ['order_id'=>$order_id]
// ]);
// Save payment record
$pdo->prepare('INSERT INTO payments (order_id,payment_provider,provider_payment_id,amount,currency,status,created_at) VALUES (?,?,?,?,?,"succeeded",NOW())')
    ->execute([$order_id,'stripe','simulated_123', $order['total_amount'],'USD']);
// Update order status to confirmed (simulate)
$pdo->prepare('UPDATE orders SET order_status="confirmed" WHERE id=?')->execute([$order_id]);
flash_set('success','Payment recorded (simulated). Order confirmed.'); header('Location: buyer_dashboard.php'); exit;
?>
