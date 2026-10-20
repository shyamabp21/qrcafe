$reportPath = "C:\Users\Shyam\Desktop\QR_Cafe_Website\QR_Cafe_Project_Report.docx"
Write-Host "Initializing Microsoft Word Automation..." -ForegroundColor Cyan

$word = New-Object -ComObject Word.Application
$word.Visible = $false
$doc = $word.Documents.Add()
$selection = $word.Selection

$wdAlignLeft = 0
$wdAlignCenter = 1
$wdAlignRight = 2
$wdAlignJustify = 3
$wdPageBreak = 7

function Set-Title($text) {
    $selection.ParagraphFormat.Alignment = $wdAlignCenter
    $selection.Font.Name = "Arial"
    $selection.Font.Size = 22
    $selection.Font.Bold = $true
    $selection.Font.Color = 0x182843
    $selection.TypeText($text)
    $selection.TypeParagraph()
    $selection.Font.Bold = $false
}

function Set-Sub($text) {
    $selection.ParagraphFormat.Alignment = $wdAlignCenter
    $selection.Font.Name = "Calibri"
    $selection.Font.Size = 13
    $selection.Font.Color = 0x555555
    $selection.TypeText($text)
    $selection.TypeParagraph()
    $selection.TypeParagraph()
}

function Set-H1($text) {
    $selection.ParagraphFormat.Alignment = $wdAlignLeft
    $selection.Font.Name = "Arial"
    $selection.Font.Size = 16
    $selection.Font.Bold = $true
    $selection.Font.Color = 0x182843
    $selection.ParagraphFormat.SpaceBefore = 14
    $selection.ParagraphFormat.SpaceAfter = 6
    $selection.TypeText($text)
    $selection.TypeParagraph()
    $selection.Font.Bold = $false
}

function Set-H2($text) {
    $selection.ParagraphFormat.Alignment = $wdAlignLeft
    $selection.Font.Name = "Arial"
    $selection.Font.Size = 13
    $selection.Font.Bold = $true
    $selection.Font.Color = 0x2A5899
    $selection.ParagraphFormat.SpaceBefore = 10
    $selection.ParagraphFormat.SpaceAfter = 4
    $selection.TypeText($text)
    $selection.TypeParagraph()
    $selection.Font.Bold = $false
}

function Set-Para($text) {
    $selection.ParagraphFormat.Alignment = $wdAlignJustify
    $selection.Font.Name = "Calibri"
    $selection.Font.Size = 11
    $selection.Font.Bold = $false
    $selection.Font.Color = 0x111111
    $selection.ParagraphFormat.SpaceBefore = 0
    $selection.ParagraphFormat.SpaceAfter = 6
    $selection.ParagraphFormat.LineSpacingRule = 0
    $selection.TypeText($text)
    $selection.TypeParagraph()
}

function Set-Bullet($text) {
    $selection.ParagraphFormat.Alignment = $wdAlignLeft
    $selection.Font.Name = "Calibri"
    $selection.Font.Size = 11
    $selection.Font.Color = 0x222222
    $selection.ParagraphFormat.SpaceBefore = 0
    $selection.ParagraphFormat.SpaceAfter = 3
    $selection.TypeText("  *  " + $text)
    $selection.TypeParagraph()
}

# --- COVER PAGE ---
$selection.TypeParagraph()
$selection.TypeParagraph()
Set-Title('QR CAFE: SMART DINING AND CAFE MANAGEMENT SYSTEM')
Set-Sub('A Modern Web Application and Database Management System')

$selection.TypeParagraph()
$selection.ParagraphFormat.Alignment = $wdAlignCenter
$selection.Font.Name = 'Calibri'
$selection.Font.Size = 12
$selection.Font.Italic = $true
$selection.TypeText("A Project Report Submitted in Partial Fulfillment of the Requirements for the Degree of`nBachelor of Technology in Computer Engineering`nCourse: Database Management Systems (DBMS) and Web Development")
$selection.Font.Italic = $false
$selection.TypeParagraph()
$selection.TypeParagraph()
$selection.TypeParagraph()

