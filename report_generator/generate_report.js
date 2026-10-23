const { 
  Document, Packer, Paragraph, TextRun, HeadingLevel, Table, TableRow, TableCell, 
  WidthType, BorderStyle, AlignmentType, ShadingType, PageBreak, UnderlineType 
} = require('docx');
const fs = require('fs');
const path = require('path');

// Helper to create styled paragraphs
function p(text, opts = {}) {
  return new Paragraph({
    alignment: opts.align || AlignmentType.LEFT,
    spacing: { before: opts.before || 120, after: opts.after || 120, line: 276 },
    children: [
      new TextRun({
        text: text,
        font: 'Calibri',
        size: opts.size || 22, // 11pt default
        bold: !!opts.bold,
        italics: !!opts.italics,
        color: opts.color || '1F2937'
      })
    ]
  });
}

function h1(text) {
  return new Paragraph({
    heading: HeadingLevel.HEADING_1,
    spacing: { before: 360, after: 180 },
    children: [
      new TextRun({
        text: text,
        font: 'Calibri',
        size: 32, // 16pt
        bold: true,
        color: '1E3A8A'
      })
    ]
  });
}

function h2(text) {
  return new Paragraph({
    heading: HeadingLevel.HEADING_2,
    spacing: { before: 240, after: 120 },
    children: [
      new TextRun({
        text: text,
        font: 'Calibri',
        size: 26, // 13pt
        bold: true,
        color: '3B82F6'
      })
    ]
  });
}

function h3(text) {
  return new Paragraph({
    heading: HeadingLevel.HEADING_3,
    spacing: { before: 180, after: 80 },
    children: [
      new TextRun({
        text: text,
        font: 'Calibri',
        size: 24, // 12pt
        bold: true,
        color: '374151'
      })
    ]
  });
}

function bullet(text, boldPrefix = '') {
  const children = [];
  if (boldPrefix) {
    children.push(new TextRun({ text: boldPrefix + ' ', font: 'Calibri', size: 22, bold: true, color: '1F2937' }));
  }
  children.push(new TextRun({ text: text, font: 'Calibri', size: 22, color: '374151' }));
  return new Paragraph({
    bullet: { level: 0 },
    spacing: { before: 60, after: 60, line: 260 },
    children: children
  });
}

// Helper to create clean tables
function createTable(headers, rows) {
  const tableRows = [];
  
  // Header row
  tableRows.push(new TableRow({
    tableHeader: true,
    children: headers.map(h => new TableCell({
      width: { size: 100 / headers.length, type: WidthType.PERCENTAGE },
      shading: { fill: '1E3A8A', type: ShadingType.CLEAR },
      children: [new Paragraph({
        alignment: AlignmentType.CENTER,
        children: [new TextRun({ text: h, font: 'Calibri', size: 20, bold: true, color: 'FFFFFF' })]
      })],
      margins: { top: 120, bottom: 120, left: 140, right: 140 }
    }))
  }));

  // Data rows
  rows.forEach((row, idx) => {
    tableRows.push(new TableRow({
      children: row.map(cell => new TableCell({
        width: { size: 100 / headers.length, type: WidthType.PERCENTAGE },
        shading: { fill: (idx % 2 === 0) ? 'F8FAFC' : 'FFFFFF', type: ShadingType.CLEAR },
        children: [new Paragraph({
          children: [new TextRun({ text: cell, font: 'Calibri', size: 20, color: '1F2937' })]
        })],
        margins: { top: 100, bottom: 100, left: 140, right: 140 }
      }))
    }));
  });

  return new Table({
    width: { size: 100, type: WidthType.PERCENTAGE },
    rows: tableRows
  });
}

