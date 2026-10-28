-- ============================================================================
-- COLLEGE DBMS MINI PROJECT: QR CAFE MANAGEMENT SYSTEM
-- MAPPED STRICTLY TO THE 11 DBMS LAB PRACTICALS SYLLABUS
-- Database Engine: MySQL / MariaDB (Import in phpMyAdmin or MySQL CLI)
-- 4-Table Simplified Relational Architecture
-- ============================================================================

CREATE DATABASE IF NOT EXISTS cafe_db;
USE cafe_db;

-- ============================================================================
-- PRACTICAL 1: CASE STUDY OF DATABASE (QR CAFE MANAGEMENT SYSTEM)
-- ============================================================================
/*
  CASE STUDY: QR Cafe Management System
  -------------------------------------
  The QR Cafe website allows diners to view cafe items from their table,
  place customized orders, and track bills in real time.
  
  4 Core Entities (3NF Normalization):
  1. CUSTOMERS: Stores customer identity, contact number, and age.
  2. MENU_ITEMS: Catalog of handcrafted beverages, snacks, and desserts.
  3. ORDERS: Header table tracking customer table number, bill amount, and status.
  4. ORDER_ITEMS: Line items mapping each order to selected menu items.
*/

-- ============================================================================
-- PRACTICAL 2: DATA DEFINITION LANGUAGE (DDL)
-- Concepts: CREATE, ALTER, DROP, TRUNCATE
-- ============================================================================

-- Clean-up step (Safe Drop)
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS menu_items;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS temp_promotions;

-- 2(a) CREATE TABLE statements with Practical 4 Constraints:
CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL UNIQUE,
    age INT NOT NULL,
    CONSTRAINT chk_customer_age CHECK (age >= 0)
);

CREATE TABLE menu_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL UNIQUE,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(8,2) NOT NULL,
    is_available CHAR(1) DEFAULT 'Y' NOT NULL,
    CONSTRAINT chk_item_price CHECK (price > 0),
    CONSTRAINT chk_item_avail CHECK (is_available IN ('Y', 'N'))
);

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    table_number VARCHAR(10) NOT NULL,
    total_amount DECIMAL(10,2) DEFAULT 0.00 NOT NULL,
    order_status VARCHAR(20) DEFAULT 'PLACED' NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT chk_order_total CHECK (total_amount >= 0),
    CONSTRAINT chk_order_status CHECK (order_status IN ('PLACED', 'PREPARING', 'SERVED', 'CANCELLED')),
    CONSTRAINT fk_orders_customer FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE
);

CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT DEFAULT 1 NOT NULL,
    unit_price DECIMAL(8,2) NOT NULL,
    CONSTRAINT chk_item_qty CHECK (quantity > 0),
    CONSTRAINT fk_oi_order FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    CONSTRAINT fk_oi_item FOREIGN KEY (item_id) REFERENCES menu_items(item_id)
);

-- 2(b) ALTER TABLE Examples:
-- Add email column to customers table
ALTER TABLE customers ADD COLUMN email VARCHAR(100) NULL;

-- Modify column size
ALTER TABLE customers MODIFY COLUMN customer_name VARCHAR(120) NOT NULL;

-- 2(c) DROP TABLE Example (Temporary demonstration table):
CREATE TABLE temp_promotions (
    promo_id INT PRIMARY KEY,
    promo_code VARCHAR(20)
);
DROP TABLE IF EXISTS temp_promotions;

-- 2(d) TRUNCATE TABLE Example (on order_items demonstration):
-- TRUNCATE TABLE order_items;


-- ============================================================================
-- PRACTICAL 3: DATA MANIPULATION LANGUAGE (DML)
-- Concepts: INSERT, SELECT, UPDATE, DELETE, LIKE '%', Relational Operators
-- ============================================================================