$selection.Font.Bold = $true
$selection.Font.Size = 12
$selection.TypeText("Submitted By:`n")
$selection.Font.Bold = $false
$selection.TypeText("Student Name: Shyam Patel`n")
$selection.TypeText("Department of Computer Engineering`nAcademic Year: 2026`n")

$selection.InsertBreak($wdPageBreak)

# --- CERTIFICATE & DECLARATION ---
Set-H1('CERTIFICATE OF APPROVAL')
Set-Para("This is to certify that the project entitled 'QR CAFE: SMART DINING AND CAFE MANAGEMENT SYSTEM' is a bona fide work carried out by Shyam Patel under guidance and supervision in partial fulfillment of the requirements for the curriculum of Database Management Systems and Advanced Web Development.")
$selection.TypeParagraph()
Set-Para('Internal Guide / Supervisor: ________________________          Date: ______________')
Set-Para('Head of Department (CSE):    ________________________          Seal / Stamp: _______')

$selection.TypeParagraph()
Set-H1('DECLARATION')
Set-Para("I hereby declare that the project entitled 'QR CAFE: SMART DINING AND CAFE MANAGEMENT SYSTEM' submitted to the department is my original work. The empirical data, system design, and software source code presented in this report have been implemented in accordance with academic integrity and ethical guidelines.")

$selection.TypeParagraph()
Set-H1('ACKNOWLEDGEMENT')
Set-Para('I express my profound gratitude to my project guide, esteemed professors, and college faculty for their invaluable guidance, constructive suggestions, and continuous encouragement throughout the inception and development of this project. I also extend sincere appreciation to the open-source community for providing modern web frameworks and database tools.')

$selection.InsertBreak($wdPageBreak)

# --- ABSTRACT & TABLE OF CONTENTS ---
Set-H1('ABSTRACT')
Set-Para("In the contemporary hospitality and restaurant sector, traditional physical paper menus and manual order taking often lead to operational delays, ordering errors, inefficient table utilization, and prolonged customer wait times. This project presents 'QR Cafe', a full-stack, contactless smart dining solution designed to digitize and automate the restaurant workflow.")
Set-Para('Customers seated at cafe tables can seamlessly scan an assigned QR code using any smartphone camera to immediately access an interactive digital menu, customize their culinary selections, place real-time food orders, and track order preparation directly from the table. Simultaneously, cafe staff and culinary chefs utilize a real-time Kitchen Display System (KDS) to track incoming orders, while management oversees table reservations, inventory, and sales analytics via an executive administrative dashboard.')
Set-Para('The software is architected with modern HTML5, Advanced CSS3, PHP 8.3, and a relational SQL database engine (SQLite/MySQL via PDO) adhering to rigorous Third Normal Form (3NF) principles. In addition, containerization via Docker and cloud deployment on Render ensures uninterrupted high-availability hosting.')

$selection.TypeParagraph()
Set-H1('TABLE OF CONTENTS')
Set-Bullet('Chapter 1: Introduction and Project Overview')
Set-Bullet('Chapter 2: Literature Survey and Feasibility Analysis')
Set-Bullet('Chapter 3: System Requirements and Architecture')
Set-Bullet('Chapter 4: Database Design and Syllabus Mapping (3NF, ER Model, DDL/DML, Triggers)')
Set-Bullet('Chapter 5: Detailed Technology Stack')
Set-Bullet('Chapter 6: System Modules and User Interface Design')
Set-Bullet('Chapter 7: Testing, Security and Verification')
Set-Bullet('Chapter 8: Conclusion and Future Enhancements')
Set-Bullet('Chapter 9: References and Bibliography')

$selection.InsertBreak($wdPageBreak)

# --- CHAPTER 1 ---
Set-H1('CHAPTER 1: INTRODUCTION')
Set-H2('1.1 Background and Motivation')
Set-Para('The hospitality landscape is undergoing rapid digital transformation. Traditional food ordering workflows necessitate physical waiters visiting tables with laminated paper menus, manually taking notes on paper pads, delivering slips to the kitchen, and manual tallying of bills. This workflow introduces multiple friction points:')
Set-Bullet('High customer wait times during peak dining hours.')
Set-Bullet('Transcription errors between customer preferences and kitchen staff.')
Set-Bullet('Frequent printing costs and hygiene concerns associated with physical paper menus.')
Set-Bullet('Lack of real-time visibility into kitchen preparation status for diners.')

