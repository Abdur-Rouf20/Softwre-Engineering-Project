
<?php

// --------------------------------------------------
// File: index.php (Home page)
// shows up to 12 products, search, pagination
// --------------------------------------------------
require_once 'config.php';
$pdo = getPDO();
$perPage = 12;
$page = max(1, (int)($_GET['page'] ?? 1));
$search = trim($_GET['q'] ?? '');
$where = '1=1'; $params = [];
if($search !== ''){ $where .= ' AND (title LIKE ? OR description LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; }

$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE product_status='active' AND $where");
$totalStmt->execute($params);
$total = $totalStmt->fetchColumn();
$pages = max(1, ceil($total/$perPage));
$offset = ($page-1)*$perPage;

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE product_status='active' AND $where ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$products = $stmt->fetchAll();
require 'header.php';
?>
<div class="container">
  <form method="get">
    <input type="text" name="q" placeholder="Search products" value="<?=e($search)?>">
    <button type="submit">Search</button>
  </form>
  <h2>Products</h2>
  <div class="grid">
    <?php foreach($products as $p): ?>
      <div class="card">
        <img src="<?=e($p['product_images'] ?? '/assets/placeholder.png')?>" alt="<?=e($p['title'])?>">
        <h3><?=e($p['title'])?></h3>
        <p><?=e(substr($p['description'] ?? '',0,120))?></p>
        <p>Price: $<?=e($p['price'])?></p>
        <a href="product_view.php?id=<?=e($p['id'])?>">View</a>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="pagination">
    <?php for($i=1;$i<=$pages;$i++): ?>
      <a href="?page=<?=$i?>&q=<?=urlencode($search)?>" <?=($i==$page)?'class="active"':''?>><?=$i?></a>
    <?php endfor; ?>
  </div>
</div>
<?php require 'footer.php'; ?>

