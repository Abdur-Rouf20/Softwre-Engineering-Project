<?php
require_once 'config.php';
session_start();

// Update quantity
if (isset($_POST['update_qty'])) {
    foreach ($_POST['qty'] as $id => $quantity) {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id]['quantity'] = (int)$quantity;
        }
    }
    header("Location: cart.php");
    exit;
}

// Remove item
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    unset($_SESSION['cart'][$remove_id]);
    header("Location: cart.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
require 'header.php';
?>

<style>
.cart-table {
    width: 100%;
    border-collapse: collapse;
}
.cart-table th, .cart-table td {
    border-bottom: 1px solid #ddd;
    padding: 10px;
}
.cart-img {
    width: 70px;
    height: 70px;
    object-fit: cover;
}
.total-box {
    margin-top: 20px;
    text-align: right;
}
</style>

<div class="container">

<h2>Your Cart</h2>

<?php if (empty($cart)): ?>
    <p>Your cart is empty.</p>
<?php else: ?>

<form method="POST">

<table class="cart-table">
    <tr>
        <th>Image</th>
        <th>Product</th>
        <th>Price</th>
        <th>Qty</th>
        <th>Subtotal</th>
        <th>Action</th>
    </tr>

    <?php
    $total = 0;
    foreach ($cart as $item):
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
    ?>
    <tr>
        <td><img src="<?= $item['image'] ?>" class="cart-img"></td>
        <td><?= $item['title'] ?></td>
        <td>$<?= $item['price'] ?></td>

        <td>
            <input 
                type="number" 
                name="qty[<?= $item['id'] ?>]" 
                value="<?= $item['quantity'] ?>" 
                min="1"
                style="width:60px;"
            >
        </td>

        <td>$<?= number_format($subtotal, 2) ?></td>

        <td>
            <a href="cart.php?remove=<?= $item['id'] ?>" class="btn btn-light">
                Remove
            </a>
        </td>
    </tr>

    <?php endforeach; ?>
</table>

<div class="total-box">
    <h3>Total: $<?= number_format($total, 2) ?></h3>
    <button type="submit" name="update_qty" class="btn">Update Cart</button>
    <a href="checkout.php" class="btn">Proceed to Checkout</a>
</div>

</form>

<?php endif; ?>

</div>

<?php require 'footer.php'; ?>
