<?php
require_once 'steamauth/userInfo.php';

$citizenIds = [];
$vehicles = [];
$vehicleCount = 0;

try {
    $stmt = $pdo->prepare("SELECT citizenid FROM players WHERE steamhex = ? OR license LIKE ?");
    $licenseSearch = "%" . $steamhex . "%";
    $stmt->execute([$steamhex, $licenseSearch]);
    $characters = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($characters as $char) {
        if (!empty($char['citizenid'])) {
            $citizenIds[] = $char['citizenid'];
        }
    }
    
    if (!empty($citizenIds)) {
        $placeholders = implode(',', array_fill(0, count($citizenIds), '?'));
        $stmt = $pdo->prepare("SELECT * FROM player_vehicles WHERE citizenid IN ($placeholders) ORDER BY id DESC");
        $stmt->execute($citizenIds);
        $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $vehicleCount = count($vehicles);
    }
    
} catch (PDOException $e) {
    error_log("Viga sõidukite laadimisel: " . $e->getMessage());
}

function formatVehicleName($vehicle) {
    $name = str_replace('_', ' ', $vehicle);
    $name = ucwords($name);
    return $name;
}

function getStateIcon($state) {
    switch($state) {
        case 1:
            return ['icon' => 'fa-check-circle', 'color' => '#4ade80', 'text' => 'Garaažis'];
        case 0:
            return ['icon' => 'fa-road', 'color' => '#fbbf24', 'text' => 'Väljas'];
        default:
            return ['icon' => 'fa-question-circle', 'color' => '#9ca3af', 'text' => 'Tundmatu'];
    }
}

function getFuelColor($fuel) {
    if ($fuel > 70) return '#4ade80';
    if ($fuel > 30) return '#fbbf24';
    return '#ef4444';
}

