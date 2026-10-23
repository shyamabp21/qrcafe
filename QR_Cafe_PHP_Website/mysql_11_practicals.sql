-- ============================================================================
-- COLLEGE DBMS MINI PROJECT: QR CAFE MANAGEMENT SYSTEM
-- MAPPED STRICTLY TO THE 11 DBMS LAB PRACTICALS SYLLABUS
-- Database Engine: MySQL / MariaDB (Import in phpMyAdmin or MySQL CLI)
-- ============================================================================

CREATE DATABASE IF NOT EXISTS cafe_db;
USE cafe_db;

-- ============================================================================
-- PRACTICAL 1: CASE STUDY OF DATABASE (QR CAFE MANAGEMENT SYSTEM)
-- ============================================================================
/*
  CASE STUDY: QR Cafe Management System
  -------------------------------------
  The QR Cafe website allows diners to scan unique QR codes placed on physical tables,
  browse the food and drinks catalog, customize orders, and place reservations.
  
  Entities & Schema (3NF Normalization):
  1. CAFE_TABLES: Manages seating areas and table capacities.
  2. CUSTOMERS: Stores customer identity and contact information.
  3. BOOKINGS: Links customers to tables for reservation slots.
  4. MENU_ITEMS: Catalog of food items and handcrafted beverages.
  5. CAFE_ORDERS: Header entity tracking customer dining bills and status.
  6. ORDER_ITEMS: Line items mapping orders to menu items.
  7. AUDIT_LOG: Audit entity tracking DML operations.
*/

-- ============================================================================
-- PRACTICAL 2: DATA DEFINITION LANGUAGE (DDL)
-- Concepts: CREATE, ALTER, DROP, TRUNCATE
-- ============================================================================

-- Clean-up step (Safe Drop)
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS cafe_orders;
DROP TABLE IF EXISTS audit_log;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS menu_items;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS cafe_tables;
DROP TABLE IF EXISTS temp_promotions;

-- 2(a) CREATE TABLE statements with Practical 4 Constraints:
CREATE TABLE cafe_tables (
    table_id INT AUTO_INCREMENT PRIMARY KEY,
    table_number VARCHAR(10) NOT NULL UNIQUE,
    capacity INT DEFAULT 2 NOT NULL,
    table_status VARCHAR(20) DEFAULT 'AVAILABLE' NOT NULL,
    CONSTRAINT chk_capacity CHECK (capacity > 0),
    CONSTRAINT chk_table_status CHECK (table_status IN ('AVAILABLE', 'RESERVED', 'DISABLED'))
);

CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL UNIQUE,
    age INT DEFAULT 18,
    email VARCHAR(100),
    CONSTRAINT chk_customer_age CHECK (age >= 12)
);

CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    table_id INT NOT NULL,
    customer_id INT NOT NULL,
    booking_date VARCHAR(15) NOT NULL,
    start_time VARCHAR(10) NOT NULL,
    end_time VARCHAR(10) NOT NULL,
    booking_status VARCHAR(20) DEFAULT 'CONFIRMED' NOT NULL,
    CONSTRAINT fk_booking_table FOREIGN KEY (table_id) REFERENCES cafe_tables(table_id) ON DELETE CASCADE,
    CONSTRAINT fk_booking_customer FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    CONSTRAINT chk_booking_status CHECK (booking_status IN ('CONFIRMED', 'CANCELLED', 'COMPLETED'))
);

CREATE TABLE menu_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL UNIQUE,
    price DECIMAL(8,2) NOT NULL,
    category VARCHAR(40) NOT NULL,
    description TEXT,
    availability CHAR(1) DEFAULT 'Y' NOT NULL,
    CONSTRAINT chk_item_price CHECK (price >= 0),
    CONSTRAINT chk_item_avail CHECK (availability IN ('Y', 'N'))
);

CREATE TABLE cafe_orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    table_number VARCHAR(10) NOT NULL,
    customer_name VARCHAR(100) DEFAULT 'Guest' NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    total_amount DECIMAL(10,2) DEFAULT 0.00 NOT NULL,
    order_status VARCHAR(20) DEFAULT 'PLACED' NOT NULL,
    notes TEXT,
    CONSTRAINT chk_order_total CHECK (total_amount >= 0),
    CONSTRAINT chk_order_status CHECK (order_status IN ('PLACED', 'PREPARING', 'SERVED', 'CANCELLED'))
);

CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT DEFAULT 1 NOT NULL,
    unit_price DECIMAL(8,2) NOT NULL,
    CONSTRAINT fk_orderitem_order FOREIGN KEY (order_id) REFERENCES cafe_orders(order_id) ON DELETE CASCADE,
    CONSTRAINT fk_orderitem_menu FOREIGN KEY (item_id) REFERENCES menu_items(item_id),
    CONSTRAINT chk_quantity CHECK (quantity > 0),
    CONSTRAINT uq_order_item UNIQUE (order_id, item_id)
);

CREATE TABLE audit_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(50) NOT NULL,
    operation VARCHAR(20) NOT NULL,
    change_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    details TEXT
);

-- 2(b) ALTER TABLE Examples
ALTER TABLE cafe_tables ADD COLUMN floor_number INT DEFAULT 1;
ALTER TABLE customers MODIFY COLUMN email VARCHAR(150);

-- 2(c) TRUNCATE TABLE & 2(d) DROP TABLE
CREATE TABLE temp_promotions (
    promo_id INT PRIMARY KEY,
    promo_code VARCHAR(20)
);

INSERT INTO temp_promotions VALUES (1, 'WELCOME50');
TRUNCATE TABLE temp_promotions;
DROP TABLE temp_promotions;


-- ============================================================================
-- PRACTICAL 4: CONSTRAINTS
-- Concepts: Null, Not Null, Unique, Check, Default, Primary Key, Foreign Key,
--           Enable/Disable/Drop Constraints
-- ============================================================================

-- Adding and dropping named constraints
ALTER TABLE customers ADD CONSTRAINT chk_phone_len CHECK (LENGTH(phone) >= 10);
ALTER TABLE customers DROP CHECK chk_phone_len;


-- ============================================================================
-- PRACTICAL 3: DATA MANIPULATION LANGUAGE (DML)
-- Concepts: Insert, Select, Update, Delete, Like '%', Relational Operators
-- ============================================================================

-- 3(a) INSERT Statements
INSERT INTO cafe_tables (table_number, capacity, table_status) VALUES 
('T01', 2, 'AVAILABLE'),
('T02', 4, 'AVAILABLE'),
('T03', 4, 'AVAILABLE'),
('T04', 6, 'AVAILABLE'),
('T05', 2, 'DISABLED'),
('T06', 8, 'AVAILABLE');

INSERT INTO customers (customer_name, phone, age, email) VALUES 
('Rahul Patel', '9876543210', 22, 'rahul@example.com'),
('Priya Shah', '9876543211', 20, 'priya@example.com'),
('Amit Sharma', '9876543212', 25, 'amit@example.com'),
('Sneha Joshi', '9876543213', 19, 'sneha@example.com');

INSERT INTO menu_items (item_name, price, category, description, availability) VALUES 
('Signature Cold Coffee', 120.00, 'Beverages', 'Thick blended espresso with chocolate sauce.', 'Y'),
('Caramel Macchiato', 150.00, 'Beverages', 'Freshly brewed espresso with caramel drizzle.', 'Y'),
('Masala Chai', 50.00, 'Beverages', 'Authentic spiced Indian tea.', 'Y'),
('Classic Cappuccino', 130.00, 'Beverages', 'Velvety espresso with steamed milk foam.', 'Y'),
('Grilled Cheese Sandwich', 140.00, 'Food', 'Crispy artisan sourdough bread with cheddar.', 'Y'),
('Paneer Tikka Panini', 180.00, 'Food', 'Tandoori paneer slices with mint mayo.', 'Y'),
('Farmhouse Margherita Pizza', 260.00, 'Food', 'Hand-tossed crust with fresh mozzarella.', 'Y'),
('Belgian Chocolate Brownie', 150.00, 'Desserts', 'Fudgy warm chocolate brownie.', 'Y');

