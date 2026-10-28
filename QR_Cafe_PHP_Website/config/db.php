<?php
// ============================================================================
// QR CAFE WEBSITE - DATABASE CONFIGURATION & INITIALIZATION (PDO)
// 4-Table Simplified Relational Database for College DBMS 11 Practicals
// ============================================================================

define('DB_TYPE', 'sqlite'); // Options: 'sqlite' or 'mysql'

// SQLite Configuration
define('SQLITE_FILE', __DIR__ . '/../cafe.db');

// MySQL Configuration (For XAMPP / phpMyAdmin if needed)
define('MYSQL_HOST', 'localhost');
define('MYSQL_PORT', '3306');
define('MYSQL_DB', 'cafe_db');
define('MYSQL_USER', 'root');
define('MYSQL_PASS', '');

function getDB() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    try {
        if (DB_TYPE === 'sqlite') {
            $pdo = new PDO('sqlite:' . SQLITE_FILE);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->exec('PRAGMA foreign_keys = ON;');
        } else {
            $dsn = "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DB . ";charset=utf8mb4";
            $pdo = new PDO($dsn, MYSQL_USER, MYSQL_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        }
    } catch (PDOException $e) {
        die("<div style='font-family:sans-serif;padding:30px;background:#fee2e2;color:#991b1b;border-radius:8px;'>
             <h3>Database Connection Failed</h3><p>{$e->getMessage()}</p></div>");
    }

    return $pdo;
}

// Automatically create 4 core tables, views, triggers, and sample data
function initDatabase() {
    $db = getDB();

    // Clean up legacy tables and triggers
    $db->exec("
        DROP TABLE IF EXISTS audit_log;
        DROP TRIGGER IF EXISTS trg_audit_orders_insert;
        DROP TRIGGER IF EXISTS trg_audit_orders_update;
        DROP TABLE IF EXISTS bookings;
        DROP TABLE IF EXISTS cafe_orders;
        DROP TABLE IF EXISTS cafe_tables;
        DROP VIEW IF EXISTS v_available_tables;
        DROP VIEW IF EXISTS v_admin_bookings_report;
    ");

    // ------------------------------------------------------------------------
    // TABLE 1: CUSTOMERS (Practical 2 DDL & Practical 4 Constraints)
    // ------------------------------------------------------------------------
    $db->exec("
        CREATE TABLE IF NOT EXISTS customers (
            customer_id INTEGER PRIMARY KEY " . (DB_TYPE === 'sqlite' ? "AUTOINCREMENT" : "AUTO_INCREMENT") . ",
            customer_name VARCHAR(100) NOT NULL,
            phone VARCHAR(15) NOT NULL UNIQUE,
            age INT NOT NULL,
            CHECK (age >= 0)
        )
    ");

    // ------------------------------------------------------------------------
    // TABLE 2: MENU_ITEMS (Practical 2 DDL & Practical 4 Constraints)
    // ------------------------------------------------------------------------
    $db->exec("
        CREATE TABLE IF NOT EXISTS menu_items (
            item_id INTEGER PRIMARY KEY " . (DB_TYPE === 'sqlite' ? "AUTOINCREMENT" : "AUTO_INCREMENT") . ",
            item_name VARCHAR(100) NOT NULL UNIQUE,
            category VARCHAR(50) NOT NULL,
            price DECIMAL(8,2) NOT NULL,
            is_available CHAR(1) DEFAULT 'Y' NOT NULL,
            CHECK (price > 0),
            CHECK (is_available IN ('Y', 'N'))
        )
    ");

    // ------------------------------------------------------------------------
    // TABLE 3: ORDERS (Practical 2 DDL & Practical 4 Constraints)
    // ------------------------------------------------------------------------
    $db->exec("
        CREATE TABLE IF NOT EXISTS orders (
            order_id INTEGER PRIMARY KEY " . (DB_TYPE === 'sqlite' ? "AUTOINCREMENT" : "AUTO_INCREMENT") . ",
            customer_id INT NOT NULL,
            table_number VARCHAR(10) NOT NULL,
            total_amount DECIMAL(10,2) DEFAULT 0 NOT NULL,
            order_status VARCHAR(20) DEFAULT 'PLACED' NOT NULL,
            order_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
            CHECK (total_amount >= 0),
            CHECK (order_status IN ('PLACED', 'PREPARING', 'SERVED', 'CANCELLED')),
            FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE
        )
    ");

    // ------------------------------------------------------------------------
    // TABLE 4: ORDER_ITEMS (Practical 2 DDL & Practical 4 Constraints)
    // ------------------------------------------------------------------------
    $db->exec("
        CREATE TABLE IF NOT EXISTS order_items (
            order_item_id INTEGER PRIMARY KEY " . (DB_TYPE === 'sqlite' ? "AUTOINCREMENT" : "AUTO_INCREMENT") . ",
            order_id INT NOT NULL,
            item_id INT NOT NULL,
            quantity INT DEFAULT 1 NOT NULL,
            unit_price DECIMAL(8,2) NOT NULL,
            CHECK (quantity > 0),
            FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
            FOREIGN KEY (item_id) REFERENCES menu_items(item_id)
        )
    ");

    // ========================================================================
    // PRACTICAL 7: VIEWS
    // ========================================================================
    
    // 7(a) Simple View: Active Beverages
    $db->exec("
        CREATE VIEW IF NOT EXISTS v_active_beverages AS
        SELECT item_id, item_name, price, category
        FROM menu_items
        WHERE category = 'Beverages' AND is_available = 'Y'
    ");

    // 7(b) Join View: Comprehensive Order Bill Details (Practical 5 Joins)
    $db->exec("
        CREATE VIEW IF NOT EXISTS v_order_bill_details AS
        SELECT 
            o.order_id,
            o.table_number,
            c.customer_name,
            c.phone,
            c.age,
            m.item_name,
            oi.quantity,
            oi.unit_price,
            (oi.quantity * oi.unit_price) AS line_total,
            o.total_amount,
            o.order_status,
            o.order_date
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN menu_items m ON oi.item_id = m.item_id
    ");

    // ========================================================================
    // PRACTICAL 10: TRIGGERS
    // ========================================================================
    if (DB_TYPE === 'sqlite') {
        // 10(b) Trigger to Check Customer Age (Raises Message Alert if age < 18)
        $db->exec("
            CREATE TRIGGER IF NOT EXISTS trg_check_customer_age
            BEFORE INSERT ON customers
            FOR EACH ROW
            WHEN NEW.age < 18
            BEGIN
                SELECT RAISE(ABORT, 'AGE ALERT: Customer must be at least 18 years old to place an order at QR Cafe!');
            END;
        ");
    }

    // Pre-populate Starter Menu Items (if empty)
    $menuCount = $db->query("SELECT COUNT(*) FROM menu_items")->fetchColumn();
    if ($menuCount == 0) {
        $stmt = $db->prepare("INSERT INTO menu_items (item_name, category, price, is_available) VALUES (?, ?, ?, 'Y')");
        $starterItems = [
            ['Signature Cold Coffee', 'Beverages', 120.00],
            ['Caramel Macchiato', 'Beverages', 150.00],
            ['Masala Chai', 'Beverages', 50.00],
            ['Classic Cappuccino', 'Beverages', 130.00],
            ['Grilled Cheese Sandwich', 'Food', 140.00],
            ['Paneer Tikka Panini', 'Food', 180.00],
            ['Farmhouse Pizza', 'Food', 250.00],
            ['Crispy Peri-Peri Fries', 'Food', 110.00],
            ['Belgian Chocolate Brownie', 'Desserts', 150.00],
            ['New York Cheesecake', 'Desserts', 210.00]
        ];
        foreach ($starterItems as $item) {
            $stmt->execute($item);
        }
    }
}

initDatabase();
