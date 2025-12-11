// --------------------------------------------------
// File: admin_actions.php (handles publish/reject)
// --------------------------------------------------
<?php
require_once 'config.php';
requireRole('admin');
$pdo = getPDO();
if($_SERVER['REQUEST_METHOD']==='POST'){
    $action = $_POST['action'] ?? '';
    $pid = (int)($_POST['product_id'] ?? 0);
    if($action==='publish'){
        $s=$pdo->prepare('UPDATE products SET product_status="active", approved_at=NOW() WHERE id=?'); $s->execute([$pid]);
        flash_set('success','Product published');
    } elseif($action==='reject'){
        $s=$pdo->prepare('UPDATE products SET product_status="removed" WHERE id=?'); $s->execute([$pid]);
        flash_set('success','Product rejected and removed');
    }
}
header('Location: admin_dashboard.php'); exit;
?>