Set-H2('1.2 Problem Statement')
Set-Para('To engineer an intuitive, web-based contactless dining platform that eliminates manual ordering latency, empowers customers to place direct table orders via smartphone QR scanning, provides a live digital kitchen display for chefs, and enforces robust database integrity constraints and relational normalization.')

Set-H2('1.3 Objectives of the Project')
Set-Bullet('Contactless Table Ordering: Enable instant menu browsing and ordering via dynamic table-specific QR URLs.')
Set-Bullet('Interactive Digital Menu: Provide categorization, live keyword search, price tags, and cart management.')
Set-Bullet('Real-Time Kitchen Display System (KDS): Enable culinary staff to update dish preparation stages (Placed -> Preparing -> Served).')
Set-Bullet('Advanced Table Booking: Facilitate online reservations with strict automated 24-hour overlapping booking validation.')
Set-Bullet('Relational Database Adherence: Fulfill all University DBMS curriculum requirements (DDL, DML, Constraints, Joins, Views, Triggers, and Normalization).')

$selection.InsertBreak($wdPageBreak)

# --- CHAPTER 2 ---
Set-H1('CHAPTER 2: SYSTEM ANALYSIS AND FEASIBILITY')
Set-H2('2.1 Existing System vs. Proposed System')
Set-Para('A comparative analysis between traditional cafe operations and the proposed QR Cafe system:')

$compTable = $doc.Tables.Add($selection.Range, 5, 3)
$compTable.Borders.Enable = 1
$compTable.Cell(1,1).Range.Text = "Parameter"
$compTable.Cell(1,2).Range.Text = "Existing Traditional System"
$compTable.Cell(1,3).Range.Text = "Proposed QR Cafe System"
$compTable.Rows.Item(1).Range.Font.Bold = $true

$compTable.Cell(2,1).Range.Text = "Menu Access"
$compTable.Cell(2,2).Range.Text = "Static paper/plastic menus"
$compTable.Cell(2,3).Range.Text = "Dynamic phone QR scan (always updated)"

$compTable.Cell(3,1).Range.Text = "Order Placement"
$compTable.Cell(3,2).Range.Text = "Manual order taking by waiter"
$compTable.Cell(3,3).Range.Text = "Direct digital transmission to kitchen"

$compTable.Cell(4,1).Range.Text = "Order Tracking"
$compTable.Cell(4,2).Range.Text = "Blind waiting / calling waiter"
$compTable.Cell(4,3).Range.Text = "Live 3-stage animated progress bar"

$compTable.Cell(5,1).Range.Text = "Data Storage"
$compTable.Cell(5,2).Range.Text = "Paper receipts or disconnected POS"
$compTable.Cell(5,3).Range.Text = "Centralized Relational SQL DB with Audit Logs"

$selection.Start = $compTable.Range.End
$selection.TypeParagraph()
$selection.TypeParagraph()

Set-H2('2.2 Feasibility Study')
Set-Para('1. Technical Feasibility: The application leverages mature, high-performance web standards (HTML5, Advanced CSS3, PHP 8.3, and SQLite/MySQL). The client executes smoothly in modern mobile browsers (Chrome, Safari, Firefox) without requiring any third-party app installations.')
Set-Para('2. Economic Feasibility: Built entirely on open-source technologies (PHP, Apache, SQLite, Docker, and Render Free Cloud Tier), eliminating licensing costs.')
Set-Para('3. Operational Feasibility: Staff require minimal training to operate the Kitchen KDS and Admin screens, while customers operate the familiar smartphone camera and browser interface.')

$selection.InsertBreak($wdPageBreak)

