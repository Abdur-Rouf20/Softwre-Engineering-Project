<?php
require_once '../config.php'; // session already started here

if(!isLoggedIn()){
    echo json_encode(['status'=>'error','message'=>'Login required']);
    exit;
}

$pid = (int)($_GET['product_id'] ?? 0);
if($pid <= 0){
    echo json_encode(['status'=>'error','message'=>'Invalid product']);
    exit;
}

// get product
$stmt = $pdo->prepare('SELECT * FROM products WHERE id=? AND product_status="active"');
$stmt->execute([$pid]);
$product = $stmt->fetch();

if(!$product){
    echo json_encode(['status'=>'error','message'=>'Invalid product']);
    exit;
}

// store in session cart
$cart = $_SESSION['cart'] ?? [];
if(isset($cart[$pid])){
    $cart[$pid]['quantity']++;
} else {
    $cart[$pid] = [
        'id' => $product['id'],
        'title' => $product['title'],
        'price' => $product['price'],
        'quantity' => 1
    ];
}
$_SESSION['cart'] = $cart;

echo json_encode(['status'=>'success','message'=>'Product added to cart']);