if ($_SESSION['whitelisted'] != 1) {
    header("Location: connect.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>takenncsui - Sõidukid</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/vehicles.css">
</head>

<body>
  <div class="dashboard">
    <aside class="sidebar">
      <div class="logo-area">
        <div class="logo-icon"><i class="fas fa-hammer"></i></div>
        <div class="logo-text">taeknncs<span> UCP</span></div>
      </div>
      <nav class="nav">
        <a href="dashboard.php" style="text-decoration: none;">
          <div class="nav-item"><i class="fas fa-th-large"></i> Avaleht</div>
        </a>

        <a href="vehicles.php" style="text-decoration: none;">
          <div class="nav-item active"><i class="fas fa-car"></i> Sõidukid</div>
        </a>

        <a href="ban.php" style="text-decoration: none;">
          <div class="nav-item"><i class="fas fa-ban"></i> Keelustused</div>
        </a>
        
        <a href="aparatements.php" style="text-decoration: none;">
          <div class="nav-item"><i class="fas fa-house"></i> Kinnisvara</div>
        </a>
      </nav>
      
      <div class="bottom-nav nav">
        <a href="settings.php" style="text-decoration: none;">
          <div class="nav-item"><i class="fas fa-cog"></i> Seaded</div>
        </a>

        <a href="https://github.com/takennncs" target="_blank" style="text-decoration: none;">
          <div class="nav-item">
            <i class="fab fa-github"></i> GitHub
          </div>
        </a>

        <a href="steamauth/logout.php" style="text-decoration: none;">
          <div class="nav-item"><i class="fas fa-sign-out-alt"></i> Logi Välja</div>
        </a>
      </div>
    </aside>

    <main class="main">
      <header class="header">
        <div class="page-title">
          <h1>Sõidukid</small></h1>
          <p>Siin näed kõiki enda sõidukeid ja nende hetkeseisundit</p>
        </div>
      </header>

      <div class="content">
        <div class="recent-table">
          <div class="table-header">
            <span class="table-title"><i class="fas fa-car"></i> Sõidukite nimekiri</span>
            <span class="badge"><?= $vehicleCount ?> sõidukit</span>
          </div>

          <?php if (!empty($vehicles)): ?>

            <div class="stats-bar">
              <?php 
                $inGarage = count(array_filter($vehicles, function($v) { return $v['state'] == 1; }));
                $outGarage = count(array_filter($vehicles, function($v) { return $v['state'] == 0; }));
                $totalValue = 0; 
              ?>
              <div class="stat-item">
                <div class="stat-value"><?= $vehicleCount ?></div>
                <div class="stat-label">Sõidukeid kokku</div>
              </div>
              <div class="stat-item">
                <div class="stat-value" style="color: #4ade80;"><?= $inGarage ?></div>
                <div class="stat-label">Garaažis</div>
              </div>
              <div class="stat-item">
                <div class="stat-value" style="color: #fbbf24;"><?= $outGarage ?></div>
                <div class="stat-label">Väljas</div>
              </div>
            </div>

            <div class="vehicle-grid">
              <?php foreach ($vehicles as $vehicle): ?>
                <?php 
                  $state = getStateIcon($vehicle['state']);
                  $mods = json_decode($vehicle['mods'], true);
                  $status = json_decode($vehicle['status'], true);
                  
                  $vehicleName = formatVehicleName($vehicle['vehicle']);
                  
                  $hasFinancing = ($vehicle['paymentsleft'] ?? 0) > 0;
                ?>
                <div class="vehicle-card">
                  <div class="vehicle-header">
                    <div class="vehicle-icon">
                      <i class="fas fa-car"></i>
                    </div>
                    <div class="vehicle-title">
                      <div class="vehicle-name"><?= htmlspecialchars($vehicleName) ?></div>
                      <div class="vehicle-plate">
                        <i class="fas fa-qrcode"></i> <?= htmlspecialchars($vehicle['plate']) ?>
                      </div>
                    </div>
                  </div>

                  <div class="vehicle-details">
                    <div class="detail-item">
                      <div class="detail-label">Seisukord</div>
                      <div class="detail-value">
                        <span class="status-badge" style="background: <?= $state['color'] ?>20; color: <?= $state['color'] ?>">
                          <i class="fas <?= $state['icon'] ?>"></i> <?= $state['text'] ?>
                        </span>
                      </div>
                    </div>

                    <div class="detail-item">
                      <div class="detail-label">Garaaž</div>
                      <div class="detail-value">
                        <i class="fas fa-home" style="color: #9ca3af;"></i>
                        <?= htmlspecialchars($vehicle['garage'] ?? 'Teadmata') ?>
                      </div>
                    </div>

                    <div class="detail-item">
                      <div class="detail-label">Kütus</div>
                      <div class="detail-value">
                        <i class="fas fa-gas-pump" style="color: <?= getFuelColor($vehicle['fuel']) ?>"></i>
                        <?= round($vehicle['fuel']) ?>%
                      </div>
                      <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= $vehicle['fuel'] ?>%; background: <?= getFuelColor($vehicle['fuel']) ?>"></div>
                      </div>
                    </div>

                    <div class="detail-item">
                      <div class="detail-label">Mootor / Kere</div>
                      <div class="detail-value">
                        <i class="fas fa-engine" style="color: #4ade80;"></i> <?= round($vehicle['engine']) ?>%
                      </div>
                      <div class="detail-value" style="margin-top: 4px;">
                        <i class="fas fa-car-crash" style="color: #fbbf24;"></i> <?= round($vehicle['body']) ?>%
                      </div>
                    </div>
                  </div>

                  <?php if ($hasFinancing): ?>
                    <div style="background: #4f46e520; border-radius: 8px; padding: 0.5rem; margin: 0.5rem 0;">
                      <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #9ca3af; font-size: 0.8rem;">
                          <i class="fas fa-credit-card"></i> Järelmaks
                        </span>
                        <span style="color: #4ade80; font-weight: 500;">
                          $<?= number_format($vehicle['paymentamount']) ?>/kuu
                        </span>
                      </div>
                      <div style="margin-top: 4px;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.75rem;">
                          <span style="color: #9ca3af;">Jäänud: <?= $vehicle['paymentsleft'] ?> makset</span>
                          <span style="color: #fbbf24;">$<?= number_format($vehicle['balance']) ?></span>
                        </div>
                        <div class="progress-bar" style="margin-top: 2px;">
                          <?php 
                            $totalPayments = ($vehicle['paymentsleft'] + ($vehicle['balance'] / $vehicle['paymentamount']));
                            $progress = ($vehicle['paymentsleft'] / $totalPayments) * 100;
                          ?>
                          <div class="progress-fill" style="width: <?= 100 - $progress ?>%; background: #4f46e5;"></div>
                        </div>
                      </div>
                    </div>
                  <?php endif; ?>

                  <div class="vehicle-footer">
                    <span>
                      <i class="fas fa-tachometer-alt"></i> 
                      <?= number_format($vehicle['drivingdistance'] ?? 0) ?> km
                    </span>
                    <span>
                      <i class="fas fa-tools"></i> 
                      Depot: $<?= number_format($vehicle['depotprice'] ?? 0) ?>
                    </span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="empty-state">
              <i class="fas fa-car-side"></i>
              <h3>Pole ühtegi sõidukit</h3>
              <p>Sul pole veel ühtegi sõidukit registreeritud</p>
            </div>
          <?php endif; ?>

          <div style="padding:1.5rem 2rem;">
            <div style="border-top:1px solid #253141; padding-top:1rem; text-align:center; color:#6b7280; font-size:0.85rem;">
              <i class="fas fa-info-circle"></i> Sõidukite andmed uuendatakse automaatselt
            </div>
          </div>
        </div>
            </div>

        <div class="footer-meta">
          🌙 Created by takenncs
        </div>
      </div>
    </main>
  </div>
</body>
</html>