-- 3(a) INSERT: Starter Menu Items
INSERT INTO menu_items (item_name, category, price, is_available) VALUES
('Signature Cold Coffee', 'Beverages', 120.00, 'Y'),
('Caramel Macchiato', 'Beverages', 150.00, 'Y'),
('Masala Chai', 'Beverages', 50.00, 'Y'),
('Classic Cappuccino', 'Beverages', 130.00, 'Y'),
('Grilled Cheese Sandwich', 'Food', 140.00, 'Y'),
('Paneer Tikka Panini', 'Food', 180.00, 'Y'),
('Farmhouse Pizza', 'Food', 250.00, 'Y'),
('Crispy Peri-Peri Fries', 'Food', 110.00, 'Y'),
('Belgian Chocolate Brownie', 'Desserts', 150.00, 'Y'),
('New York Cheesecake', 'Desserts', 210.00, 'Y');

-- Insert Customers
INSERT INTO customers (customer_name, phone, age, email) VALUES
('Aarav Mehta', '9876543210', 21, 'aarav@gmail.com'),
('Diya Sharma', '9811223344', 24, 'diya@gmail.com'),
('Rohan Verma', '9822334455', 19, 'rohan@gmail.com');

-- Insert Orders
INSERT INTO orders (customer_id, table_number, total_amount, order_status) VALUES
(1, 'Table 1', 270.00, 'PLACED'),
(2, 'Table 3', 390.00, 'PREPARING'),
(3, 'Table 2', 120.00, 'SERVED');

-- Insert Order Line Items
INSERT INTO order_items (order_id, item_id, quantity, unit_price) VALUES
(1, 1, 1, 120.00), -- 1 Cold Coffee
(1, 9, 1, 150.00), -- 1 Brownie
(2, 7, 1, 250.00), -- 1 Pizza
(2, 5, 1, 140.00), -- 1 Sandwich
(3, 1, 1, 120.00); -- 1 Cold Coffee

-- 3(b) SELECT: Basic selection and projections
SELECT customer_id, customer_name, phone, age FROM customers;
SELECT item_id, item_name, price FROM menu_items WHERE is_available = 'Y';

-- 3(c) UPDATE: Updating order status
UPDATE orders SET order_status = 'SERVED' WHERE order_id = 1;
UPDATE menu_items SET price = 125.00 WHERE item_name = 'Signature Cold Coffee';

-- 3(d) DELETE: Removing an item
INSERT INTO menu_items (item_name, category, price, is_available) VALUES ('Temp Green Tea', 'Beverages', 80.00, 'Y');
DELETE FROM menu_items WHERE item_name = 'Temp Green Tea';

-- 3(e) LIKE Wildcard Pattern Matching '%':
-- Find all beverages or coffees containing 'Coffee'
SELECT * FROM menu_items WHERE item_name LIKE '%Coffee%';

-- Find items starting with 'P'
SELECT * FROM menu_items WHERE item_name LIKE 'P%';

-- 3(f) Relational Operators (=, !=, <, >, <=, >=):
-- Menu items with price >= 150
SELECT item_name, category, price FROM menu_items WHERE price >= 150.00;

-- Orders strictly less than 300
SELECT * FROM orders WHERE total_amount < 300.00;


-- ============================================================================
-- PRACTICAL 4: CONSTRAINTS
-- Concepts: NULL, NOT NULL, UNIQUE, CHECK, DEFAULT, PRIMARY KEY, FOREIGN KEY,
--           ENABLE/DISABLE CONSTRAINTS, DROP CONSTRAINTS
-- ============================================================================
-- Enforced during CREATE TABLE:
-- 4(a) NULL: email in customers (optional).
-- 4(b) NOT NULL: customer_name, phone, item_name, price, table_number.
-- 4(c) UNIQUE: phone in customers, item_name in menu_items.
-- 4(d) CHECK: (price > 0), (age >= 0), (order_status IN (...)).
-- 4(e) DEFAULT: total_amount = 0.00, order_status = 'PLACED', is_available = 'Y'.
-- 4(f) PRIMARY KEY: customer_id, item_id, order_id, order_item_id.
-- 4(g) FOREIGN KEY: orders(customer_id) and order_items(order_id, item_id).