INSERT INTO bookings (table_id, customer_id, booking_date, start_time, end_time, booking_status) VALUES
(1, 1, CURDATE(), '14:00', '15:30', 'CONFIRMED'),
(2, 2, CURDATE(), '16:00', '17:00', 'CONFIRMED');

INSERT INTO cafe_orders (table_number, customer_name, total_amount, order_status) VALUES
('T01', 'Rahul Patel', 260.00, 'PLACED');

INSERT INTO order_items (order_id, item_id, quantity, unit_price) VALUES
(1, 1, 1, 120.00),
(1, 5, 1, 140.00);

-- 3(b) SELECT with Relational Operators (=, !=, >, <, >=, <=)
SELECT * FROM menu_items WHERE price >= 140.00;
SELECT * FROM cafe_tables WHERE capacity > 2 AND table_status = 'AVAILABLE';

-- 3(c) SELECT with LIKE '%' Wildcard
SELECT * FROM customers WHERE customer_name LIKE 'P%';
SELECT * FROM menu_items WHERE item_name LIKE '%Coffee%';

-- 3(d) UPDATE Statement
UPDATE customers SET email = 'rahul.patel@gmail.com' WHERE customer_name = 'Rahul Patel';

-- 3(e) DELETE Statement
INSERT INTO menu_items (item_name, price, category, availability) VALUES ('Sample Item', 99.00, 'Food', 'N');
DELETE FROM menu_items WHERE item_name = 'Sample Item';


-- ============================================================================
-- PRACTICAL 5: RELATIONAL JOINS
-- Concepts: Inner Join, Left Join, Right Join, Full Join
-- ============================================================================

-- 5(a) INNER JOIN
SELECT b.booking_id, c.customer_name, c.phone, t.table_number, b.booking_date, b.booking_status
FROM bookings b
INNER JOIN customers c ON b.customer_id = c.customer_id
INNER JOIN cafe_tables t ON b.table_id = t.table_id;

-- 5(b) LEFT JOIN: All tables and their bookings (including unbooked tables)
SELECT t.table_number, t.capacity, b.booking_id, b.booking_date, b.booking_status
FROM cafe_tables t
LEFT JOIN bookings b ON t.table_id = b.table_id
ORDER BY t.table_number;

-- 5(c) RIGHT JOIN: All customers and their reservations
SELECT c.customer_name, c.phone, b.booking_id, b.booking_date
FROM bookings b
RIGHT JOIN customers c ON b.customer_id = c.customer_id;

-- 5(d) FULL OUTER JOIN (Simulated in MySQL via UNION of LEFT & RIGHT JOIN)
SELECT t.table_number, b.booking_id, b.booking_status
FROM cafe_tables t
LEFT JOIN bookings b ON t.table_id = b.table_id
UNION
SELECT t.table_number, b.booking_id, b.booking_status
FROM cafe_tables t
RIGHT JOIN bookings b ON t.table_id = b.table_id;


-- ============================================================================
-- PRACTICAL 6: NESTED SUB QUERIES
-- ============================================================================

-- 6(a) Aggregate Subquery: Items priced higher than average price
SELECT item_name, price, category 
FROM menu_items 
WHERE price > (SELECT AVG(price) FROM menu_items);

-- 6(b) Subquery with IN: Customers who have placed at least one booking
SELECT customer_name, phone 
FROM customers 
WHERE customer_id IN (SELECT DISTINCT customer_id FROM bookings);

-- 6(c) Subquery with NOT IN: Tables that have never been reserved
SELECT table_number, capacity 
FROM cafe_tables 
WHERE table_id NOT IN (SELECT DISTINCT table_id FROM bookings);

-- 6(d) Correlated Subquery with EXISTS
SELECT c.customer_name, c.phone
FROM customers c
WHERE EXISTS (
    SELECT 1 FROM bookings b WHERE b.customer_id = c.customer_id AND b.booking_status = 'CONFIRMED'
);


-- ============================================================================
-- PRACTICAL 7: DATABASE VIEWS
-- Concepts: View, Join View, View with Check Option
-- ============================================================================