# --- CHAPTER 3 ---
Set-H1('CHAPTER 3: SYSTEM ARCHITECTURE AND REQUIREMENTS')
Set-H2('3.1 System Architecture')
Set-Para('The application adopts a 3-Tier Client-Server Architecture:')
Set-Bullet('Presentation Layer (Client Tier): Responsive HTML5/CSS3 user interfaces, interactive JavaScript cart management, and dynamic QR scanner.')
Set-Bullet('Application Layer (Server Tier): PHP 8.3 handling business logic, session management, URL table parameter extraction, and route security.')
Set-Bullet('Data Layer (Persistence Tier): Relational SQL Database executing queries via PDO with prepared statements, triggers, and relational views.')

Set-H2('3.2 Hardware and Software Requirements')
Set-Para('Hardware Requirements:')
Set-Bullet('Processor: Intel Core i3 / AMD Ryzen 3 or higher.')
Set-Bullet('RAM: 4 GB Minimum (8 GB Recommended).')
Set-Bullet('Disk Space: 500 MB free storage.')
Set-Bullet('Client Device: Any Android or iOS smartphone with camera and modern browser.')

Set-Para('Software Requirements:')
Set-Bullet('Operating System: Windows 10/11, Linux, or macOS.')
Set-Bullet('Web Server: Apache 2.4 / PHP 8.3 Built-in Server.')
Set-Bullet('Database: SQLite 3 / MySQL 8.0 (MariaDB).')
Set-Bullet('Container Platform: Docker / Dockerfile.')
Set-Bullet('Browser: Google Chrome, Mozilla Firefox, Microsoft Edge, Apple Safari.')

$selection.InsertBreak($wdPageBreak)

# --- CHAPTER 4 ---
Set-H1('CHAPTER 4: DATABASE DESIGN AND DBMS SYLLABUS MAPPING')
Set-Para('The database design specifically maps to academic DBMS curriculum standards, implementing all key concepts: Entity-Relationship modeling, normalization, DDL, DML, constraints, joins, views, triggers, and transaction management.')

Set-H2('4.1 Entity-Relationship (ER) Schema and Normalization')
Set-Para('The schema decomposes cafe operations into 7 well-defined entities to achieve Third Normal Form (3NF) and Boyce-Codd Normal Form (BCNF):')
Set-Bullet('1. CAFE_TABLES: Stores physical cafe table details (table_number, capacity, status).')
Set-Bullet('2. CUSTOMERS: Stores verified customer demographics (customer_name, phone, age, email).')
Set-Bullet('3. BOOKINGS: Relates customers to tables for specified calendar dates and time slots (1:N relationship).')
Set-Bullet('4. MENU_ITEMS: Manages food and beverage catalog with prices and category classifications.')
Set-Bullet('5. CAFE_ORDERS: Header entity representing an order session linked to a table.')
Set-Bullet('6. ORDER_ITEMS: Line item junction table resolving the Many-to-Many (M:N) relationship between Orders and Menu Items.')
Set-Bullet('7. AUDIT_LOG: Immutable logging table populated automatically via database triggers upon DML mutations.')

Set-H2('4.2 Database Tables Description and Integrity Constraints')

$dbTable = $doc.Tables.Add($selection.Range, 8, 4)
$dbTable.Borders.Enable = 1
$dbTable.Cell(1,1).Range.Text = "Table Name"
$dbTable.Cell(1,2).Range.Text = "Primary Key"
$dbTable.Cell(1,3).Range.Text = "Foreign Key(s)"
$dbTable.Cell(1,4).Range.Text = "Key Constraints"
$dbTable.Rows.Item(1).Range.Font.Bold = $true

$dbTable.Cell(2,1).Range.Text = "cafe_tables"
$dbTable.Cell(2,2).Range.Text = "table_id"
$dbTable.Cell(2,3).Range.Text = "None"
$dbTable.Cell(2,4).Range.Text = "table_number UNIQUE, capacity > 0"

$dbTable.Cell(3,1).Range.Text = "customers"
$dbTable.Cell(3,2).Range.Text = "customer_id"
$dbTable.Cell(3,3).Range.Text = "None"
$dbTable.Cell(3,4).Range.Text = "phone UNIQUE, age >= 12"

