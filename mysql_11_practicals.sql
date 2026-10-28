-- QR Cafe Database - 11 DBMS Practicals
CREATE DATABASE IF NOT EXISTS cafe_db;
USE cafe_db;

-- Practical 1: Database Case Study (QR Cafe 4-Table Schema)
-- Entities: customers, menu_items, orders, order_items

-- Practical 2: DDL (Create, Alter, Drop, Truncate)
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS menu_items;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS temp_promotions;

-- 2(a) Create Tables with Practical 4 Constraints
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

-- 2(b) Alter Table
ALTER TABLE customers ADD COLUMN email VARCHAR(100) NULL;
ALTER TABLE customers MODIFY COLUMN customer_name VARCHAR(120) NOT NULL;

-- 2(c) Drop Table
CREATE TABLE temp_promotions (
    promo_id INT PRIMARY KEY,
    promo_code VARCHAR(20)
);
DROP TABLE IF EXISTS temp_promotions;

-- 2(d) Truncate Table
-- TRUNCATE TABLE order_items;

-- Practical 3: DML (Insert, Select, Update, Delete, LIKE, Relational Operators)

-- 3(a) Insert Data
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

INSERT INTO customers (customer_name, phone, age, email) VALUES
('Aarav Mehta', '9876543210', 21, 'aarav@gmail.com'),
('Diya Sharma', '9811223344', 24, 'diya@gmail.com'),
('Rohan Verma', '9822334455', 19, 'rohan@gmail.com');

INSERT INTO orders (customer_id, table_number, total_amount, order_status) VALUES
(1, 'Table 1', 270.00, 'PLACED'),
(2, 'Table 3', 390.00, 'PREPARING'),
(3, 'Table 2', 120.00, 'SERVED');

INSERT INTO order_items (order_id, item_id, quantity, unit_price) VALUES
(1, 1, 1, 120.00),
(1, 9, 1, 150.00),
(2, 7, 1, 250.00),
(2, 5, 1, 140.00),
(3, 1, 1, 120.00);

-- 3(b) Select Queries
SELECT customer_id, customer_name, phone, age FROM customers;
SELECT item_id, item_name, price FROM menu_items WHERE is_available = 'Y';

-- 3(c) Update Queries
UPDATE orders SET order_status = 'SERVED' WHERE order_id = 1;
UPDATE menu_items SET price = 125.00 WHERE item_name = 'Signature Cold Coffee';

-- 3(d) Delete Query
INSERT INTO menu_items (item_name, category, price, is_available) VALUES ('Temp Green Tea', 'Beverages', 80.00, 'Y');
DELETE FROM menu_items WHERE item_name = 'Temp Green Tea';

-- 3(e) LIKE Wildcard Pattern Matching
SELECT * FROM menu_items WHERE item_name LIKE '%Coffee%';
SELECT * FROM menu_items WHERE item_name LIKE 'P%';

-- 3(f) Relational Operators
SELECT item_name, category, price FROM menu_items WHERE price >= 150.00;
SELECT * FROM orders WHERE total_amount < 300.00;

-- Practical 4: Constraints (PK, FK, Unique, Check, Not Null, Default)

-- 4(h) Disable Constraints
SET FOREIGN_KEY_CHECKS = 0;

-- 4(i) Enable Constraints
SET FOREIGN_KEY_CHECKS = 1;

-- 4(j) Drop Constraint
ALTER TABLE customers ADD CONSTRAINT chk_test CHECK (age < 150);
ALTER TABLE customers DROP CHECK chk_test;

-- Practical 5: Joins (Inner, Left, Right, Full)

-- 5(a) Inner Join
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

-- 5(b) Left Join
SELECT 
    m.item_name,
    m.price,
    COALESCE(SUM(oi.quantity), 0) AS total_sold
FROM menu_items m
LEFT JOIN order_items oi ON m.item_id = oi.item_id
GROUP BY m.item_id, m.item_name, m.price;

-- 5(c) Right Join
SELECT 
    oi.order_id,
    oi.quantity,
    m.item_name,
    m.price
FROM order_items oi
RIGHT JOIN menu_items m ON oi.item_id = m.item_id;