-- 4(h) Disable Constraints in MySQL:
SET FOREIGN_KEY_CHECKS = 0;

-- 4(i) Enable Constraints in MySQL:
SET FOREIGN_KEY_CHECKS = 1;

-- 4(j) Drop Constraint Example:
ALTER TABLE customers ADD CONSTRAINT chk_test CHECK (age < 150);
ALTER TABLE customers DROP CHECK chk_test;


-- ============================================================================
-- PRACTICAL 5: JOINS
-- Concepts: INNER JOIN, LEFT JOIN, RIGHT JOIN, FULL JOIN
-- ============================================================================

-- 5(a) INNER JOIN: Retrieve complete billing details matching all 4 tables
SELECT 
    o.order_id,
    o.table_number,
    c.customer_name,
    c.phone,
    m.item_name,
    oi.quantity,
    oi.unit_price,
    (oi.quantity * oi.unit_price) AS line_total,
    o.order_status
FROM orders o
INNER JOIN customers c ON o.customer_id = c.customer_id
INNER JOIN order_items oi ON o.order_id = oi.order_id
INNER JOIN menu_items m ON oi.item_id = m.item_id;

-- 5(b) LEFT JOIN: List all menu items and how many times they have been ordered
SELECT 
    m.item_name,
    m.price,
    COALESCE(SUM(oi.quantity), 0) AS total_sold
FROM menu_items m
LEFT JOIN order_items oi ON m.item_id = oi.item_id
GROUP BY m.item_id, m.item_name, m.price;

-- 5(c) RIGHT JOIN: Matching items with existing orders
SELECT 
    oi.order_id,
    oi.quantity,
    m.item_name,
    m.price
FROM order_items oi
RIGHT JOIN menu_items m ON oi.item_id = m.item_id;

-- 5(d) FULL OUTER JOIN (Simulated via UNION of LEFT & RIGHT JOIN in MySQL):
SELECT c.customer_name, o.order_id, o.total_amount
FROM customers c
LEFT JOIN orders o ON c.customer_id = o.customer_id
UNION
SELECT c.customer_name, o.order_id, o.total_amount
FROM customers c
RIGHT JOIN orders o ON c.customer_id = o.customer_id;


-- ============================================================================
-- PRACTICAL 6: NESTED SUBQUERIES
-- Concepts: Subqueries with Aggregate, IN, NOT IN
-- ============================================================================

-- 6(a) Single-Row Subquery: Orders with total amount greater than the average order
SELECT order_id, table_number, total_amount, order_status
FROM orders
WHERE total_amount > (SELECT AVG(total_amount) FROM orders);

-- 6(b) Multiple-Row Subquery with NOT IN: Menu items that have never been ordered
SELECT item_id, item_name, category, price
FROM menu_items
WHERE item_id NOT IN (SELECT DISTINCT item_id FROM order_items);

-- 6(c) Multiple-Row Subquery with IN: Customers who ordered Farmhouse Pizza
SELECT customer_name, phone
FROM customers
WHERE customer_id IN (
    SELECT o.customer_id
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN menu_items m ON oi.item_id = m.item_id
    WHERE m.item_name = 'Farmhouse Pizza'
);


-- ============================================================================
-- PRACTICAL 7: VIEWS
-- Concepts: View, Join View, Force View, View with CHECK OPTION
-- ============================================================================

-- 7(a) Simple View: Active Beverages
CREATE OR REPLACE VIEW v_active_beverages AS
SELECT item_id, item_name, price, category
FROM menu_items
WHERE category = 'Beverages' AND is_available = 'Y';

SELECT * FROM v_active_beverages;

-- 7(b) Join View: Comprehensive Order Bill Details
CREATE OR REPLACE VIEW v_order_bill_details AS
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
JOIN menu_items m ON oi.item_id = m.item_id;

SELECT * FROM v_order_bill_details;

