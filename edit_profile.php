




// --------------------------------------------------
// File: edit_profile.php
// --------------------------------------------------
<?php
require_once 'config.php';
requireLogin();
$pdo = getPDO();
$uid = $_SESSION['user']['id'];
$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    if($name==='') $errors['name']='Name required';
    if(empty($errors)){
        $pdo->prepare('UPDATE users SET name=?, phone=?, address=?, updated_at=NOW() WHERE id=?')->execute([$name,$phone,$address,$uid]);
        flash_set('success','Profile updated'); header('Location: buyer_dashboard.php'); exit;
    }
}
$user = $pdo->prepare('SELECT * FROM users WHERE id=?'); $user->execute([$uid]); $user = $user->fetch();
require 'header.php';
?>
<div class="container">
  <h2>Edit Profile</h2>
  <?php if($m=flash_get('success')) echo '<div class="success">'.e($m).'</div>'; ?>
  <?php if($errors) foreach($errors as $er) echo '<p class="errors">'.e($er).'</p>'; ?>
  <form method="post">
    <label>Name<input type="text" name="name" value="<?=e($user['name'])?>" required></label>
    <label>Phone<input type="text" name="phone" value="<?=e($user['phone'])?>"></label>
    <label>Address<textarea name="address"><?=e($user['address'])?></textarea></label>
    <button type="submit">Save</button>
  </form>
</div>
<?php require 'footer.php'; ?>

