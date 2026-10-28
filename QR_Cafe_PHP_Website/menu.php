<?php
require_once __DIR__ . '/config/db.php';
$db = getDB();

$message = null;
$error = null;

// Handle Add Item (Practical 3 DML Insert & Practical 4 Constraints)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['item_name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = floatval($_POST['price'] ?? 0);

    if (empty($name) || empty($category) || $price <= 0) {
        $error = "Please provide a valid item name, category, and price greater than 0.";
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO menu_items (item_name, category, price, is_available) VALUES (?, ?, ?, 'Y')");
            $stmt->execute([$name, $category, $price]);
            $message = "Item '{$name}' added to menu successfully!";
        } catch (Exception $e) {
            $error = "Failed to add item: " . $e->getMessage();
        }
    }
}

// Handle Toggle Availability (Practical 3 DML Update)
if (isset($_GET['toggle'])) {
    $itemId = intval($_GET['toggle']);
    $curr = $db->query("SELECT is_available FROM menu_items WHERE item_id = {$itemId}")->fetchColumn();
    $new = ($curr === 'Y') ? 'N' : 'Y';
    $db->query("UPDATE menu_items SET is_available = '{$new}' WHERE item_id = {$itemId}");
    header("Location: menu.php");
    exit;
}

// Handle Delete Item (Practical 3 DML Delete)
if (isset($_GET['delete'])) {
    $itemId = intval($_GET['delete']);
    try {
        $db->query("DELETE FROM menu_items WHERE item_id = {$itemId}");
        header("Location: menu.php");
        exit;
    } catch (Exception $e) {
        $error = "Cannot delete this item as it is linked to past orders (Practical 4 Foreign Key protection).";
    }
}

// Practical 3(e): LIKE '%' Wildcard Search and Practical 7 View
$search = trim($_GET['search'] ?? '');
$categoryFilter = trim($_GET['category'] ?? 'all');

if (!empty($search)) {
    // Wildcard Pattern Matching
    $stmt = $db->prepare("SELECT * FROM menu_items WHERE item_name LIKE ? ORDER BY category, item_name");
    $stmt->execute(["%{$search}%"]);
    $items = $stmt->fetchAll();
} elseif ($categoryFilter === 'Beverages') {
    // Query Practical 7 View: Active Beverages
    $items = $db->query("SELECT item_id, item_name, price, category, 'Y' AS is_available FROM v_active_beverages ORDER BY item_name")->fetchAll();
} else {
    $items = $db->query("SELECT * FROM menu_items ORDER BY category, item_name")->fetchAll();
}

$pageTitle = 'Manage Menu — QR Cafe';
require_once __DIR__ . '/includes/header.php';
?>

<div style="margin-bottom: 2rem;">
  <h1 style="font-size: 2.2rem; font-weight: 800; color: #0f172a;">Menu Management</h1>
  <p style="color: #64748b;">Add new items, adjust pricing, and toggle in-stock availability (CRUD Operations).</p>
</div>

<?php if ($message): ?>
  <div class="alert alert-success">
    <span>✅</span>
    <div><?= htmlspecialchars($message) ?></div>
  </div>
<?php endif; ?>

<?php if ($error): ?>
  <div class="alert alert-danger">
    <span>⚠️</span>
    <div><?= htmlspecialchars($error) ?></div>
  </div>
<?php endif; ?>

<!-- Add Item Form Card -->
<div class="card">
  <div class="card-title">
    <span>➕</span> Add New Cafe Menu Item
  </div>

  <form method="POST" action="menu.php" class="form-grid" style="align-items: flex-end;">
    <input type="hidden" name="action" value="add">

    <div class="form-group" style="margin-bottom: 0;">
      <label class="form-label">Item Name *</label>
      <input type="text" name="item_name" class="form-control" placeholder="e.g. Espresso Romano" required>
    </div>

    <div class="form-group" style="margin-bottom: 0;">
      <label class="form-label">Category *</label>
      <select name="category" class="form-control" required>
        <option value="Beverages">Beverages</option>
        <option value="Food">Food / Snacks</option>
        <option value="Desserts">Desserts</option>
      </select>
    </div>

    <div class="form-group" style="margin-bottom: 0;">
      <label class="form-label">Price (₹) *</label>
      <input type="number" step="0.5" min="1" name="price" class="form-control" placeholder="150.00" required>
    </div>

    <div>
      <button type="submit" class="btn btn-primary" style="width: 100%;">
        Save Item
      </button>
    </div>
  </form>
</div>

<!-- Search & Filter Bar -->
<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
  <!-- Practical 3e LIKE Wildcard Form -->
  <form method="GET" action="menu.php" style="display: flex; gap: 8px; max-width: 400px; width: 100%;">
    <input type="text" name="search" class="form-control" placeholder="🔍 Search by name (LIKE '%kw%')" value="<?= htmlspecialchars($search) ?>">
    <button type="submit" class="btn btn-outline">Search</button>
    <?php if (!empty($search)): ?>
      <a href="menu.php" class="btn btn-outline">Clear</a>
    <?php endif; ?>
  </form>

  <div style="display: flex; gap: 8px;">
    <a href="menu.php?category=all" class="btn btn-sm <?= ($categoryFilter === 'all') ? 'btn-primary' : 'btn-outline' ?>">All</a>
    <a href="menu.php?category=Beverages" class="btn btn-sm <?= ($categoryFilter === 'Beverages') ? 'btn-primary' : 'btn-outline' ?>">Beverages (Practical 7 View)</a>
  </div>
</div>

<!-- Menu Items Table -->
<div class="card" style="padding: 0; overflow: hidden;">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Item Name</th>
          <th>Category</th>
          <th>Price</th>
          <th>Availability</th>
          <th style="text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($items)): ?>
          <tr>
            <td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748b;">
              No menu items match your search.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($items as $it): ?>
            <tr>
              <td style="font-weight: 700; color: #64748b;">#<?= $it['item_id'] ?></td>
              <td style="font-weight: 700; color: #0f172a;"><?= htmlspecialchars($it['item_name']) ?></td>
              <td>
                <span style="background: #f1f5f9; padding: 3px 8px; border-radius: 4px; font-size: 0.82rem; font-weight: 600;">
                  <?= htmlspecialchars($it['category']) ?>
                </span>
              </td>
              <td style="font-weight: 800; color: #16a34a;">₹<?= number_format($it['price'], 2) ?></td>
              <td>
                <?php if ($it['is_available'] === 'Y'): ?>
                  <span class="badge badge-served">In Stock</span>
                <?php else: ?>
                  <span class="badge badge-cancelled">Out of Stock</span>
                <?php endif; ?>
              </td>
              <td style="text-align: right;">
                <a href="menu.php?toggle=<?= $it['item_id'] ?>" class="btn btn-sm btn-outline" style="font-size: 0.78rem;">
                  <?= ($it['is_available'] === 'Y') ? 'Set Out of Stock' : 'Set Available' ?>
                </a>
                <a href="menu.php?delete=<?= $it['item_id'] ?>" class="btn btn-sm btn-danger" style="font-size: 0.78rem;" onclick="return confirm('Delete this menu item?')">
                  Delete
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
