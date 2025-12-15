<?php
require_once 'config.php';
session_start();
requireLogin();

$cart = $_SESSION['cart'] ?? [];
if (!$cart) die("Cart is empty.");

$payment_method = $_POST['payment_method'] ?? 'cod';
$shipping_address = $_POST['shipping_address'] ?? '';
$uid = $_SESSION['user']['id'];

$total = 0;
foreach ($cart as $item) $total += $item['price'] * $item['quantity'];

$pdo = getPDO();

if ($payment_method === 'cod') {
    $pdo->beginTransaction();
    try {
        $orderNumber = 'ORD'.time().rand(100,999);
        $stmt = $pdo->prepare('INSERT INTO orders (user_id,order_number,total_amount,shipping_address,payment_method,order_status,created_at) VALUES (?,?,?,?,?,"pending",NOW())');
        $stmt->execute([$uid,$orderNumber,$total,$shipping_address,'cod']);
        $order_id = $pdo->lastInsertId();

        foreach ($cart as $item) {
            $subtotal = $item['price']*$item['quantity'];
            $pdo->prepare('INSERT INTO order_items (order_id,product_id,seller_id,unit_price,quantity,subtotal,created_at) VALUES (?,?,?,?,?,?,NOW())')
                ->execute([$order_id,$item['id'],null,$item['price'],$item['quantity'],$subtotal]);
        }
        $pdo->commit();
        unset($_SESSION['cart']);
        header('Location: order_success.php?order_id='.$order_id);
        exit;
    } catch(Exception $e) {
        $pdo->rollBack();
        die("Error: ".$e->getMessage());
    }
}

if ($payment_method === 'stripe') {
    require_once 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey("YOUR_STRIPE_SECRET_KEY");

    $line_items = [];
    foreach ($cart as $item) {
        $line_items[] = [
            'price_data' => [
                'currency' => 'usd',
                'unit_amount' => $item['price']*100,
                'product_data' => ['name'=>$item['title']]
            ],
            'quantity' => $item['quantity']
        ];
    }

    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $line_items,
        'mode' => 'payment',
        'success_url' => 'http://localhost/software/Softwre-Engineering-Project/order_success.php?stripe=1',
        'cancel_url' => 'http://localhost/software/Softwre-Engineering-Project/checkout.php'
    ]);

    header("Location: " . $session->url);
    exit;
}
