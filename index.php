<?php

require_once 'config.php';
$pdo = getPDO();

$perPage = 12; // 3 per row × 4 rows
$page = max(1, (int)($_GET['page'] ?? 1));
$search = trim($_GET['q'] ?? '');

$where = '1=1';
$params = [];

if ($search !== '') {
    $where .= ' AND (title LIKE ? OR description LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE product_status='active' AND $where");
$totalStmt->execute($params);
$total = $totalStmt->fetchColumn();
$pages = max(1, ceil($total / $perPage));
$offset = ($page - 1) * $perPage;

$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE product_status='active' AND $where 
    ORDER BY created_at DESC 
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$products = $stmt->fetchAll();

require 'header.php';
?>

<style>
/* Grid layout: 3 cards in a row */
.grid {
    margin-top: 20px;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
}

/* Product card styling */
.product-card {
    background: #fff;
    border-radius: 10px;
    padding: 15px;
    border: 1px solid #ddd;
    transition: 0.3s;
}

.product-card:hover {
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    border-color: var(--primary);
}

.product-card img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    border-radius: 8px;
}

.product-card h3 {
    margin: 10px 0 6px;
}

.product-card p {
    margin: 6px 0;
}

/* Buttons */
.card-actions {
    margin-top: 12px;
    display: flex;
    gap: 10px;
}

.card-actions .btn {
    flex: 1;
    text-align: center;
}
</style>

<div class="container">

  <form method="get">
    <input type="text" name="q" placeholder="Search products" value="<?= e($search) ?>">
    <button type="submit" class="btn">Search</button>
  </form>

  <h2>Products</h2>

  <div class="grid">
    <?php foreach ($products as $p): ?>
      <div class="product-card">

        <img src="<?= e($p['product_images'] ?? '/assets/placeholder.png') ?>" 
             alt="<?= e($p['title']) ?>">

        <h3><?= e($p['title']) ?></h3>
        <p><?= e(substr($p['description'] ?? '', 0, 120)) ?>...</p>
        <p><strong>Price: $<?= e($p['price']) ?></strong></p>

        <div class="card-actions">
          <a href="add_to_cart.php?id=<?= e($p['id']) ?>" class="btn btn-light">Add to Cart</a>
          <a href="checkout.php?id=<?= e($p['id']) ?>" class="btn">Buy Now</a>
        </div>

        <a href="product_view.php?id=<?= e($p['id']) ?>" style="display:block; margin-top: 10px;">
          View Details
        </a>

      </div>
    <?php endforeach; ?>
  </div>

  <!-- Pagination -->
  <div class="pagination">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
      <a 
        href="?page=<?= $i ?>&q=<?= urlencode($search) ?>" 
        class="<?= ($i == $page) ? 'active' : '' ?>"
      >
        <?= $i ?>
      </a>
    <?php endfor; ?>
  </div>

</div>

<?php require 'footer.php'; ?>