-- 7(a) Simple View: Available Tables
CREATE OR REPLACE VIEW v_available_tables AS
SELECT table_id, table_number, capacity
FROM cafe_tables
WHERE table_status = 'AVAILABLE';

-- 7(b) Join View: Comprehensive Admin Bookings Report
CREATE OR REPLACE VIEW v_admin_bookings_report AS
SELECT b.booking_id, c.customer_name, c.phone, t.table_number, t.capacity,
       b.booking_date, b.start_time, b.end_time, b.booking_status
FROM bookings b
JOIN customers c ON b.customer_id = c.customer_id
JOIN cafe_tables t ON b.table_id = t.table_id;

-- 7(d) View with CHECK OPTION
CREATE OR REPLACE VIEW v_active_beverages AS
SELECT item_id, item_name, price, category, availability
FROM menu_items
WHERE category = 'Beverages' AND availability = 'Y'
WITH CHECK OPTION;


-- ============================================================================
-- PRACTICAL 8: TRANSACTION CONTROL LANGUAGE (TCL)
-- Concepts: Commit, Rollback, Save Point
-- ============================================================================

START TRANSACTION;

INSERT INTO cafe_orders (table_number, total_amount, order_status)
VALUES ('T02', 150.00, 'PLACED');

SAVEPOINT sp_order_placed;

UPDATE cafe_orders SET total_amount = 999.00 WHERE table_number = 'T02';

-- Roll back to savepoint if error detected
ROLLBACK TO sp_order_placed;

-- Finalize transaction
COMMIT;


-- ============================================================================
-- PRACTICAL 9: DATA CONTROL LANGUAGE (DCL)
-- Concepts: Grant, Revoke
-- ============================================================================

-- CREATE USER 'cafe_staff'@'localhost' IDENTIFIED BY 'Cafe@123';
-- GRANT SELECT, INSERT ON cafe_db.cafe_orders TO 'cafe_staff'@'localhost';
-- REVOKE INSERT ON cafe_db.cafe_orders FROM 'cafe_staff'@'localhost';
-- FLUSH PRIVILEGES;


-- ============================================================================
-- PRACTICAL 10: DATABASE TRIGGERS
-- (a) Trigger to pop-up / log DML operations
-- (b) Trigger to check the age valid or not Using Message Alert
-- ============================================================================

-- 10(a) DML Operation Audit Trigger (Order Insert & Update)
DROP TRIGGER IF EXISTS trg_audit_orders_insert;
DELIMITER //
CREATE TRIGGER trg_audit_orders_insert
AFTER INSERT ON cafe_orders
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, operation, details)
    VALUES ('CAFE_ORDERS', 'INSERT', CONCAT('New order #', NEW.order_id, ' placed for Table ', NEW.table_number, ' (Total: Rs.', NEW.total_amount, ')'));
END //
DELIMITER ;

-- 10(b) Trigger to check the age valid or not Using Message Alert
DROP TRIGGER IF EXISTS trg_check_customer_age;
DELIMITER //
CREATE TRIGGER trg_check_customer_age
BEFORE INSERT ON customers
FOR EACH ROW
BEGIN
    IF NEW.age < 18 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'AGE ALERT: Customer must be at least 18 years old to make a cafe table reservation.';
    END IF;
END //
DELIMITER ;


-- ============================================================================
-- PRACTICAL 11: MINI PROJECT VERIFICATION QUERIES
-- ============================================================================
SELECT 'CAFE_TABLES' AS Table_Name, COUNT(*) AS Total_Rows FROM cafe_tables
UNION ALL
SELECT 'CUSTOMERS', COUNT(*) FROM customers
UNION ALL
SELECT 'BOOKINGS', COUNT(*) FROM bookings
UNION ALL
SELECT 'MENU_ITEMS', COUNT(*) FROM menu_items
UNION ALL
SELECT 'CAFE_ORDERS', COUNT(*) FROM cafe_orders
UNION ALL
SELECT 'ORDER_ITEMS', COUNT(*) FROM order_items
UNION ALL
SELECT 'AUDIT_LOG', COUNT(*) FROM audit_log;

SELECT * FROM v_admin_bookings_report;