-- 5(d) Full Outer Join
SELECT c.customer_name, o.order_id, o.total_amount
FROM customers c
LEFT JOIN orders o ON c.customer_id = o.customer_id
UNION
SELECT c.customer_name, o.order_id, o.total_amount
FROM customers c
RIGHT JOIN orders o ON c.customer_id = o.customer_id;

-- Practical 6: Nested Subqueries

-- 6(a) Single-Row Subquery with Aggregate
SELECT order_id, table_number, total_amount, order_status
FROM orders
WHERE total_amount > (SELECT AVG(total_amount) FROM orders);

-- 6(b) Subquery with NOT IN
SELECT item_id, item_name, category, price
FROM menu_items
WHERE item_id NOT IN (SELECT DISTINCT item_id FROM order_items);

-- 6(c) Subquery with IN
SELECT customer_name, phone
FROM customers
WHERE customer_id IN (
    SELECT o.customer_id
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN menu_items m ON oi.item_id = m.item_id
    WHERE m.item_name = 'Farmhouse Pizza'
);

-- Practical 7: Views (Simple View, Join View, Force View, View with Check Option)

-- 7(a) Simple View
CREATE OR REPLACE VIEW v_active_beverages AS
SELECT item_id, item_name, price, category
FROM menu_items
WHERE category = 'Beverages' AND is_available = 'Y';

SELECT * FROM v_active_beverages;

-- 7(b) Join View
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

-- 7(c) Force View Concept
CREATE OR REPLACE VIEW v_order_summary AS
SELECT order_status, COUNT(*) AS count_orders, SUM(total_amount) AS total_revenue
FROM orders
GROUP BY order_status;

-- 7(d) View with CHECK OPTION
CREATE OR REPLACE VIEW v_beverages_check AS
SELECT item_id, item_name, price, category, is_available
FROM menu_items
WHERE category = 'Beverages'
WITH CHECK OPTION;

-- Practical 8: TCL (Commit, Rollback, Savepoint)

-- 8(a) Commit
START TRANSACTION;
INSERT INTO customers (customer_name, phone, age) VALUES ('Siddharth Rao', '9899001122', 22);
SET @sid = LAST_INSERT_ID();
INSERT INTO orders (customer_id, table_number, total_amount, order_status) VALUES (@sid, 'Table 4', 210.00, 'PLACED');
INSERT INTO order_items (order_id, item_id, quantity, unit_price) VALUES (LAST_INSERT_ID(), 10, 1, 210.00);
COMMIT;

-- 8(b) Rollback
START TRANSACTION;
INSERT INTO menu_items (item_name, category, price, is_available) VALUES ('Faulty Item', 'Food', 999.00, 'Y');
ROLLBACK;

-- 8(c) Savepoint
START TRANSACTION;
INSERT INTO customers (customer_name, phone, age) VALUES ('Test Guest', '9000000001', 25);
SAVEPOINT sp_after_guest;
INSERT INTO orders (customer_id, table_number, total_amount, order_status) VALUES (LAST_INSERT_ID(), 'Table 99', 500.00, 'PLACED');
ROLLBACK TO SAVEPOINT sp_after_guest;
COMMIT;

-- Practical 9: DCL (Grant, Revoke)
CREATE USER IF NOT EXISTS 'cafe_waiter'@'localhost' IDENTIFIED BY 'cafe_pass123';

-- 9(a) Grant Permissions
GRANT SELECT, INSERT, UPDATE ON cafe_db.orders TO 'cafe_waiter'@'localhost';
GRANT SELECT, INSERT ON cafe_db.order_items TO 'cafe_waiter'@'localhost';
GRANT SELECT ON cafe_db.menu_items TO 'cafe_waiter'@'localhost';
FLUSH PRIVILEGES;

-- 9(b) Revoke Permissions
REVOKE DELETE ON cafe_db.orders FROM 'cafe_waiter'@'localhost';
FLUSH PRIVILEGES;

-- Practical 10: Triggers (Age Check Trigger with Alert)
DELIMITER $$

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

-- Practical 11: Mini Project (QR Cafe Web Application)
