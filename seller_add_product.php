
<?php
require_once 'config.php';
requireRole('seller');
$pdo = getPDO();
$cats = $pdo->query('SELECT * FROM categories')->fetchAll();
$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
    $title = trim($_POST['title'] ?? '');
    $price = $_POST['price'] ?? 0;
    $qty = (int)($_POST['quantity'] ?? 0);
    $cat = $_POST['category_id'] ?: null;
    if($title==='') $errors['title']='Title required';
    if(!is_numeric($price) || $price < 0) $errors['price']='Valid price required';
    if($qty<0) $errors['quantity']='Valid quantity';
    if(empty($errors)){
        $slug = strtolower(preg_replace('/[^a-z0-9]+/','-', $title));
        $s = $pdo->prepare('INSERT INTO products (seller_id,category_id,title,slug,description,price,quantity,product_status,created_at) VALUES (?,?,?,?,?,?,?,?,NOW())');
        $s->execute([$_SESSION['user']['id'],$cat,$title,$slug,$_POST['description'] ?? null,$price,$qty,'pending']);
        $pid = $pdo->lastInsertId();
        // handle image upload optionally
        flash_set('success','Product submitted for approval');
        header('Location: seller_dashboard.php'); exit;
    }
}
require 'header.php';
?>
<div class="container">
  <h2>Add Product</h2>
  <?php if($errors){ foreach($errors as $er) echo '<p class="errors">'.e($er).'</p>'; } ?>
  <form method="post" enctype="multipart/form-data">
    <label>Title<input type="text" name="title" required></label>
    <label>Price<input type="number" step="0.01" name="price" required></label>
    <label>Quantity<input type="number" name="quantity" required></label>
    <label>Category<select name="category_id"><?php foreach($cats as $c) echo '<option value="'.e($c['id']).'">'.e($c['name']).'</option>'; ?></select></label>
    <label>Description<textarea name="description"></textarea></label>
    <button type="submit">Create</button>
  </form>
</div>
<?php require 'footer.php'; ?>