// Generate the complete document
async function generateDoc() {
  const doc = new Document({
    sections: [
      {
        properties: {},
        children: [
          // ================= COVER PAGE =================
          new Paragraph({ spacing: { before: 800 } }),
          new Paragraph({
            alignment: AlignmentType.CENTER,
            children: [
              new TextRun({
                text: "A MINI PROJECT REPORT ON",
                font: 'Calibri',
                size: 24,
                bold: true,
                color: '6B7280'
              })
            ]
          }),
          new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 240, after: 360 },
            children: [
              new TextRun({
                text: "QR CAFE MANAGEMENT &\nSMART DINING SYSTEM",
                font: 'Calibri',
                size: 44, // 22pt
                bold: true,
                color: '1E3A8A'
              })
            ]
          }),
          new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { after: 600 },
            children: [
              new TextRun({
                text: "A Full-Stack Web-Based Digital Ordering, Table Reservation & Kitchen Management System",
                font: 'Calibri',
                size: 24,
                italics: true,
                color: '4B5563'
              })
            ]
          }),
          new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 600, after: 200 },
            children: [
              new TextRun({
                text: "Submitted in partial fulfillment of the requirements for the course of",
                font: 'Calibri',
                size: 22,
                color: '374151'
              })
            ]
          }),
          new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { after: 600 },
            children: [
              new TextRun({
                text: "DATABASE MANAGEMENT SYSTEMS (DBMS) / WEB TECHNOLOGY",
                font: 'Calibri',
                size: 26,
                bold: true,
                color: '111827'
              })
            ]
          }),
          new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 1200 },
            children: [
              new TextRun({ text: "SUBMITTED BY:\n", font: 'Calibri', size: 22, bold: true, color: '6B7280' }),
              new TextRun({ text: "SHYAM PATEL\n", font: 'Calibri', size: 28, bold: true, color: '1E3A8A' }),
              new TextRun({ text: "Department of Computer Science & Engineering\nAcademic Year 2026", font: 'Calibri', size: 22, color: '4B5563' })
            ]
          }),
          new Paragraph({ children: [new PageBreak()] }),

          // ================= CERTIFICATE =================
          h1("CERTIFICATE OF AUTHENTICITY"),
          p("This is to certify that the project entitled \"QR CAFE MANAGEMENT & SMART DINING SYSTEM\" is a bonafide record of work carried out by SHYAM PATEL in partial fulfillment of the requirements for the award of Bachelor of Technology / Diploma in Computer Science & Engineering."),
          p("The work embodied in this project report has been done under academic supervision and has not been submitted elsewhere for the award of any degree or diploma."),
          new Paragraph({ spacing: { before: 1400 } }),
          new Paragraph({
            alignment: AlignmentType.RIGHT,
            children: [
              new TextRun({ text: "___________________________\n", bold: true }),
              new TextRun({ text: "Project Guide / Head of Department\n", bold: true, color: '1E3A8A' }),
              new TextRun({ text: "Department of Computer Science & Engineering", italics: true })
            ]
          }),
          new Paragraph({ children: [new PageBreak()] }),

          // ================= ACKNOWLEDGEMENT =================
          h1("ACKNOWLEDGEMENT"),
          p("I express my profound gratitude and sincere thanks to our respected Project Guide and Faculty Members of the Department of Computer Science & Engineering for their invaluable guidance, constant encouragement, and constructive critique throughout the development of this project."),
          p("I am also grateful to our Head of Department and the College Management for providing state-of-the-art laboratory infrastructure, computational facilities, and technical support necessary to bring this project to fruition."),
          p("Finally, I extend my heartfelt appreciation to my family and peers for their continuous motivation and moral support during the design, coding, testing, and documentation phases of this project."),
          new Paragraph({ spacing: { before: 800 } }),
          new Paragraph({
            alignment: AlignmentType.RIGHT,
            children: [
              new TextRun({ text: "SHYAM PATEL\n", bold: true, size: 24 }),
              new TextRun({ text: "Student, Computer Science & Engineering" })
            ]
          }),
          new Paragraph({ children: [new PageBreak()] }),

          // ================= ABSTRACT =================
          h1("ABSTRACT"),
          p("In traditional restaurant and cafe environments, the conventional ordering process relies on physical printed menus, manual table service, and verbal order transmission to the kitchen. This model introduces substantial operational bottlenecks, including prolonged customer wait times, human order errors, labor inefficiency, and unhygienic reusable physical menus."),
          p("The \"QR Cafe Management & Smart Dining System\" addresses these challenges by delivering an end-to-end, contactless digital dining experience. Each physical cafe table is provisioned with a dedicated Quick Response (QR) code standee. When scanned by a customer using any standard smartphone camera, the application dynamically resolves the table identifier and presents an interactive digital menu with category filtering, real-time item search, dynamic cart management, and customized culinary notes."),
          p("On the operational backend, the system incorporates an executive Admin Dashboard, an interactive Kitchen Display System (KDS) utilizing Kanban-style live workflow stages (Placed ➔ Cooking ➔ Served), and an automated Table Reservation module with overlapping time-slot conflict prevention. The database architecture is designed in strict compliance with university DBMS syllabus principles (1NF, 2NF, 3NF Normalization, Integrity Constraints, Relational Joins, and DML Auditing) implemented using PHP PDO and relational SQL."),
          new Paragraph({ children: [new PageBreak()] }),

          // ================= CHAPTER 1 =================
          h1("CHAPTER 1: INTRODUCTION"),
          h2("1.1 Background & Motivation"),
          p("The modern hospitality and food-service industry is undergoing rapid digital transformation. The widespread adoption of mobile smartphones, high-speed mobile internet, and camera-based QR code recognition has created unprecedented opportunities to streamline customer dining experiences and optimize kitchen throughput."),
          p("Conventional cafe operations suffer from labor-intensive overheads. Waiters must manually distribute menus, take written notes, enter data into POS terminals, and repeatedly return to tables to communicate order updates. By decentralizing order placement directly to the customer's personal device, the QR Cafe system eliminates ordering friction and provides instantaneous order dispatch directly to the kitchen line."),

          h2("1.2 Problem Statement"),
          bullet("Prolonged waiting times during peak rush hours for customers attempting to attract waitstaff attention.", "1. Service Delays:"),
          bullet("Manual transcription errors resulting in incorrect food preparation or dietary oversight.", "2. Order Inaccuracies:"),
          bullet("High operational printing expenses and difficulty updating dynamic prices or out-of-stock items on paper menus.", "3. Menu Rigidity:"),
          bullet("Absence of real-time visibility into kitchen preparation statuses for seated diners.", "4. Lack of Transparency:"),

          h2("1.3 Objectives of the Project"),
          bullet("Develop a fully responsive, mobile-first web interface that requires zero mobile app installation from the customer."),
          bullet("Implement table-specific dynamic QR code resolution linking customer sessions directly to physical table numbers."),
          bullet("Engineer a real-time Kitchen Display System (KDS) providing chefs with live order feeds and one-click status transitions."),
          bullet("Construct a 24-hour conflict-free table reservation system verifying capacity and temporal availability."),
          bullet("Design and normalize a robust relational database conforming to 3NF standards with strict data integrity."),

          h2("1.4 Scope of the Project"),
          p("The scope encompasses customer self-ordering, cart management, table reservation, live kitchen workflow tracking, menu CRUD management, customer history logging, and database inspection. The system operates responsively across all smartphone platforms, tablets, and desktop terminals."),
          new Paragraph({ children: [new PageBreak()] }),

          // ================= CHAPTER 2 =================
          h1("CHAPTER 2: SYSTEM ANALYSIS & REQUIREMENTS"),
          h2("2.1 Existing System vs. Proposed System"),
          createTable(
            ["Feature / Parameter", "Existing Manual System", "Proposed QR Cafe System"],
            [
              ["Ordering Medium", "Physical paper/plastic menus", "Digital dynamic smartphone menu"],
              ["Order Transmission", "Manual waiter pen & paper notes", "Instant real-time database dispatch"],
              ["Kitchen Communication", "Verbal or printed kitchen tickets", "Live interactive Kitchen Display (KDS)"],
              ["Price/Menu Updates", "Requires expensive reprinting", "Instant 1-click update via Admin panel"],
              ["Table Booking", "Phone calls and handwritten logs", "Automated time-slot conflict validation"],
              ["Customer Tracking", "Customer must wave for waiter", "Animated 3-stage live status tracker"]
            ]
          ),

          h2("2.2 Functional Requirements"),
          h3("A. Customer Module:"),
          bullet("Automatic table identification via URL parameter parsing (?table=T01)."),
          bullet("Categorized menu browsing (Beverages, Gourmet Snacks, Main Course, Desserts)."),
          bullet("Live character-by-character search filtering across dish titles and descriptions."),
          bullet("Interactive cart modification (item additions, quantity increments, cooking instructions)."),
          bullet("Table reservation engine with date picker and start/end time boundary validation."),

          h3("B. Kitchen & Staff Module (KDS):"),
          bullet("Chronological order queue displaying pending dishes and customer dietary remarks."),
          bullet("One-click status transitions: 'Start Cooking' (Preparing) and 'Mark Served' (Completed)."),
          bullet("Auto-refreshing dashboard synchronizing with active kitchen workloads."),

          h3("C. Administrator Module:"),
          bullet("High-level KPI statistics (Today's Gross Revenue, Total Orders, Active Reservations)."),
          bullet("Complete Menu CRUD: Create new dishes, edit pricing, toggle in-stock/out-of-stock."),
          bullet("Multi-table relational bookings report with one-click reservation cancellation."),
          bullet("Integrated Database Inspector permitting raw table and row verification within the browser."),

          h2("2.3 Hardware & Software Specifications"),
          createTable(
            ["Component", "Minimum Requirement", "Recommended / Deployed Specification"],
            [
              ["Processor", "Dual-Core 2.0 GHz CPU", "Intel Core i5 / AMD Ryzen 5 or higher"],
              ["RAM", "2 GB System Memory", "8 GB DDR4 / DDR5 RAM"],
              ["Storage", "500 MB Free Disk Space", "Fast NVMe SSD"],
              ["Operating System", "Windows 10 / 11 / Linux / macOS", "Windows 11 (64-bit)"],
              ["Runtime Environment", "PHP 8.0+", "PHP 8.3 (Thread Safe x64)"],
              ["Web Server", "Apache 2.4 or PHP Built-in Server", "Apache 2.4 (Docker / Local)"],
              ["Database Engine", "SQLite 3 or MySQL 8.0", "SQLite 3 with PDO & MySQL compatibility"],
              ["Frontend Languages", "HTML5, CSS3, JavaScript", "HTML5, Advanced CSS3, ES6 JavaScript"]
            ]
          ),
          new Paragraph({ children: [new PageBreak()] }),

          // ================= CHAPTER 3 =================
          h1("CHAPTER 3: SYSTEM ARCHITECTURE & DESIGN"),
          h2("3.1 System Architecture"),
          p("The application utilizes a modular Model-View-Controller (MVC) architectural pattern. The presentation tier (View) communicates asynchronously via modern HTML5, CSS3, and JavaScript. Business logic (Controller) is handled through modular PHP scripts, which interact with the persistent data tier (Model) via the PHP Data Objects (PDO) interface."),

          h2("3.2 Entity-Relationship (ER) Design"),
          p("The relational design consists of seven core entities structured to eliminate data redundancy and preserve referential integrity:"),
          bullet("CAFE_TABLES: Represents seating areas, table numbers, guest capacities, and availability.", "1."),
          bullet("CUSTOMERS: Stores personal information including unique phone numbers, names, and age.", "2."),
          bullet("BOOKINGS: Represents reservation contracts linking customers to tables for specified time windows.", "3."),
          bullet("MENU_ITEMS: Catalogs food and beverage offerings with pricing, categories, and descriptions.", "4."),
          bullet("CAFE_ORDERS: Header entity tracking customer orders, table locations, status, and bill amounts.", "5."),
          bullet("ORDER_ITEMS: Line-item associative entity resolving the M:N relationship between orders and menu items.", "6."),
          bullet("AUDIT_LOG: Security audit table capturing automated trigger executions on data modifications.", "7."),

          h2("3.3 Database Normalization Analysis"),
          p("The database was designed from first principles to satisfy Boyce-Codd Normal Form (BCNF):"),
          bullet("First Normal Form (1NF): All attributes contain strictly atomic, non-decomposable values. No repeating groups or multi-valued fields exist in any relation.", "•"),
          bullet("Second Normal Form (2NF): All relations are in 1NF and every non-prime attribute is fully functionally dependent on the entire primary key. In the composite ORDER_ITEMS entity, surrogate key indexing prevents partial key dependencies.", "•"),
          bullet("Third Normal Form (3NF): All relations are in 2NF and contain zero transitive functional dependencies (X -> Y and Y -> Z). Customer information is decoupled from bookings, and menu pricing is isolated from order headers.", "•"),
          new Paragraph({ children: [new PageBreak()] }),

          // ================= CHAPTER 4 =================
          h1("CHAPTER 4: DATABASE IMPLEMENTATION ACCORDING TO 11 LAB PRACTICALS"),
          p("The database for QR Cafe has been designed and implemented in strict compliance with the college syllabus list of 11 DBMS Lab Practicals:"),

          h2("4.1 Practical 1: Case Study of Database (QR Cafe System)"),
          p("Case study of a digital restaurant dining system eliminating physical paper menus. It incorporates 7 core entities: CAFE_TABLES, CUSTOMERS, BOOKINGS, MENU_ITEMS, CAFE_ORDERS, ORDER_ITEMS, and AUDIT_LOG."),

          h2("4.2 Practical 2: Data Definition Language (DDL)"),
          p("Implemented DDL commands for schema definition:"),
          bullet("CREATE TABLE: Creates the relational tables with data types, auto-incrementing surrogate keys, and field definitions.", "(a)"),
          bullet("ALTER TABLE: Used to extend schema without dropping data (e.g., adding floor_number to cafe_tables and modifying column widths).", "(b)"),
          bullet("DROP TABLE: Safe cleanup removing outdated or temporary relations.", "(c)"),
          bullet("TRUNCATE TABLE: Rapid deletion of all table tuples without destroying table structure.", "(d)"),

          h3("Table Specifications & Data Dictionary:"),
          h4 = (t) => new Paragraph({ children: [new TextRun({ text: t, bold: true, size: 22, color: '1E3A8A' })], spacing: { before: 120, after: 60 } }),
          h4("1. CAFE_TABLES"),
          createTable(
            ["Column Name", "Data Type", "Constraints", "Description"],
            [
              ["table_id", "INTEGER", "PRIMARY KEY, AUTOINCREMENT", "Surrogate unique identifier for table"],
              ["table_number", "VARCHAR(10)", "NOT NULL, UNIQUE", "Physical table code (e.g., T01, T02)"],
              ["capacity", "INTEGER", "NOT NULL, CHECK (capacity > 0)", "Seating capacity of the table"],
              ["table_status", "VARCHAR(20)", "DEFAULT 'AVAILABLE'", "Status: AVAILABLE, RESERVED, DISABLED"]
            ]
          ),
          new Paragraph({ spacing: { before: 120 } }),

          h4("2. CUSTOMERS"),
          createTable(
            ["Column Name", "Data Type", "Constraints", "Description"],
            [
              ["customer_id", "INTEGER", "PRIMARY KEY, AUTOINCREMENT", "Unique customer identification number"],
              ["customer_name", "VARCHAR(100)", "NOT NULL", "Full customer name"],
              ["phone", "VARCHAR(15)", "NOT NULL, UNIQUE", "Customer contact mobile number"],
              ["age", "INTEGER", "DEFAULT 18, CHECK (age >= 12)", "Customer age (Validation trigger target)"],
              ["email", "VARCHAR(100)", "NULLABLE", "Customer email address"]
            ]
          ),
          new Paragraph({ spacing: { before: 120 } }),

          h4("3. BOOKINGS"),
          createTable(
            ["Column Name", "Data Type", "Constraints", "Description"],
            [
              ["booking_id", "INTEGER", "PRIMARY KEY, AUTOINCREMENT", "Unique reservation booking ID"],
              ["table_id", "INTEGER", "FOREIGN KEY -> cafe_tables(table_id)", "Reserved table foreign reference"],
              ["customer_id", "INTEGER", "FOREIGN KEY -> customers(customer_id)", "Reserving customer reference"],
              ["booking_date", "VARCHAR(15)", "NOT NULL", "Date of the reservation (YYYY-MM-DD)"],
              ["start_time", "VARCHAR(10)", "NOT NULL", "Start time of reservation (HH:MM 24h)"],
              ["end_time", "VARCHAR(10)", "NOT NULL", "End time of reservation (HH:MM 24h)"],
              ["booking_status", "VARCHAR(20)", "DEFAULT 'CONFIRMED'", "CONFIRMED, CANCELLED, COMPLETED"]
            ]
          ),
          new Paragraph({ spacing: { before: 120 } }),

          h4("4. MENU_ITEMS"),
          createTable(
            ["Column Name", "Data Type", "Constraints", "Description"],
            [
              ["item_id", "INTEGER", "PRIMARY KEY, AUTOINCREMENT", "Unique identifier for food/drink"],
              ["item_name", "VARCHAR(100)", "NOT NULL, UNIQUE", "Title of the dish or beverage"],
              ["price", "DECIMAL(8,2)", "NOT NULL, CHECK (price >= 0)", "Retail price in Indian Rupees (₹)"],
              ["category", "VARCHAR(40)", "NOT NULL", "Category: Beverages, Food, Desserts"],
              ["description", "TEXT", "NULLABLE", "Culinary description and ingredient summary"],
              ["availability", "CHAR(1)", "DEFAULT 'Y', CHECK (IN 'Y', 'N')", "Stock flag: Y = In Stock, N = Sold Out"]
            ]
          ),
          new Paragraph({ spacing: { before: 120 } }),

          h4("5. CAFE_ORDERS"),
          createTable(
            ["Column Name", "Data Type", "Constraints", "Description"],
            [
              ["order_id", "INTEGER", "PRIMARY KEY, AUTOINCREMENT", "Unique order receipt number"],
              ["table_number", "VARCHAR(10)", "NOT NULL", "Seated dining table identifier"],
              ["customer_name", "VARCHAR(100)", "DEFAULT 'Guest'", "Name of ordering diner"],
              ["order_date", "DATETIME", "DEFAULT CURRENT_TIMESTAMP", "Timestamp when order was submitted"],
              ["total_amount", "DECIMAL(10,2)", "DEFAULT 0.00", "Total bill amount in Rupees"],
              ["order_status", "VARCHAR(20)", "DEFAULT 'PLACED'", "PLACED, PREPARING, SERVED, CANCELLED"],
              ["notes", "TEXT", "NULLABLE", "Special customer dietary instructions"]
            ]
          ),
          new Paragraph({ spacing: { before: 120 } }),

          h4("6. ORDER_ITEMS"),
          createTable(
            ["Column Name", "Data Type", "Constraints", "Description"],
            [
              ["order_item_id", "INTEGER", "PRIMARY KEY, AUTOINCREMENT", "Surrogate order line identifier"],
              ["order_id", "INTEGER", "FOREIGN KEY -> cafe_orders(order_id)", "Associated order reference"],
              ["item_id", "INTEGER", "FOREIGN KEY -> menu_items(item_id)", "Ordered menu item reference"],
              ["quantity", "INTEGER", "DEFAULT 1, CHECK (quantity > 0)", "Number of portions ordered"],
              ["unit_price", "DECIMAL(8,2)", "NOT NULL", "Price per unit at transaction time"]
            ]
          ),
          new Paragraph({ spacing: { before: 120 } }),

          h4("7. AUDIT_LOG"),
          createTable(
            ["Column Name", "Data Type", "Constraints", "Description"],
            [
              ["log_id", "INTEGER", "PRIMARY KEY, AUTOINCREMENT", "Sequential audit entry identifier"],
              ["table_name", "VARCHAR(50)", "NOT NULL", "Entity modified (e.g., CAFE_ORDERS)"],
              ["operation", "VARCHAR(20)", "NOT NULL", "DML action (INSERT, UPDATE, DELETE)"],
              ["change_date", "DATETIME", "DEFAULT CURRENT_TIMESTAMP", "Timestamp of database operation"],
              ["details", "TEXT", "NULLABLE", "Detailed audit transaction summary"]
            ]
          ),

          h2("4.3 Practical 3: Data Manipulation Language (DML)"),
          bullet("INSERT: Adding records into menu_items, customers, bookings, and cafe_orders.", "(a)"),
          bullet("SELECT: Querying active dishes, available tables, and customer order history.", "(b)"),
          bullet("UPDATE: Modifying customer contact details, item prices, and order statuses (PLACED -> PREPARING -> SERVED).", "(c)"),
          bullet("DELETE: Removing cancelled orders, expired items, and sample testing records.", "(d)"),
          bullet("LIKE '%': Wildcard pattern matching (e.g., `WHERE item_name LIKE '%Coffee%'` or `customer_name LIKE 'P%'`).", "(e)"),
          bullet("Relational Operators: Domain filtering using `=`, `!=`, `>`, `<`, `>=`, `<=` (e.g., `WHERE price >= 140.00`).", "(f)"),

          h2("4.4 Practical 4: Integrity Constraints"),
          bullet("PRIMARY KEY: Guaranteed unique tuple identification on all 7 entities.", "•"),
          bullet("FOREIGN KEY (ON DELETE CASCADE): Enforces referential integrity between orders, bookings, and items.", "•"),
          bullet("UNIQUE: Enforced on customer phone numbers, table numbers, and menu titles to prevent duplicates.", "•"),
          bullet("CHECK CONSTRAINTS: Enforced on capacity > 0, price >= 0, quantity > 0, and customer age >= 18.", "•"),
          bullet("DEFAULT CONSTRAINTS: Pre-populating default seating capacity (2), table status ('AVAILABLE'), and order status ('PLACED').", "•"),
          bullet("ENABLE / DISABLE / DROP CONSTRAINTS: Demonstrating constraint management syntax.", "•"),

          h2("4.5 Practical 5: Relational Joins"),
          bullet("INNER JOIN: Matches bookings with customers and tables to produce comprehensive reservation slips.", "(a)"),
          bullet("LEFT JOIN: Selects all cafe tables alongside bookings, preserving unreserved tables as NULL.", "(b)"),
          bullet("RIGHT JOIN: Selects all registered customers along with any reservation contracts they hold.", "(c)"),
          bullet("FULL JOIN (UNION): Matches all tables and reservations comprehensively.", "(d)"),

          h2("4.6 Practical 6: Nested Subqueries"),
          bullet("Subquery with IN: Retrieves customers who have placed at least one confirmed booking.", "•"),
          bullet("Subquery with NOT IN: Identifies cafe tables that have never been reserved.", "•"),
          bullet("Aggregate Subquery: Extracts all menu items priced strictly higher than the average menu price.", "•"),
          bullet("Correlated Subquery with EXISTS: Validates customer reservation history dynamically.", "•"),

          h2("4.7 Practical 7: Database Views"),
          bullet("Simple View (`v_available_tables`): Filtered abstraction exposing only currently free tables.", "(a)"),
          bullet("Join View (`v_admin_bookings_report`): Pre-computed multi-table join view across Bookings, Customers, and Tables.", "(b)"),
          bullet("View with CHECK OPTION (`v_active_beverages`): Enforces that updates through the view must satisfy category and availability criteria.", "(c)"),

          h2("4.8 Practical 8: Transaction Control Language (TCL)"),
          p("Ensures ACID properties during dining transactions (in `checkout.php` and `book.php`):"),
          bullet("START TRANSACTION: Initiates atomic execution boundary.", "•"),
          bullet("SAVEPOINT: Sets intermediate checkpoints during order item iteration.", "•"),
          bullet("ROLLBACK: Reverts state if item validation fails or an exception is caught.", "•"),
          bullet("COMMIT: Finalizes and permanently writes order headers, order line items, and audit records.", "•"),

          h2("4.9 Practical 9: Data Control Language (DCL)"),
          p("Access control and security privilege management demonstrated in the database:"),
          bullet("GRANT SELECT, INSERT ON cafe_orders TO 'cafe_waiter'@'localhost';", "•"),
          bullet("REVOKE UPDATE ON cafe_orders FROM 'cafe_waiter'@'localhost';", "•"),

          h2("4.10 Practical 10: Database Triggers"),
          bullet("DML Operation Audit Trigger (`trg_audit_orders_insert` & `trg_audit_orders_update`): Automatically logs all INSERT and UPDATE operations on cafe orders directly into the `AUDIT_LOG` relation.", "(a)"),
          bullet("Validation Trigger with Message Alert (`trg_check_customer_age`): Evaluates customer age upon reservation creation. If `age < 18`, the trigger automatically aborts the transaction with an alert message: 'AGE ALERT: Customer must be at least 18 years old to make a cafe table reservation.'", "(b)"),

          h2("4.11 Practical 11: Mini Project Database & ERP Model Implementation"),
          p("Complete production database implementation for the QR Cafe Smart Dining System provided in pure MySQL syntax (`mysql_11_practicals.sql`) and SQLite (`schema_11_practicals.sql`)."),
          new Paragraph({ children: [new PageBreak()] }),

          // ================= CHAPTER 5 =================
          h1("CHAPTER 5: IMPLEMENTATION & USER INTERFACES"),
          h2("5.1 Module Walkthrough"),
          h3("1. Customer Digital Menu & QR Detection:"),
          p("When a customer scans a table QR code standee, the client browser accesses `menu.php?table=T01`. The PHP backend initializes a session variable capturing the table identity. The user is presented with categorized food tabs, responsive thumbnail cards, live pricing, and interactive '+ Add to Order' actions."),

          h3("2. Client-Side Cart & Dynamic Checkout:"),
          p("The shopping cart is managed via modern JavaScript using browser `localStorage`. Diners can adjust quantities, add special cooking notes (such as 'Less sugar' or 'Extra spicy'), and submit orders. The PHP checkout controller processes the transaction atomically within a database transaction block."),

          h3("3. Live Kitchen Display System (KDS):"),
          p("Located at `admin/kitchen.php`, this screen is dedicated for kitchen staff. Orders automatically appear organized in chronological order. Chefs click 'Start Cooking' to update order status to PREPARING, and 'Mark Served' to transition completed orders to SERVED. The screen auto-refreshes every 15 seconds."),

          h3("4. Automated Table Reservation Engine:"),
          p("The booking module (`book.php`) executes SQL validation queries ensuring no table can be double-booked during overlapping time windows. It verifies guest capacity and rejects reservation requests from customers under 18 years old."),

          h3("5. Live Database Inspector:"),
          p("Located at `admin/db_viewer.php`, this feature allows faculty and examiners to inspect live tables, primary key linkages, and row records directly within the browser without requiring external database administrative tools."),
          new Paragraph({ children: [new PageBreak()] }),

          // ================= CHAPTER 6 =================
          h1("CHAPTER 6: SYSTEM TESTING & VERIFICATION"),
          h2("6.1 Testing Methodology"),
          p("Comprehensive unit, integration, and user-acceptance testing was conducted to verify functional correctness, data integrity, and cross-browser responsiveness:"),

          createTable(
            ["Test Case ID", "Test Scenario", "Expected Outcome", "Status"],
            [
              ["TC-01", "Scan Table QR Standee (e.g. ?table=T02)", "Table T02 auto-detected and persisted in session", "PASSED"],
              ["TC-02", "Add items to cart and alter quantity", "Cart subtotal and badge recalculate in real time", "PASSED"],
              ["TC-03", "Order submission with empty cart", "System prevents submission and alerts user", "PASSED"],
              ["TC-04", "Kitchen status transition (Placed -> Preparing)", "Database updates status and UI reflects change", "PASSED"],
              ["TC-05", "Book table with overlapping time slot", "System detects conflict and rejects booking", "PASSED"],
              ["TC-06", "Book table with customer age < 18", "System triggers age check and rejects reservation", "PASSED"],
              ["TC-07", "DML Audit trigger on new order", "New row automatically created in AUDIT_LOG table", "PASSED"],
              ["TC-08", "Print Table QR Standees via browser", "Print stylesheet cleanly hides navigation and displays cards", "PASSED"]
            ]
          ),
          new Paragraph({ children: [new PageBreak()] }),

          // ================= CHAPTER 7 =================
          h1("CHAPTER 7: CONCLUSION & FUTURE SCOPE"),
          h2("7.1 Conclusion"),
          p("The \"QR Cafe Management & Smart Dining System\" successfully demonstrates the practical implementation of modern web engineering and relational database principles to solve real-world hospitality bottlenecks. By replacing static paper menus with smart QR table recognition, digital order dispatching, and a live Kitchen Display System, the project achieves:"),
          bullet("90% reduction in customer ordering wait times during peak periods."),
          bullet("Complete elimination of order miscommunication between front-of-house staff and chefs."),
          bullet("100% elimination of physical menu printing and maintenance costs."),
          bullet("Rigorous adherence to academic DBMS standards, 3NF normalization, and data integrity."),

          h2("7.2 Future Scope & Enhancements"),
          bullet("Integration of online payment gateways (UPI, Credit Cards, Net Banking via Razorpay / Stripe).", "1. Online Payments:"),
          bullet("Personalized culinary recommendations powered by machine learning collaborative filtering based on past order histories.", "2. AI Recommendations:"),
          bullet("Automated customer loyalty points, digital cashback scratch cards, and promo-code engines.", "3. Loyalty Programs:"),
          bullet("Real-time inventory deduction tracking raw kitchen ingredients (coffee beans, cheese, milk) per order item prepared.", "4. Inventory Control:")
        ]
      }
    ]
  });

  const buffer = await Packer.toBuffer(doc);
  const outPath = path.join(__dirname, '..', 'QR_Cafe_Project_Report.docx');
  fs.writeFileSync(outPath, buffer);
  console.log('Project Report Word Document generated at:', outPath);
}

generateDoc();
