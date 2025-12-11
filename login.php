<?php
// --------------------------------------------------
// File: login.php (Login + Register combined)
// Notes: admin should not register. Registration allowed for seller and buyer.
// --------------------------------------------------
require_once 'config.php';
$pdo = getPDO();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['action']) && $_POST['action']==='register'){
        // registration
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'buyer';
        if(!in_array($role,['buyer','seller'])) $role='buyer';
        if($name==='') $errors['name']='Name required';
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email']='Valid email required';
        if(strlen($password) < 6) $errors['password']='Password must be >=6 chars';
        // check existing
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if($stmt->fetch()) $errors['email']='Email already registered';

        if(empty($errors)){
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('INSERT INTO users (name,email,password_hash,role,created_at) VALUES (?,?,?,?,NOW())');
            $stmt->execute([$name,$email,$hash,$role]);
            flash_set('success','Registration successful. Please login.');
            header('Location: login.php'); exit;
        }

    } else {
        // login
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email']='Valid email required';
        if($password==='') $errors['password']='Password required';
        if(empty($errors)){
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email=? LIMIT 1');
            $stmt->execute([$email]);
            $u = $stmt->fetch();
            if($u && password_verify($password, $u['password_hash'])){
                unset($u['password_hash']);
                $_SESSION['user']=$u;
                // update last_login_at
                $s = $pdo->prepare('UPDATE users SET last_login_at=NOW() WHERE id=?'); $s->execute([$u['id']]);
                header('Location: index.php'); exit;
            } else {
                $errors['login']='Invalid credentials';
            }
        }
    }
}

require 'header.php';
?>
<div class="container">
  <?php if($msg = flash_get('success')): ?><div class="success"><?=e($msg)?></div><?php endif; ?>
  <h2>Login</h2>
  <?php if(!empty($errors)): ?><div class="errors"><?php foreach($errors as $err) echo '<p>'.e($err).'</p>'; ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="action" value="login">
    <label>Email<input type="email" name="email" required></label>
    <label>Password<input type="password" name="password" required></label>
    <label><input type="checkbox" name="remember"> Remember me</label>
    <button type="submit">Login</button>
  </form>

  <hr>
  <h2>Register (buyer / seller)</h2>
  <form method="post">
    <input type="hidden" name="action" value="register">
    <label>Name<input type="text" name="name" required></label>
    <label>Email<input type="email" name="email" required></label>
    <label>Password<input type="password" name="password" required minlength="6"></label>
    <label>Role
      <select name="role">
        <option value="buyer">Buyer</option>
        <option value="seller">Seller</option>
      </select>
    </label>
    <button type="submit">Register</button>
  </form>
</div>
<?php require 'footer.php'; ?>