$dbTable.Cell(4,1).Range.Text = "bookings"
$dbTable.Cell(4,2).Range.Text = "booking_id"
$dbTable.Cell(4,3).Range.Text = "table_id, customer_id"
$dbTable.Cell(4,4).Range.Text = "ON DELETE CASCADE, status check"

$dbTable.Cell(5,1).Range.Text = "menu_items"
$dbTable.Cell(5,2).Range.Text = "item_id"
$dbTable.Cell(5,3).Range.Text = "None"
$dbTable.Cell(5,4).Range.Text = "item_name UNIQUE, price >= 0"

$dbTable.Cell(6,1).Range.Text = "cafe_orders"
$dbTable.Cell(6,2).Range.Text = "order_id"
$dbTable.Cell(6,3).Range.Text = "None"
$dbTable.Cell(6,4).Range.Text = "total_amount >= 0, status check"

$dbTable.Cell(7,1).Range.Text = "order_items"
$dbTable.Cell(7,2).Range.Text = "order_item_id"
$dbTable.Cell(7,3).Range.Text = "order_id, item_id"
$dbTable.Cell(7,4).Range.Text = "quantity > 0, unit_price >= 0"

$dbTable.Cell(8,1).Range.Text = "audit_log"
$dbTable.Cell(8,2).Range.Text = "log_id"
$dbTable.Cell(8,3).Range.Text = "None"
$dbTable.Cell(8,4).Range.Text = "change_date DEFAULT CURRENT_TIMESTAMP"

$selection.Start = $dbTable.Range.End
$selection.TypeParagraph()
$selection.TypeParagraph()

Set-H2('4.3 Advanced SQL Concepts Implemented')
Set-Para('* Relational Joins: Utilized in the Administrative Bookings report to join Customers, Bookings, and Cafe Tables in a single unified view.')
Set-Para('* Database Triggers: Automated audit triggers record every order creation, status modification, and deletion into the audit_log table.')
Set-Para('* Transaction Control (TCL): Orders and line items are enclosed within atomic transactions (beginTransaction / commit / rollBack) to prevent orphaned records.')

$selection.InsertBreak($wdPageBreak)

# --- CHAPTER 5 ---
Set-H1('CHAPTER 5: TECHNOLOGY STACK AND IMPLEMENTATION')
Set-H2('5.1 Frontend Implementation (HTML5 and Advanced CSS3)')
Set-Para('The user interface utilizes modern HTML5 semantics and Advanced CSS3 features:')
Set-Bullet('CSS Variables: Centralized branding tokens for color themes, drop shadows, and border radii.')
Set-Bullet('CSS Grid and Flexbox: Multi-column responsive cards adapting seamlessly to mobile screens.')
Set-Bullet('Glassmorphism: Frosted blur headers (backdrop-filter: blur(12px)) providing an aesthetic coffee-shop ambience.')
Set-Bullet('Print Stylesheets: Tailored @media print CSS to print QR standees directly from the browser.')

Set-H2('5.2 Backend Logic (PHP 8.3 and PDO)')
Set-Bullet('Session Detection: Automatically extracts the "?table=T01" URL parameter upon QR scanning and retains it in PHP session state.')
Set-Bullet('Security via Prepared Statements: Enforces parameterized SQL queries preventing SQL Injection vulnerabilities.')
Set-Bullet('Modular Structure: Codebase segregated into config/ (database connection), assets/ (styles and scripts), includes/ (header and footer), and admin/ (staff portals).')

Set-H2('5.3 Deployment and Containerization (Docker and Render Cloud)')
Set-Para('To enable frictionless deployment, a production Dockerfile was engineered using the official "php:8.3-apache" base image with SQLite and mod_rewrite pre-configured. Render.com continuously deploys the application directly from the connected GitHub repository.')

$selection.InsertBreak($wdPageBreak)

# --- CHAPTER 6 ---
Set-H1('CHAPTER 6: SYSTEM MODULES AND WALKTHROUGH')
Set-H2('6.1 Customer Module')
Set-Bullet('Home Landing Page (index.php): Highlights cafe atmosphere, features, and quick reservation shortcuts.')
Set-Bullet('Digital Menu Catalog (menu.php): Interactive food and drink menu with category filters and real-time search.')
Set-Bullet('Shopping Cart and Checkout (checkout.php): Real-time quantity adjustment, special dietary notes, and instant order placement.')
Set-Bullet('Order Tracker (track_order.php): Visual 3-stage animated progress bar displaying preparation status.')
Set-Bullet('Table Reservation (book.php): Online table booking with automatic time-slot overlap prevention.')

