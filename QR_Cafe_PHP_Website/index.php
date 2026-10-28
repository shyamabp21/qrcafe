<?php
require_once __DIR__ . '/config/db.php';
$db = getDB();

$error = null;
$successOrder = null;

// Handle Order Placement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tableNumber = trim($_POST['table_number'] ?? 'Table 1');
    $customerName = trim($_POST['customer_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $age = intval($_POST['age'] ?? 0);
    $selectedItems = $_POST['qty'] ?? [];

    if (empty($customerName) || empty($phone) || empty($tableNumber)) {
        $error = "Please provide your Name, Phone Number, and Table Number.";
    } else {
        try {
            // Practical 8: Transaction Control Language (TCL) - Begin Transaction
            $db->beginTransaction();

            // Find or Insert Customer (Target of Practical 10b Age Validation Trigger)
            $cStmt = $db->prepare("SELECT customer_id FROM customers WHERE phone = ?");
            $cStmt->execute([$phone]);
            $custId = $cStmt->fetchColumn();

            if (!$custId) {
                // If $age < 18, trigger trg_check_customer_age will abort and raise message alert!
                $insC = $db->prepare("INSERT INTO customers (customer_name, phone, age) VALUES (?, ?, ?)");
                $insC->execute([$customerName, $phone, $age]);
                $custId = $db->lastInsertId();
            } else {
                $updC = $db->prepare("UPDATE customers SET customer_name = ?, age = ? WHERE customer_id = ?");
                $updC->execute([$customerName, $age, $custId]);
            }

            // Calculate Total and Validate Line Items
            $totalAmount = 0;
            $itemsToInsert = [];

            foreach ($selectedItems as $itemId => $quantity) {
                $qty = intval($quantity);
                if ($qty > 0) {
                    $itemStmt = $db->prepare("SELECT item_name, price FROM menu_items WHERE item_id = ? AND is_available = 'Y'");
                    $itemStmt->execute([$itemId]);
                    $row = $itemStmt->fetch();
                    if ($row) {
                        $subtotal = floatval($row['price']) * $qty;
                        $totalAmount += $subtotal;
                        $itemsToInsert[] = [
                            'item_id' => $itemId,
                            'item_name' => $row['item_name'],
                            'price' => floatval($row['price']),
                            'quantity' => $qty,
                            'subtotal' => $subtotal
                        ];
                    }
                }
            }

            if (empty($itemsToInsert)) {
                throw new Exception("Please select at least 1 menu item with quantity greater than 0.");
            }

            // Insert into orders table (Practical 10a Trigger automatically audits this into audit_log)
            $oStmt = $db->prepare("
                INSERT INTO orders (customer_id, table_number, total_amount, order_status)
                VALUES (?, ?, ?, 'PLACED')
            ");
            $oStmt->execute([$custId, $tableNumber, $totalAmount]);
            $orderId = $db->lastInsertId();

            // Insert each line item into order_items table (Practical 4 Referential Integrity)
            $oiStmt = $db->prepare("
                INSERT INTO order_items (order_id, item_id, quantity, unit_price)
                VALUES (?, ?, ?, ?)
            ");
            foreach ($itemsToInsert as $item) {
                $oiStmt->execute([$orderId, $item['item_id'], $item['quantity'], $item['price']]);
            }

            // Practical 8 TCL - Commit Transaction
            $db->commit();

            $successOrder = [
                'order_id' => $orderId,
                'table_number' => $tableNumber,
                'customer_name' => $customerName,
                'phone' => $phone,
                'age' => $age,
                'total_amount' => $totalAmount,
                'items' => $itemsToInsert,
                'date' => date('Y-m-d h:i A')
            ];

        } catch (Exception $e) {
            // Practical 8 TCL - Rollback on Error
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $msg = $e->getMessage();
            if (stripos($msg, 'AGE ALERT') !== false) {
                $error = "AGE ALERT: Customer must be at least 18 years old to place an order at QR Cafe (Practical 10 Trigger Enforcement)!";
            } else {
                $error = "Order failed: " . $msg;
            }
        }
    }
}

// Fetch all available menu items (Practical 3 DML Select)
$menuItems = $db->query("SELECT * FROM menu_items WHERE is_available = 'Y' ORDER BY category, item_name")->fetchAll();

$pageTitle = 'Place Order — QR Cafe';
require_once __DIR__ . '/includes/header.php';
?>

<div style="margin-bottom: 2rem; text-align: center;">
  <h1 style="font-size: 2.2rem; font-weight: 800; color: #0f172a;">Welcome to QR Cafe</h1>
  <p style="color: #64748b; font-size: 1.05rem;">Enter your table details, pick your favorite snacks and coffees, and place your order instantly.</p>
</div>

<?php if ($error): ?>
  <div class="alert alert-danger">
    <span style="font-size: 1.3rem;">⚠️</span>
    <div><?= htmlspecialchars($error) ?></div>
  </div>
<?php endif; ?>

<?php if ($successOrder): ?>
  <!-- Practical 7 & 11: Bill Receipt -->
  <div class="receipt-card">
    <div class="receipt-header">
      <div style="font-size: 2rem; margin-bottom: 0.25rem;">☕</div>
      <h2 style="font-size: 1.4rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem;">QR CAFE RECEIPT</h2>
      <p style="font-size: 0.85rem; color: #64748b;">Order #<?= $successOrder['order_id'] ?> • Table: <strong><?= htmlspecialchars($successOrder['table_number']) ?></strong></p>
      <p style="font-size: 0.85rem; color: #64748b;">Customer: <?= htmlspecialchars($successOrder['customer_name']) ?> (Age: <?= $successOrder['age'] ?>)</p>
      <p style="font-size: 0.8rem; color: #94a3b8;"><?= $successOrder['date'] ?></p>
    </div>

    <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem; margin-bottom: 1rem;">
      <thead>
        <tr style="border-bottom: 1px dashed #cbd5e1; text-align: left;">
          <th style="padding: 6px 0;">Item</th>
          <th style="text-align: center; padding: 6px 0;">Qty</th>
          <th style="text-align: right; padding: 6px 0;">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($successOrder['items'] as $it): ?>
          <tr>
            <td style="padding: 6px 0;"><?= htmlspecialchars($it['item_name']) ?></td>
            <td style="text-align: center; padding: 6px 0;">× <?= $it['quantity'] ?></td>
            <td style="text-align: right; padding: 6px 0; font-weight: 700;">₹<?= number_format($it['subtotal'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="receipt-footer">
      <span style="font-size: 1.1rem; font-weight: 800; color: #0f172a;">Total Bill Amount:</span>
      <span style="font-size: 1.4rem; font-weight: 800; color: #16a34a;">₹<?= number_format($successOrder['total_amount'], 2) ?></span>
    </div>

    <div style="text-align: center; margin-top: 1.5rem; display: flex; gap: 10px; justify-content: center;">
      <a href="orders.php" class="btn btn-primary btn-sm">📋 View in Orders List</a>
      <a href="index.php" class="btn btn-outline btn-sm">➕ New Order</a>
    </div>
  </div>
<?php endif; ?>

<!-- Order Form -->
<form method="POST" action="index.php" id="orderForm">

  <!-- Step 1: Customer & Table Details -->
  <div class="card">
    <div class="card-title">
      <span>1️⃣</span> Table & Diner Information
    </div>
    
    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">Table Number *</label>
        <select name="table_number" class="form-control" required>
          <option value="Table 1">Table 1 (Indoor)</option>
          <option value="Table 2">Table 2 (Window)</option>
          <option value="Table 3">Table 3 (Outdoor)</option>
          <option value="Table 4">Table 4 (Balcony)</option>
          <option value="Table 5">Table 5 (Corner Booth)</option>
          <option value="Table 6">Table 6 (Large Group)</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Your Full Name *</label>
        <input type="text" name="customer_name" class="form-control" placeholder="e.g. Rahul Sharma" required>
      </div>

      <div class="form-group">
        <label class="form-label">Phone Number *</label>
        <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile number" required>
      </div>

      <div class="form-group">
        <label class="form-label">Age * (Trigger Rule: 18+)</label>
        <input type="number" name="age" class="form-control" min="1" max="100" value="20" required>
        <small style="color: #64748b; font-size: 0.78rem;">Trigger test: Entering &lt; 18 will activate Practical 10 Age Alert.</small>
      </div>
    </div>
  </div>

  <!-- Step 2: Menu Items Selection -->
  <div class="card">
    <div class="card-title" style="display: flex; justify-content: space-between; align-items: center;">
      <div>
        <span>2️⃣</span> Select Menu Items
      </div>
      <div style="font-size: 0.9rem; font-weight: 600; color: #2563eb;">
        Total Bill: ₹<span id="grandTotal">0.00</span>
      </div>
    </div>

    <div class="menu-grid">
      <?php foreach ($menuItems as $m): ?>
        <div class="menu-card">
          <div>
            <span class="menu-card-cat"><?= htmlspecialchars($m['category']) ?></span>
            <div class="menu-card-title"><?= htmlspecialchars($m['item_name']) ?></div>
          </div>
          
          <div class="menu-card-bottom">
            <span class="menu-card-price">₹<?= number_format($m['price'], 2) ?></span>

            <div class="qty-control">
              <button type="button" class="qty-btn" onclick="updateQty(<?= $m['item_id'] ?>, -1, <?= $m['price'] ?>)">−</button>
              <input type="number" id="qty_<?= $m['item_id'] ?>" name="qty[<?= $m['item_id'] ?>]" value="0" min="0" max="20" class="qty-input" readonly>
              <button type="button" class="qty-btn" onclick="updateQty(<?= $m['item_id'] ?>, 1, <?= $m['price'] ?>)">+</button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Submit Section -->
    <div style="text-align: center; padding-top: 1rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      <div style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">
        Estimated Bill: <span style="font-size: 1.4rem; color: #16a34a; font-weight: 800;">₹<span id="bottomTotal">0.00</span></span>
      </div>

      <button type="submit" class="btn btn-primary" style="padding: 10px 28px; font-size: 1rem;">
        🚀 Confirm & Place Order
      </button>
    </div>
  </div>

</form>

<script>
const itemPrices = {
  <?php foreach ($menuItems as $m): ?>
    <?= $m['item_id'] ?>: <?= $m['price'] ?>,
  <?php endforeach; ?>
};

function updateQty(itemId, change, price) {
  const input = document.getElementById('qty_' + itemId);
  let currentVal = parseInt(input.value) || 0;
  let newVal = currentVal + change;
  if (newVal < 0) newVal = 0;
  if (newVal > 20) newVal = 20;
  input.value = newVal;
  calculateTotal();
}

function calculateTotal() {
  let total = 0;
  for (const [id, price] of Object.entries(itemPrices)) {
    const input = document.getElementById('qty_' + id);
    if (input) {
      const qty = parseInt(input.value) || 0;
      total += qty * price;
    }
  }
  document.getElementById('grandTotal').innerText = total.toFixed(2);
  document.getElementById('bottomTotal').innerText = total.toFixed(2);
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
