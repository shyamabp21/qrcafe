<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auto-detect and remember table from QR code scan (?table=Table 1)
if (isset($_GET['table']) && !empty($_GET['table'])) {
    $_SESSION['table_number'] = htmlspecialchars(trim($_GET['table']));
}
$currentTable = $_SESSION['table_number'] ?? null;
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'QR Cafe — Smart Dining System' ?></title>
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Stylesheet -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <!-- Top Navigation -->
  <header class="navbar">
    <div class="nav-container">
      <a class="brand" href="index.php">
        <span class="brand-icon">☕</span>
        <span>QR Cafe</span>
      </a>

      <ul class="nav-links">
        <li><a href="index.php" class="<?= ($currentPage === 'index.php') ? 'active' : '' ?>">📖 Order Menu</a></li>
        <li><a href="orders.php" class="<?= ($currentPage === 'orders.php') ? 'active' : '' ?>">📋 Orders & Bills</a></li>
        <li><a href="menu.php" class="<?= ($currentPage === 'menu.php') ? 'active' : '' ?>">⚙️ Manage Menu</a></li>
        <li><a href="qr_codes.php" class="<?= ($currentPage === 'qr_codes.php') ? 'active' : '' ?>">📱 Table QRs</a></li>
      </ul>

      <?php if ($currentTable): ?>
        <div style="display:flex;align-items:center;gap:8px;">
          <span style="background:#eff6ff;color:#2563eb;font-weight:700;font-size:0.85rem;padding:4px 12px;border-radius:9999px;border:1px solid #bfdbfe;">
            📍 <?= htmlspecialchars($currentTable) ?>
          </span>
        </div>
      <?php endif; ?>
    </div>
  </header>

  <main class="main-wrapper">