Set-H2('6.2 Staff and Administrative Module')
Set-Bullet('Executive Dashboard (admin/index.php): Live metrics including Total Revenue, Total Orders, and Active Bookings.')
Set-Bullet('Kitchen Display System (admin/kitchen.php): Real-time culinary Kanban board with auto-refresh and status controls.')
Set-Bullet('Order History (admin/orders.php): Comprehensive log of past and pending orders filterable by status.')
Set-Bullet('Menu CRUD Management (admin/menu_manage.php): Add new items, update pricing, and toggle in-stock availability.')
Set-Bullet('Live Database Inspector (admin/db_viewer.php): In-browser database explorer showing raw tables and rows.')

$selection.InsertBreak($wdPageBreak)

# --- CHAPTER 7 ---
Set-H1('CHAPTER 7: TESTING AND SECURITY')
Set-H2('7.1 Functional and Unit Testing')
Set-Bullet('Table Overlap Validation: Verified that two reservations for the same table and overlapping hours are properly rejected.')
Set-Bullet('Cart Math Verification: Tested unit price multiplications, subtotal aggregations, and total calculations across items.')
Set-Bullet('QR URL Parsing: Verified that scanning table QR codes correctly pre-populates table numbers throughout the checkout flow.')

Set-H2('7.2 Security Measures')
Set-Bullet('SQL Injection Prevention: Implemented strict PDO prepared statements with parameter binding for all user inputs.')
Set-Bullet('Cross-Site Scripting (XSS) Mitigation: Enforced htmlspecialchars() escaping on all user-rendered text.')
Set-Bullet('Data Integrity: Foreign Key cascade rules ensure clean deletion without orphan records.')

$selection.InsertBreak($wdPageBreak)

# --- CHAPTER 8 & 9 ---
Set-H1('CHAPTER 8: CONCLUSION AND FUTURE SCOPE')
Set-H2('8.1 Conclusion')
Set-Para("The 'QR Cafe' smart dining and management system successfully addresses the inefficiencies of conventional restaurant operations. By bridging modern web technologies with academic database design principles, the project achieves an intuitive, contactless dining experience while maintaining rock-solid data integrity and 24/7 cloud availability.")

Set-H2('8.2 Future Enhancements')
Set-Bullet('Integration with Payment Gateways: Adding automated UPI, Razorpay, or Stripe payments.')
Set-Bullet('AI Menu Recommendations: Recommending complementary beverage pairings based on customer order history.')
Set-Bullet('Customer Feedback and Loyalty Points: Rewarding frequent diners with loyalty points.')

Set-H1('CHAPTER 9: REFERENCES AND BIBLIOGRAPHY')
Set-Para('1. Silberschatz, A., Korth, H. F., and Sudarshan, S. (2020). Database System Concepts (7th ed.). McGraw-Hill Education.')
Set-Para('2. The PHP Group. (2026). PHP 8.3 Documentation and Manual. Retrieved from https://www.php.net/docs.php')
Set-Para('3. SQLite Development Team. (2026). SQLite SQL Documentation. Retrieved from https://www.sqlite.org/docs.html')
Set-Para('4. Mozilla Developer Network (MDN). (2026). HTML5 and Advanced CSS Reference. Retrieved from https://developer.mozilla.org/')
Set-Para('5. Docker Inc. (2026). Official PHP Docker Hub Images. Retrieved from https://hub.docker.com/_/php')

Write-Host "Saving Document to: $reportPath" -ForegroundColor Green
$doc.SaveAs([ref]$reportPath, [ref]16)
$doc.Close()
$word.Quit()
[System.Runtime.InteropServices.Marshal]::ReleaseComObject($word) | Out-Null
Write-Host "Project Report Generated Successfully!" -ForegroundColor Green
