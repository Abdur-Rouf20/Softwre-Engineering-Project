<?php

// --------------------------------------------------
// File: admin_add_category.php
// --------------------------------------------------
require_once 'config.php';
requireRole('admin');
$pdo = getPDO();
$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name = trim($_POST['name'] ?? '');
    $parent = $_POST['parent_id'] ?: null;
    $slug = trim($_POST['slug'] ?? '');
    if($name==='') $errors['name']='Name required';
    if($slug==='') $slug = strtolower(preg_replace('/[^a-z0-9]+/','-', $name));
    // ensure unique slug
    $stmt=$pdo->prepare('SELECT id FROM categories WHERE slug=?'); $stmt->execute([$slug]);
    if($stmt->fetch()) $errors['slug']='Slug already exists';
    if(empty($errors)){
        $s=$pdo->prepare('INSERT INTO categories (parent_id,name,slug,description,created_at) VALUES (?,?,?,?,NOW())');
        $s->execute([$parent,$name,$slug,$_POST['description'] ?? null]);
        flash_set('success','Category added'); header('Location: admin_dashboard.php'); exit;
    }
}
require 'header.php';
$cats = $pdo->query('SELECT * FROM categories')->fetchAll();
?>
<div class="container">
  <h2>Add Category</h2>
  <?php if($msg=flash_get('success')): ?><div class="success"><?=e($msg)?></div><?php endif;?>
  <?php if($errors): ?><div class="errors"><?php foreach($errors as $er) echo '<p>'.e($er).'</p>'; ?></div><?php endif; ?>
  <form method="post">
    <label>Name<input type="text" name="name" required></label>
    <label>Slug<input type="text" name="slug"></label>
    <label>Parent
      <select name="parent_id">
        <option value="">-- none --</option>
        <?php foreach($cats as $c): ?><option value="<?=e($c['id'])?>"><?=e($c['name'])?></option><?php endforeach; ?>
      </select>
    </label>
    <label>Description<textarea name="description"></textarea></label>
    <button type="submit">Create</button>
  </form>
</div>
<?php require 'footer.php'; ?>

