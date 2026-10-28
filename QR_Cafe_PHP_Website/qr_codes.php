<?php
$pageTitle = 'Table QR Standees — QR Cafe';
require_once __DIR__ . '/includes/header.php';

// Detect current host (works on localhost or Wi-Fi IP for mobile phones)
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$baseUrl = $protocol . '://' . $host;

$tables = [
    ['num' => 'Table 1', 'desc' => 'Indoor Cozy Section', 'icon' => '🪑'],
    ['num' => 'Table 2', 'desc' => 'Window View Booth', 'icon' => '🪟'],
    ['num' => 'Table 3', 'desc' => 'Outdoor Garden Seating', 'icon' => '🌿'],
    ['num' => 'Table 4', 'desc' => 'Balcony Terrace Table', 'icon' => '☕'],
    ['num' => 'Table 5', 'desc' => 'Corner Family Booth', 'icon' => '🛋️'],
    ['num' => 'Table 6', 'desc' => 'Executive Meeting Space', 'icon' => '💼']
];
?>

<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h1 style="font-size: 2.2rem; font-weight: 800; color: #0f172a;">Table QR Standees</h1>
    <p style="color: #64748b;">Scan any table's QR code with your phone camera, or click "Simulate Scan" to test in browser.</p>
  </div>
  <button onclick="window.print()" class="btn btn-outline">🖨️ Print Standees</button>
</div>

<!-- QR Standee Cards Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
  <?php foreach ($tables as $t): 
    $targetUrl = $baseUrl . '/?table=' . urlencode($t['num']);
    // Generate QR code via free, standard QR API
    $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=8&data=' . urlencode($targetUrl);
  ?>
    <div class="card" style="text-align: center; border: 2px solid #e2e8f0; border-radius: 14px; padding: 1.75rem 1.25rem; background: #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
      
      <!-- Standee Header -->
      <div style="font-size: 1.6rem; margin-bottom: 0.25rem;">☕</div>
      <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: #64748b; letter-spacing: 1px;">QR CAFE SMART DINING</div>
      <h3 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin: 0.35rem 0;"><?= htmlspecialchars($t['num']) ?></h3>
      <p style="font-size: 0.82rem; color: #64748b; margin-bottom: 1rem;"><?= $t['icon'] ?> <?= htmlspecialchars($t['desc']) ?></p>

      <!-- QR Code Image -->
      <div style="background: #f8fafc; padding: 10px; border-radius: 12px; display: inline-block; border: 1px solid #e2e8f0; margin-bottom: 1rem;">
        <img src="<?= $qrApiUrl ?>" alt="QR Code for <?= htmlspecialchars($t['num']) ?>" style="width: 170px; height: 170px; display: block; border-radius: 6px;">
      </div>

      <div style="font-size: 0.85rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">
        📱 Point Camera to Order
      </div>

      <!-- Action Button -->
      <a href="index.php?table=<?= urlencode($t['num']) ?>" class="btn btn-primary btn-sm" style="width: 100%; border-radius: 8px;">
        ⚡ Simulate Scan (Open Menu)
      </a>

      <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.5rem; word-break: break-all;">
        Target: <?= htmlspecialchars($targetUrl) ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<!-- Info Box -->
<div class="card" style="background: #f0fdf4; border-color: #bbf7d0;">
  <div style="display: flex; gap: 12px; align-items: flex-start;">
    <span style="font-size: 1.5rem;">💡</span>
    <div>
      <h4 style="font-size: 1rem; font-weight: 700; color: #166534; margin-bottom: 0.25rem;">Zero Database Overhead</h4>
      <p style="font-size: 0.88rem; color: #15803d; margin: 0;">
        Each QR code dynamically encodes the table link. When scanned, the website automatically extracts the table number and saves it into the existing <strong>`orders`</strong> table. No extra database tables are needed!
      </p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
