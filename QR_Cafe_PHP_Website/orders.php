<?php
require_once __DIR__ . '/config/db.php';
$db = getDB();

// Handle Order Status Update (Practical 3 DML Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $orderId = intval($_POST['order_id']);
    $newStatus = trim($_POST['status']);
    if (in_array($newStatus, ['PLACED', 'PREPARING', 'SERVED', 'CANCELLED'])) {
        $stmt = $db->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
        $stmt->execute([$newStatus, $orderId]);
    }
    header("Location: orders.php");
    exit;
}

// ----------------------------------------------------------------------------
// PRACTICAL 6: NESTED SUBQUERIES & AGGREGATE CALCULATIONS
// ----------------------------------------------------------------------------
$totalRevenue = $db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE order_status != 'CANCELLED'")->fetchColumn();
$totalOrders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();

// Practical 6 Nested Subquery: Orders with spending greater than cafe average bill
$highValueOrders = $db->query("
    SELECT COUNT(*) FROM orders 
    WHERE total_amount > (SELECT COALESCE(AVG(total_amount), 0) FROM orders WHERE order_status != 'CANCELLED')
")->fetchColumn();

$avgBill = $db->query("SELECT COALESCE(AVG(total_amount), 0) FROM orders WHERE order_status != 'CANCELLED'")->fetchColumn();

// Filter status
$filter = strtolower(trim($_GET['status'] ?? 'all'));
$query = "
    SELECT o.*, c.customer_name, c.phone, c.age
    FROM orders o
    JOIN customers c ON o.customer_id = c.customer_id
";
if ($filter !== 'all' && in_array(strtoupper($filter), ['PLACED', 'PREPARING', 'SERVED', 'CANCELLED'])) {
    $query .= " WHERE o.order_status = " . $db->quote(strtoupper($filter));
}
$query .= " ORDER BY o.order_date DESC";

// Practical 5: Joins & Practical 3: Select
$orders = $db->query($query)->fetchAll();

$pageTitle = 'Orders & Bills — QR Cafe';
require_once __DIR__ . '/includes/header.php';
?>

<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h1 style="font-size: 2.2rem; font-weight: 800; color: #0f172a;">Orders & Billing History</h1>
    <p style="color: #64748b;">Live orders, dining bills, and kitchen preparation status.</p>
  </div>
  <a href="index.php" class="btn btn-primary">➕ Place New Order</a>
</div>

<!-- Practical 6 KPI Statistics (Nested Subqueries) -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-label">Total Revenue</div>
    <div class="stat-value" style="color: #16a34a;">₹<?= number_format($totalRevenue, 2) ?></div>
  </div>

  <div class="stat-card">
    <div class="stat-label">Total Orders Placed</div>
    <div class="stat-value"><?= $totalOrders ?></div>
  </div>

  <div class="stat-card">
    <div class="stat-label">Average Order Value</div>
    <div class="stat-value" style="color: #2563eb;">₹<?= number_format($avgBill, 2) ?></div>
  </div>

  <div class="stat-card">
    <div class="stat-label">High-Value Orders (> Avg)</div>
    <div class="stat-value" style="color: #ea580c;"><?= $highValueOrders ?></div>
  </div>
</div>

<!-- Status Filter Tabs -->
<div style="margin-bottom: 1.5rem; display: flex; gap: 8px; flex-wrap: wrap;">
  <a href="orders.php?status=all" class="btn btn-sm <?= ($filter === 'all') ? 'btn-primary' : 'btn-outline' ?>">All Orders</a>
  <a href="orders.php?status=placed" class="btn btn-sm <?= ($filter === 'placed') ? 'btn-primary' : 'btn-outline' ?>">Placed</a>
  <a href="orders.php?status=preparing" class="btn btn-sm <?= ($filter === 'preparing') ? 'btn-primary' : 'btn-outline' ?>">Preparing</a>
  <a href="orders.php?status=served" class="btn btn-sm <?= ($filter === 'served') ? 'btn-primary' : 'btn-outline' ?>">Served</a>
  <a href="orders.php?status=cancelled" class="btn btn-sm <?= ($filter === 'cancelled') ? 'btn-primary' : 'btn-outline' ?>">Cancelled</a>
</div>

<!-- Orders Table -->
<div class="card" style="padding: 0; overflow: hidden;">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Order #</th>
          <th>Table</th>
          <th>Customer Details</th>
          <th>Items Ordered</th>
          <th>Total Bill</th>
          <th>Status</th>
          <th>Update Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($orders)): ?>
          <tr>
            <td colspan="7" style="text-align: center; padding: 3rem; color: #64748b;">
              No orders found for this filter. <a href="index.php" style="color: #2563eb; font-weight: 700;">Click here to place an order</a>.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($orders as $o): 
            // Practical 5 Inner Join: Order Items with Menu Items
            $itemsStmt = $db->prepare("
                SELECT oi.quantity, oi.unit_price, m.item_name
                FROM order_items oi
                JOIN menu_items m ON oi.item_id = m.item_id
                WHERE oi.order_id = ?
            ");
            $itemsStmt->execute([$o['order_id']]);
            $lineItems = $itemsStmt->fetchAll();
          ?>
            <tr>
              <td style="font-weight: 800; color: #0f172a;">#<?= $o['order_id'] ?></td>
              <td>
                <span style="font-weight: 700; background: #f1f5f9; padding: 3px 8px; border-radius: 4px; font-size: 0.85rem;">
                  <?= htmlspecialchars($o['table_number']) ?>
                </span>
              </td>
              <td>
                <div style="font-weight: 700; color: #0f172a;"><?= htmlspecialchars($o['customer_name']) ?></div>
                <div style="font-size: 0.8rem; color: #64748b;">📞 <?= htmlspecialchars($o['phone']) ?> (Age: <?= $o['age'] ?>)</div>
              </td>
              <td style="font-size: 0.85rem;">
                <?php foreach ($lineItems as $li): ?>
                  <div>• <?= htmlspecialchars($li['item_name']) ?> <span style="color: #64748b;">(×<?= $li['quantity'] ?>)</span></div>
                <?php endforeach; ?>
              </td>
              <td>
                <span style="font-size: 1.05rem; font-weight: 800; color: #16a34a;">₹<?= number_format($o['total_amount'], 2) ?></span>
              </td>
              <td>
                <span class="badge badge-<?= strtolower($o['order_status']) ?>">
                  <?= htmlspecialchars($o['order_status']) ?>
                </span>
                <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 2px;">
                  <?= date('h:i A', strtotime($o['order_date'])) ?>
                </div>
              </td>
              <td>
                <form method="POST" action="orders.php" style="display: flex; gap: 6px;">
                  <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">
                  <select name="status" class="form-control" style="padding: 4px 8px; font-size: 0.85rem; width: auto;" onchange="this.form.submit()">
                    <option value="PLACED" <?= ($o['order_status'] === 'PLACED') ? 'selected' : '' ?>>Placed</option>
                    <option value="PREPARING" <?= ($o['order_status'] === 'PREPARING') ? 'selected' : '' ?>>Preparing</option>
                    <option value="SERVED" <?= ($o['order_status'] === 'SERVED') ? 'selected' : '' ?>>Served</option>
                    <option value="CANCELLED" <?= ($o['order_status'] === 'CANCELLED') ? 'selected' : '' ?>>Cancelled</option>
                  </select>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
