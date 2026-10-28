<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
      </ul>
    </div>
  </header>

  <main class="main-wrapper">
