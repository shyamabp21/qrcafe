<?php
require_once __DIR__ . '/../config/db.php';
$db = getDB();

// Handle Practical 10(b) Trigger Test (Age validation)
$triggerTestResult = null;
$triggerTestError = null;
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'test_trigger') {
    $testAge = intval($_POST['test_age'] ?? 0);
    $testName = trim($_POST['test_name'] ?? 'Test User');
    $testPhone = '99999' . rand(10000, 99999);

    try {
        $stmt = $db->prepare("INSERT INTO customers (customer_name, phone, age) VALUES (?, ?, ?)");
        $stmt->execute([$testName, $testPhone, $testAge]);
        $triggerTestResult = "Customer added successfully (Age: {$testAge} >= 18). Trigger allowed the INSERT.";
    } catch (PDOException $e) {
        $triggerTestError = $e->getMessage();
    }
}

// Active tab for practicals (1 to 11)
$activeP = isset($_GET['p']) ? intval($_GET['p']) : 1;
if ($activeP < 1 || $activeP > 11) $activeP = 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>DBMS 11 Practicals Lab — QR Cafe</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .prac-nav { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 24px; }
    .prac-btn { padding: 8px 14px; border-radius: 6px; font-size: 13px; font-weight: 700; text-decoration: none; border: 1px solid #cbd5e1; background: #fff; color: #334155; transition: all 0.2s; }
    .prac-btn:hover { background: #f1f5f9; border-color: #94a3b8; }
    .prac-btn.active { background: #1e3a8a; color: #fff; border-color: #1e3a8a; box-shadow: 0 2px 6px rgba(30,58,138,0.3); }
    .sql-box { background: #0f172a; color: #38bdf8; padding: 16px; border-radius: 8px; font-family: 'Consolas', monospace; font-size: 13px; overflow-x: auto; margin: 12px 0 20px; line-height: 1.5; }
    .sql-comment { color: #94a3b8; }
    .sql-kw { color: #f43f5e; font-weight: bold; }
    .sql-fn { color: #fbbf24; }
    .section-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; }
    .badge-prac { background: #e0e7ff; color: #3730a3; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
  </style>
</head>
<body style="background:#f8fafc;">

  <!-- Admin Navbar -->
  <header class="navbar" style="background:#1e293b;border-bottom:none;">
    <div class="nav-container">
      <a class="brand" href="index.php" style="color:#f8fafc;">
        <span class="brand-icon">⚡</span>
        <span>QR Cafe Admin</span>
      </a>
      <ul class="nav-links">
        <li><a href="index.php" style="color:#cbd5e1;">Dashboard</a></li>
        <li><a href="kitchen.php" style="color:#cbd5e1;">Kitchen KDS</a></li>
        <li><a href="orders.php" style="color:#cbd5e1;">Orders</a></li>
        <li><a href="bookings.php" style="color:#cbd5e1;">Bookings</a></li>
        <li><a href="menu_manage.php" style="color:#cbd5e1;">Manage Menu</a></li>
        <li><a href="practicals.php" class="active" style="color:#facc15;font-weight:800;">🎓 11 Practicals Lab</a></li>
        <li><a href="db_viewer.php" style="color:#60a5fa;">DB Inspector</a></li>
      </ul>
    </div>
  </header>

  <main class="main-wrapper">
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 12px;">
      <div>
        <span class="badge-prac">College DBMS Syllabus</span>
        <h1 style="font-size: 2.2rem; font-weight: 800; color: #0f172a; margin-top: 6px;">DBMS Lab Practicals (1 to 11)</h1>
        <p style="color: #64748b;">Live practical implementation and query execution mapped directly to your 11 practical syllabus list.</p>
      </div>
      <a href="../mysql_11_practicals.sql" download class="btn btn-primary" style="background:#16a34a;">
        📥 Download MySQL 11 Practicals Script
      </a>
    </div>

    <!-- Practical Navigation Tabs -->
    <div class="prac-nav">
      <a href="practicals.php?p=1" class="prac-btn <?= ($activeP == 1) ? 'active' : '' ?>">P1: Case Study</a>
      <a href="practicals.php?p=2" class="prac-btn <?= ($activeP == 2) ? 'active' : '' ?>">P2: DDL Commands</a>
      <a href="practicals.php?p=3" class="prac-btn <?= ($activeP == 3) ? 'active' : '' ?>">P3: DML Operations</a>
      <a href="practicals.php?p=4" class="prac-btn <?= ($activeP == 4) ? 'active' : '' ?>">P4: Constraints</a>
      <a href="practicals.php?p=5" class="prac-btn <?= ($activeP == 5) ? 'active' : '' ?>">P5: Joins (Inner/Left/Right/Full)</a>
      <a href="practicals.php?p=6" class="prac-btn <?= ($activeP == 6) ? 'active' : '' ?>">P6: Nested Subqueries</a>
      <a href="practicals.php?p=7" class="prac-btn <?= ($activeP == 7) ? 'active' : '' ?>">P7: Database Views</a>
      <a href="practicals.php?p=8" class="prac-btn <?= ($activeP == 8) ? 'active' : '' ?>">P8: TCL (Commit/Rollback/Savepoint)</a>
      <a href="practicals.php?p=9" class="prac-btn <?= ($activeP == 9) ? 'active' : '' ?>">P9: DCL (Grant/Revoke)</a>
      <a href="practicals.php?p=10" class="prac-btn <?= ($activeP == 10) ? 'active' : '' ?>">P10: Triggers (DML & Age Alert)</a>
      <a href="practicals.php?p=11" class="prac-btn <?= ($activeP == 11) ? 'active' : '' ?>">P11: Mini Project</a>
    </div>

    <!-- ==================== PRACTICAL 1 ==================== -->
    <?php if ($activeP == 1): ?>
      <div class="section-card">
        <h2>Practical 1: Introduction to Database & Case Study</h2>
        <p style="color:#64748b;margin:8px 0 16px;"><strong>Aim:</strong> Brief introduction to database systems and Case Study of a real-world database system (QR Cafe Management System).</p>
        
        <h3 style="color:#1e3a8a;margin-top:16px;">Case Study: QR Cafe Smart Dining Database</h3>
        <p>The QR Cafe website allows diners to scan unique QR codes placed on physical tables, browse items, customize orders, and make table reservations. The database maintains relational entities to guarantee zero redundancy and high operational throughput.</p>
        
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;margin-top:16px;">
          <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:16px;border-radius:8px;">
            <h4 style="color:#0f172a;margin-bottom:6px;">1. CAFE_TABLES</h4>
            <p style="font-size:13px;color:#64748b;">Stores table codes (T01–T06), seating capacity, and availability status (AVAILABLE, RESERVED, DISABLED).</p>
          </div>
          <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:16px;border-radius:8px;">
            <h4 style="color:#0f172a;margin-bottom:6px;">2. CUSTOMERS</h4>
            <p style="font-size:13px;color:#64748b;">Customer records with unique phone numbers, name, email, and age (target of Practical 10 age validation trigger).</p>
          </div>
          <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:16px;border-radius:8px;">
            <h4 style="color:#0f172a;margin-bottom:6px;">3. BOOKINGS</h4>
            <p style="font-size:13px;color:#64748b;">Advance table reservations linking customers to tables with conflict-free date and time intervals (1:N).</p>
          </div>
          <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:16px;border-radius:8px;">
            <h4 style="color:#0f172a;margin-bottom:6px;">4. MENU_ITEMS</h4>
            <p style="font-size:13px;color:#64748b;">Catalog of food and handcrafted beverages with prices, categories, and stock availability.</p>
          </div>
          <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:16px;border-radius:8px;">
            <h4 style="color:#0f172a;margin-bottom:6px;">5. CAFE_ORDERS</h4>
            <p style="font-size:13px;color:#64748b;">Orders generated by diners seated at physical tables with total bill, status, and culinary notes.</p>
          </div>
          <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:16px;border-radius:8px;">
            <h4 style="color:#0f172a;margin-bottom:6px;">6. ORDER_ITEMS</h4>
            <p style="font-size:13px;color:#64748b;">Line items decomposing the M:N relationship between orders and menu items into 3NF.</p>
          </div>
          <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:16px;border-radius:8px;">
            <h4 style="color:#0f172a;margin-bottom:6px;">7. AUDIT_LOG</h4>
            <p style="font-size:13px;color:#64748b;">Security log table populated automatically by database triggers on DML operations.</p>
          </div>
        </div>
      </div>

    <!-- ==================== PRACTICAL 2 ==================== -->
    <?php elseif ($activeP == 2): ?>
      <div class="section-card">
        <h2>Practical 2: Data Definition Language (DDL)</h2>
        <p style="color:#64748b;margin:8px 0 16px;"><strong>Aim:</strong> To implement Data Definition language: (a) Create, (b) Alter, (c) Drop, (d) Truncate.</p>

        <h3 style="color:#1e3a8a;">(a) CREATE TABLE Statements</h3>
        <div class="sql-box">
CREATE TABLE cafe_tables (
    table_id INTEGER PRIMARY KEY AUTOINCREMENT,
    table_number VARCHAR(10) NOT NULL UNIQUE,
    capacity INT DEFAULT 2 NOT NULL CHECK(capacity > 0),
    table_status VARCHAR(20) DEFAULT 'AVAILABLE' NOT NULL
);

CREATE TABLE customers (
    customer_id INTEGER PRIMARY KEY AUTOINCREMENT,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL UNIQUE,
    age INT DEFAULT 18 CHECK(age >= 12),
    email VARCHAR(100)
);
        </div>

        <h3 style="color:#1e3a8a;">(b) ALTER TABLE Statements</h3>
        <div class="sql-box">
-- Add a new column to existing table
ALTER TABLE cafe_tables ADD COLUMN floor_number INTEGER DEFAULT 1;

-- Add check constraint / column modification
ALTER TABLE customers ADD COLUMN address VARCHAR(200);
        </div>

        <h3 style="color:#1e3a8a;">(c) DROP TABLE & (d) TRUNCATE Statements</h3>
        <div class="sql-box">
-- Create a temporary demonstration table
CREATE TABLE temp_promotions (
    promo_id INT PRIMARY KEY,
    promo_code VARCHAR(20)
);

-- TRUNCATE TABLE: Deletes all records rapidly without removing the table structure
DELETE FROM temp_promotions; -- In SQLite / TRUNCATE TABLE temp_promotions in MySQL

-- DROP TABLE: Deletes the table structure and data permanently
DROP TABLE IF EXISTS temp_promotions;
        </div>
      </div>

    <!-- ==================== PRACTICAL 3 ==================== -->
    <?php elseif ($activeP == 3): ?>
      <div class="section-card">
        <h2>Practical 3: Data Manipulation Language (DML)</h2>
        <p style="color:#64748b;margin:8px 0 16px;"><strong>Aim:</strong> To implement DML: (a) Insert, (b) Select, (c) Update, (d) Delete, (e) Like '%', (f) Relational Operator.</p>

        <h3 style="color:#1e3a8a;">(a) INSERT & (b) SELECT</h3>
        <div class="sql-box">
INSERT INTO menu_items (item_name, price, category, availability) 
VALUES ('Hazelnut Frappe', 180.00, 'Beverages', 'Y');

SELECT * FROM menu_items WHERE availability = 'Y';
        </div>

        <h3 style="color:#1e3a8a;">(c) UPDATE & (d) DELETE</h3>
        <div class="sql-box">
UPDATE customers SET email = 'rahul.new@gmail.com' WHERE customer_name = 'Rahul Patel';

DELETE FROM menu_items WHERE item_name = 'Hazelnut Frappe';
        </div>

        <h3 style="color:#1e3a8a;">(e) SELECT with LIKE '%' Wildcard & (f) Relational Operators</h3>
        <div class="sql-box">
-- LIKE '%' Wildcard Search: All menu items with 'Coffee' anywhere in name
SELECT item_name, price, category FROM menu_items WHERE item_name LIKE '%Coffee%';

-- Relational Operators: Menu items with price >= 140.00 and price <= 250.00
SELECT item_name, price, category FROM menu_items WHERE price >= 140.00 AND price <= 250.00;
        </div>

        <h3 style="margin-top:20px;color:#0f172a;">Live Query Output: Menu items where price >= 140.00</h3>
        <?php
          $dmlRows = $db->query("SELECT item_name, price, category FROM menu_items WHERE price >= 140.00 ORDER BY price DESC")->fetchAll();
        ?>
        <div class="table-responsive">
          <table class="custom-table">
            <thead><tr><th>Item Name</th><th>Category</th><th>Price (Relational: price >= 140)</th></tr></thead>
            <tbody>
              <?php foreach ($dmlRows as $r): ?>
                <tr>
                  <td style="font-weight:700;"><?= htmlspecialchars($r['item_name']) ?></td>
                  <td><span class="badge-prac"><?= htmlspecialchars($r['category']) ?></span></td>
                  <td style="font-weight:800;color:#16a34a;">₹<?= number_format($r['price'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

    <!-- ==================== PRACTICAL 4 ==================== -->
    <?php elseif ($activeP == 4): ?>
      <div class="section-card">
        <h2>Practical 4: Integrity Constraints</h2>
        <p style="color:#64748b;margin:8px 0 16px;"><strong>Aim:</strong> To implement Constraints: (a) Null, (b) Not Null, (c) Unique, (d) Check, (e) Default, (f) Primary Key, (g) Foreign Key, (h) Enable Constraints, (i) Disable Constraints, (j) Drop Constraints.</p>

        <div class="table-responsive" style="margin-bottom:20px;">
          <table class="custom-table">
            <thead>
              <tr><th>Constraint Type</th><th>Column / Table in QR Cafe</th><th>Definition / Rule</th></tr>
            </thead>
            <tbody>
              <tr><td><strong>PRIMARY KEY</strong></td><td>`table_id`, `customer_id`, `booking_id`, `item_id`, `order_id`</td><td>Uniquely identifies each entity record. Cannot be NULL.</td></tr>
              <tr><td><strong>FOREIGN KEY</strong></td><td>`table_id` in BOOKINGS, `booking_id` in CAFE_ORDERS</td><td>Enforces referential integrity with ON DELETE CASCADE.</td></tr>
              <tr><td><strong>UNIQUE</strong></td><td>`phone` in CUSTOMERS, `table_number` in CAFE_TABLES</td><td>Prevents duplicate phone numbers or duplicate table numbers.</td></tr>
              <tr><td><strong>NOT NULL</strong></td><td>`customer_name`, `booking_date`, `total_amount`</td><td>Field must always have a value; cannot be left blank.</td></tr>
              <tr><td><strong>CHECK</strong></td><td>`capacity > 0`, `price >= 0`, `age >= 18`</td><td>Validates data domain (e.g. customer must be >= 18).</td></tr>
              <tr><td><strong>DEFAULT</strong></td><td>`capacity DEFAULT 2`, `table_status DEFAULT 'AVAILABLE'`</td><td>Assigns standard default value if omitted during INSERT.</td></tr>
            </tbody>
          </table>
        </div>

        <h3 style="color:#1e3a8a;">Enable, Disable, Drop Constraints (DDL Syntax)</h3>
        <div class="sql-box">
-- MySQL / Oracle Syntax:
ALTER TABLE cafe_tables DISABLE CONSTRAINT chk_capacity;
ALTER TABLE cafe_tables ENABLE CONSTRAINT chk_capacity;
ALTER TABLE customers DROP CONSTRAINT chk_phone_len;
        </div>
      </div>

    <!-- ==================== PRACTICAL 5 ==================== -->
    <?php elseif ($activeP == 5): ?>
      <div class="section-card">
        <h2>Practical 5: Relational Joins</h2>
        <p style="color:#64748b;margin:8px 0 16px;"><strong>Aim:</strong> To implement Join: (a) Inner Join, (b) Left Join, (c) Right Join, (d) Full Join.</p>

        <h3 style="color:#1e3a8a;">(a) INNER JOIN: Bookings with Customer Names & Table Numbers</h3>
        <div class="sql-box">
SELECT b.booking_id, c.customer_name, c.phone, t.table_number, b.booking_date, b.booking_status
FROM bookings b
INNER JOIN customers c ON b.customer_id = c.customer_id
INNER JOIN cafe_tables t ON b.table_id = t.table_id;
        </div>
        <?php
          $innerJoin = $db->query("
            SELECT b.booking_id, c.customer_name, c.phone, t.table_number, b.booking_date, b.booking_status
            FROM bookings b
            INNER JOIN customers c ON b.customer_id = c.customer_id
            INNER JOIN cafe_tables t ON b.table_id = t.table_id
          ")->fetchAll();
        ?>
        <div class="table-responsive" style="margin-bottom:24px;">
          <table class="custom-table">
            <thead><tr><th>Booking #</th><th>Customer Name</th><th>Phone</th><th>Table #</th><th>Date</th><th>Status</th></tr></thead>
            <tbody>
              <?php foreach ($innerJoin as $r): ?>
                <tr>
                  <td>#<?= $r['booking_id'] ?></td>
                  <td style="font-weight:700;"><?= htmlspecialchars($r['customer_name']) ?></td>
                  <td><?= htmlspecialchars($r['phone']) ?></td>
                  <td><span class="table-badge">Table <?= htmlspecialchars($r['table_number']) ?></span></td>
                  <td><?= htmlspecialchars($r['booking_date']) ?></td>
                  <td><span class="status-badge status-confirmed"><?= htmlspecialchars($r['booking_status']) ?></span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <h3 style="color:#1e3a8a;">(b) LEFT JOIN: All Cafe Tables & Their Bookings (Including Unbooked Tables)</h3>
        <div class="sql-box">
SELECT t.table_number, t.capacity, b.booking_id, b.booking_date, b.booking_status
FROM cafe_tables t
LEFT JOIN bookings b ON t.table_id = b.table_id
ORDER BY t.table_number;
        </div>
        <?php
          $leftJoin = $db->query("
            SELECT t.table_number, t.capacity, b.booking_id, b.booking_date, b.booking_status
            FROM cafe_tables t
            LEFT JOIN bookings b ON t.table_id = b.table_id
            ORDER BY t.table_number
          ")->fetchAll();
        ?>
        <div class="table-responsive">
          <table class="custom-table">
            <thead><tr><th>Table Number</th><th>Capacity</th><th>Booking ID (NULL if never booked)</th><th>Date</th><th>Status</th></tr></thead>
            <tbody>
              <?php foreach ($leftJoin as $r): ?>
                <tr>
                  <td style="font-weight:700;">Table <?= htmlspecialchars($r['table_number']) ?></td>
                  <td><?= $r['capacity'] ?> Seats</td>
                  <td><?= $r['booking_id'] ? '#' . $r['booking_id'] : '<span style="color:#94a3b8;font-style:italic;">NULL (No Booking)</span>' ?></td>
                  <td><?= $r['booking_date'] ?? '—' ?></td>
                  <td><?= $r['booking_status'] ?? '—' ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

    <!-- ==================== PRACTICAL 6 ==================== -->
    <?php elseif ($activeP == 6): ?>
      <div class="section-card">
        <h2>Practical 6: Nested Subqueries</h2>
        <p style="color:#64748b;margin:8px 0 16px;"><strong>Aim:</strong> To implement Nested Subqueries (IN, NOT IN, Aggregate Subquery, Correlated EXISTS).</p>

        <h3 style="color:#1e3a8a;">1. Aggregate Subquery: Menu Items Priced Above the Average Price</h3>
        <div class="sql-box">
SELECT item_name, price, category
FROM menu_items
WHERE price > (SELECT AVG(price) FROM menu_items);
        </div>
        <?php
          $avgPrice = $db->query("SELECT AVG(price) FROM menu_items")->fetchColumn();
          $sub1 = $db->query("SELECT item_name, price, category FROM menu_items WHERE price > (SELECT AVG(price) FROM menu_items)")->fetchAll();
        ?>
        <p style="font-size:13px;color:#64748b;margin-bottom:10px;">Average Menu Item Price = <strong>₹<?= number_format($avgPrice, 2) ?></strong></p>
        <div class="table-responsive" style="margin-bottom:24px;">
          <table class="custom-table">
            <thead><tr><th>Item Name</th><th>Category</th><th>Price (> Average)</th></tr></thead>
            <tbody>
              <?php foreach ($sub1 as $r): ?>
                <tr>
                  <td style="font-weight:700;"><?= htmlspecialchars($r['item_name']) ?></td>
                  <td><span class="badge-prac"><?= htmlspecialchars($r['category']) ?></span></td>
                  <td style="font-weight:800;color:#16a34a;">₹<?= number_format($r['price'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <h3 style="color:#1e3a8a;">2. Subquery with IN: Customers Who Have Placed At Least One Booking</h3>
        <div class="sql-box">
SELECT customer_name, phone FROM customers 
WHERE customer_id IN (SELECT DISTINCT customer_id FROM bookings);
        </div>

        <h3 style="color:#1e3a8a;">3. Subquery with NOT IN: Tables That Have Never Been Reserved</h3>
        <div class="sql-box">
SELECT table_number, capacity FROM cafe_tables 
WHERE table_id NOT IN (SELECT DISTINCT table_id FROM bookings);
        </div>
      </div>

    <!-- ==================== PRACTICAL 7 ==================== -->
    <?php elseif ($activeP == 7): ?>
      <div class="section-card">
        <h2>Practical 7: Database Views</h2>
        <p style="color:#64748b;margin:8px 0 16px;"><strong>Aim:</strong> To implement Views: (a) View, (b) Join View, (c) Force View, (d) View with Check Option.</p>

        <h3 style="color:#1e3a8a;">(a) Simple View: `v_available_tables`</h3>
        <div class="sql-box">
CREATE VIEW v_available_tables AS
SELECT table_id, table_number, capacity
FROM cafe_tables
WHERE table_status = 'AVAILABLE';
        </div>

        <h3 style="color:#1e3a8a;">(b) Multi-Table Join View: `v_admin_bookings_report`</h3>
        <div class="sql-box">
CREATE VIEW v_admin_bookings_report AS
SELECT b.booking_id, c.customer_name, c.phone, t.table_number, t.capacity,
       b.booking_date, b.start_time, b.end_time, b.booking_status
FROM bookings b
JOIN customers c ON b.customer_id = c.customer_id
JOIN cafe_tables t ON b.table_id = t.table_id;
        </div>

        <h3 style="margin-top:20px;color:#0f172a;">Live Query Output from View: `SELECT * FROM v_admin_bookings_report`</h3>
        <?php
          $viewRows = $db->query("SELECT * FROM v_admin_bookings_report LIMIT 5")->fetchAll();
        ?>
        <div class="table-responsive">
          <table class="custom-table">
            <thead><tr><th>Booking #</th><th>Customer Name</th><th>Phone</th><th>Table</th><th>Date</th><th>Slot</th><th>Status</th></tr></thead>
            <tbody>
              <?php foreach ($viewRows as $r): ?>
                <tr>
                  <td>#<?= $r['booking_id'] ?></td>
                  <td style="font-weight:700;"><?= htmlspecialchars($r['customer_name']) ?></td>
                  <td><?= htmlspecialchars($r['phone']) ?></td>
                  <td><span class="table-badge">Table <?= htmlspecialchars($r['table_number']) ?></span></td>
                  <td><?= htmlspecialchars($r['booking_date']) ?></td>
                  <td><?= htmlspecialchars($r['start_time']) ?> – <?= htmlspecialchars($r['end_time']) ?></td>
                  <td><span class="status-badge status-confirmed"><?= htmlspecialchars($r['booking_status']) ?></span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <h3 style="color:#1e3a8a;margin-top:20px;">(d) View with CHECK OPTION</h3>
        <div class="sql-box">
-- MySQL / Oracle Syntax: Enforces that updates through the view must satisfy WHERE condition
CREATE VIEW v_active_beverages AS
SELECT item_id, item_name, price, category, availability
FROM menu_items
WHERE category = 'Beverages' AND availability = 'Y'
WITH CHECK OPTION;
        </div>
      </div>

    <!-- ==================== PRACTICAL 8 ==================== -->
    <?php elseif ($activeP == 8): ?>
      <div class="section-card">
        <h2>Practical 8: Transaction Control Language (TCL)</h2>
        <p style="color:#64748b;margin:8px 0 16px;"><strong>Aim:</strong> To implement TCL: (a) Commit, (b) Rollback, (c) Save Point.</p>

        <p>In our QR Cafe website, when an order is placed (`checkout.php`) or table reserved (`book.php`), multiple database inserts occur atomically inside a transaction. If any query fails, `ROLLBACK` executes so corrupt partial orders are never created. If all queries succeed, `COMMIT` executes.</p>

        <h3 style="color:#1e3a8a;">TCL Query Flow Example</h3>
        <div class="sql-box">
-- Step 1: Start Transaction
START TRANSACTION; -- In MySQL / BEGIN TRANSACTION in SQLite

-- Step 2: Insert parent order
INSERT INTO cafe_orders (table_number, total_amount, order_status)
VALUES ('T01', 270.00, 'PLACED');

-- Step 3: Create Savepoint
SAVEPOINT sp_before_items;

-- Step 4: Insert line items
INSERT INTO order_items (order_id, item_id, quantity, unit_price)
VALUES (1, 1, 1, 120.00);

-- If an error happens during the second item insert:
-- ROLLBACK TO sp_before_items; (Reverts only to the savepoint)

-- Step 5: Finalize transaction
COMMIT; -- Changes are permanently saved
        </div>
      </div>

    <!-- ==================== PRACTICAL 9 ==================== -->
    <?php elseif ($activeP == 9): ?>
      <div class="section-card">
        <h2>Practical 9: Data Control Language (DCL)</h2>
        <p style="color:#64748b;margin:8px 0 16px;"><strong>Aim:</strong> To implement DCL: (a) Grant, (b) Revoke.</p>

        <p>Data Control Language commands manage user access control, privileges, and database security.</p>

        <h3 style="color:#1e3a8a;">(a) GRANT Syntax & Examples in CafeDB</h3>
        <div class="sql-box">
-- Create cafe staff user
CREATE USER 'cafe_waiter'@'localhost' IDENTIFIED BY 'WaiterPass@123';

-- GRANT read & write privileges on orders and menu tables to the staff
GRANT SELECT, INSERT, UPDATE ON cafe_db.cafe_orders TO 'cafe_waiter'@'localhost';
GRANT SELECT ON cafe_db.menu_items TO 'cafe_waiter'@'localhost';

-- Apply privileges
FLUSH PRIVILEGES;
        </div>

        <h3 style="color:#1e3a8a;">(b) REVOKE Syntax & Examples</h3>
        <div class="sql-box">
-- Revoke UPDATE permission from waiter (waiter can only INSERT new orders, not edit)
REVOKE UPDATE ON cafe_db.cafe_orders FROM 'cafe_waiter'@'localhost';

FLUSH PRIVILEGES;
        </div>
      </div>

    <!-- ==================== PRACTICAL 10 ==================== -->
    <?php elseif ($activeP == 10): ?>
      <div class="section-card">
        <h2>Practical 10: Database Triggers</h2>
        <p style="color:#64748b;margin:8px 0 16px;"><strong>Aim:</strong> (a) To write a Trigger to pop-up / log DML operations, (b) To write a Trigger to check the age valid or not Using Message Alert.</p>

        <!-- 10(b) Trigger Live Test Form -->
        <div style="background:#fefce8;border:2px solid #fef08a;padding:20px;border-radius:10px;margin-bottom:24px;">
          <h3 style="color:#854d0e;margin-top:0;">⚡ Live Test: Trigger to check Age valid or not Using Message Alert</h3>
          <p style="font-size:13px;color:#713f12;margin-bottom:14px;">The database trigger enforces that <code>age >= 18</code>. If age is less than 18, the database trigger immediately fires and aborts with a Message Alert!</p>
          
          <?php if ($triggerTestResult): ?>
            <div style="background:#dcfce7;color:#15803d;padding:12px;border-radius:6px;font-weight:700;margin-bottom:14px;">
              ✅ <?= htmlspecialchars($triggerTestResult) ?>
            </div>
          <?php endif; ?>

          <?php if ($triggerTestError): ?>
            <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:6px;font-weight:700;margin-bottom:14px;">
              🚨 TRIGGER ALERT POP-UP: <?= htmlspecialchars($triggerTestError) ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="practicals.php?p=10" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            <input type="hidden" name="action" value="test_trigger">
            <div>
              <label style="display:block;font-size:12px;font-weight:700;margin-bottom:4px;">Customer Name</label>
              <input type="text" name="test_name" value="Rahul Student" class="form-control" style="width:160px;padding:6px 10px;">
            </div>
            <div>
              <label style="display:block;font-size:12px;font-weight:700;margin-bottom:4px;">Enter Age (Try < 18 vs >= 18)</label>
              <input type="number" name="test_age" value="15" class="form-control" style="width:120px;padding:6px 10px;">
            </div>
            <button type="submit" class="btn btn-primary" style="padding:7px 16px;font-size:13px;">
              Test Trigger Execution ➔
            </button>
          </form>
        </div>

        <h3 style="color:#1e3a8a;">Trigger 10(b) Code: Age Validation Trigger with Message Alert</h3>
        <div class="sql-box">
-- SQLite Trigger:
CREATE TRIGGER trg_check_customer_age
BEFORE INSERT ON customers
FOR EACH ROW
WHEN NEW.age < 18
BEGIN
    SELECT RAISE(ABORT, 'AGE ALERT: Customer must be at least 18 years old to make a cafe reservation.');
END;

-- MySQL Trigger:
DELIMITER //
CREATE TRIGGER trg_check_customer_age_mysql
BEFORE INSERT ON customers
FOR EACH ROW
BEGIN
    IF NEW.age < 18 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'AGE ALERT: Customer must be at least 18 years old to make a cafe reservation.';
    END IF;
END //
DELIMITER ;
        </div>

        <h3 style="color:#1e3a8a;margin-top:24px;">Trigger 10(a): DML Audit Trigger (Logs DML operations to `audit_log`)</h3>
        <div class="sql-box">
CREATE TRIGGER trg_audit_orders_insert
AFTER INSERT ON cafe_orders
BEGIN
    INSERT INTO audit_log (table_name, operation, details)
    VALUES ('CAFE_ORDERS', 'INSERT', 'New order #' || NEW.order_id || ' placed for Table ' || NEW.table_number);
END;
        </div>

        <h3 style="margin-top:16px;color:#0f172a;">Live Audit Entries Generated by Trigger 10(a) in `AUDIT_LOG`:</h3>
        <?php
          $auditRows = $db->query("SELECT * FROM audit_log ORDER BY log_id DESC LIMIT 5")->fetchAll();
        ?>
        <div class="table-responsive">
          <table class="custom-table">
            <thead><tr><th>Log #</th><th>Table Name</th><th>Operation</th><th>Timestamp</th><th>Details Logged by Trigger</th></tr></thead>
            <tbody>
              <?php if (empty($auditRows)): ?>
                <tr><td colspan="5" style="text-align:center;padding:16px;color:#94a3b8;">No audit records yet. Place an order to see the trigger log it live!</td></tr>
              <?php else: ?>
                <?php foreach ($auditRows as $a): ?>
                  <tr>
                    <td>#<?= $a['log_id'] ?></td>
                    <td><strong><?= htmlspecialchars($a['table_name']) ?></strong></td>
                    <td><span class="badge-prac"><?= htmlspecialchars($a['operation']) ?></span></td>
                    <td style="font-size:12px;color:#64748b;"><?= htmlspecialchars($a['change_date']) ?></td>
                    <td><?= htmlspecialchars($a['details']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    <!-- ==================== PRACTICAL 11 ==================== -->
    <?php elseif ($activeP == 11): ?>
      <div class="section-card">
        <h2>Practical 11: Mini Project Database & ERP Model Implementation</h2>
        <p style="color:#64748b;margin:8px 0 16px;"><strong>Aim:</strong> To design the Database and ERP model and implement it by using MySQL queries (Mini Project).</p>

        <div style="background:#f1f5f9;border-left:4px solid #1e3a8a;padding:16px;border-radius:4px;margin-bottom:20px;">
          <h3 style="color:#1e3a8a;margin-top:0;">Project: QR Cafe Management & Smart Dining System</h3>
          <p style="font-size:13.5px;color:#334155;line-height:1.6;">
            A complete end-to-end cafe ordering and reservation system where diners scan table QR codes to access an interactive digital menu, place orders directly to a live Kitchen Display Screen (KDS), and book cafe tables in advance.
          </p>
        </div>

        <h3 style="color:#1e3a8a;">ERP / Entity Relationship Model Summary:</h3>
        <ul style="margin:12px 0 20px 24px;line-height:1.8;color:#334155;">
          <li><strong>CAFE_TABLES:</strong> Master entity managing restaurant tables, capacity, and status.</li>
          <li><strong>CUSTOMERS:</strong> Stores user profiles, unique phone number constraint, and age check.</li>
          <li><strong>BOOKINGS:</strong> Links Customer to Table with date and time slot validation (1:N).</li>
          <li><strong>MENU_ITEMS:</strong> Catalog of food items, prices, categories, and stock status.</li>
          <li><strong>CAFE_ORDERS:</strong> Transactional orders placed by customers at physical tables (1:N).</li>
          <li><strong>ORDER_ITEMS:</strong> Decomposed line items resolving M:N order-to-menu relationship into 3NF.</li>
          <li><strong>AUDIT_LOG:</strong> Auditing entity tracking all DML inserts and updates.</li>
        </ul>

        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:20px;">
          <a href="../mysql_11_practicals.sql" download class="btn btn-primary" style="background:#16a34a;">
            📥 Download Complete MySQL 11 Practicals Script (.sql)
          </a>
          <a href="db_viewer.php" class="btn btn-dark">
            🗄️ Open Live Database Inspector
          </a>
        </div>
      </div>
    <?php endif; ?>

  </main>
</body>
</html>