-- 7(c) Force View Concept (Standard SQL view definition):
CREATE OR REPLACE VIEW v_order_summary AS
SELECT order_status, COUNT(*) AS count_orders, SUM(total_amount) AS total_revenue
FROM orders
GROUP BY order_status;

-- 7(d) View WITH CHECK OPTION: Enforces criteria on update/insert through view
CREATE OR REPLACE VIEW v_beverages_check AS
SELECT item_id, item_name, price, category, is_available
FROM menu_items
WHERE category = 'Beverages'
WITH CHECK OPTION;


-- ============================================================================
-- PRACTICAL 8: TRANSACTION CONTROL LANGUAGE (TCL)
-- Concepts: COMMIT, ROLLBACK, SAVEPOINT
-- ============================================================================

-- 8(a) COMMIT: Atomic transaction inserting customer and order successfully
START TRANSACTION;
INSERT INTO customers (customer_name, phone, age) VALUES ('Siddharth Rao', '9899001122', 22);
SET @sid = LAST_INSERT_ID();
INSERT INTO orders (customer_id, table_number, total_amount, order_status) VALUES (@sid, 'Table 4', 210.00, 'PLACED');
INSERT INTO order_items (order_id, item_id, quantity, unit_price) VALUES (LAST_INSERT_ID(), 10, 1, 210.00);
COMMIT;

-- 8(b) ROLLBACK: Aborting transaction upon error
START TRANSACTION;
INSERT INTO menu_items (item_name, category, price, is_available) VALUES ('Faulty Item', 'Food', 999.00, 'Y');
-- Intentional rollback to revert changes
ROLLBACK;

-- 8(c) SAVEPOINT: Partial rollback to a designated checkpoint
START TRANSACTION;
INSERT INTO customers (customer_name, phone, age) VALUES ('Test Guest', '9000000001', 25);
SAVEPOINT sp_after_guest;
INSERT INTO orders (customer_id, table_number, total_amount, order_status) VALUES (LAST_INSERT_ID(), 'Table 99', 500.00, 'PLACED');
-- Rollback only the order, keeping the customer
ROLLBACK TO SAVEPOINT sp_after_guest;
COMMIT;


-- ============================================================================
-- PRACTICAL 9: DATA CONTROL LANGUAGE (DCL)
-- Concepts: GRANT, REVOKE
-- ============================================================================

-- Create a cafe waiter user
CREATE USER IF NOT EXISTS 'cafe_waiter'@'localhost' IDENTIFIED BY 'cafe_pass123';

-- 9(a) GRANT: Grant cashier permissions to view and update orders
GRANT SELECT, INSERT, UPDATE ON cafe_db.orders TO 'cafe_waiter'@'localhost';
GRANT SELECT, INSERT ON cafe_db.order_items TO 'cafe_waiter'@'localhost';
GRANT SELECT ON cafe_db.menu_items TO 'cafe_waiter'@'localhost';
FLUSH PRIVILEGES;

-- 9(b) REVOKE: Revoke delete permissions
REVOKE DELETE ON cafe_db.orders FROM 'cafe_waiter'@'localhost';
FLUSH PRIVILEGES;


-- ============================================================================
-- PRACTICAL 10: TRIGGERS
-- Concepts: Trigger to check age valid or not using Message Alert
-- ============================================================================

DELIMITER $$

-- 10(b) Trigger to check Age Valid or Not using Message Alert
DROP TRIGGER IF EXISTS trg_check_customer_age$$
CREATE TRIGGER trg_check_customer_age
BEFORE INSERT ON customers
FOR EACH ROW
BEGIN
    IF NEW.age < 18 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'AGE ALERT: Customer must be at least 18 years old to place an order at QR Cafe!';
    END IF;
END$$

DELIMITER ;


-- ============================================================================
-- PRACTICAL 11: MINI PROJECT
-- Implement QR Cafe Management System with complete web interface
-- ============================================================================
-- End-to-end relational schema and business logic operational in PHP & MySQL/SQLite.
