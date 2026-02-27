<?php
require_once 'steamauth/userInfo.php';

$citizenIds = [];
$apartments = [];
$apartmentCount = 0;

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
        $stmt = $pdo->prepare("SELECT * FROM apartments WHERE citizenid IN ($placeholders) ORDER BY id DESC");
        $stmt->execute($citizenIds);
        $apartments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $apartmentCount = count($apartments);
    }
    
    error_log("Leitud citizenid'd: " . implode(', ', $citizenIds));
    error_log("Leitud kortereid: " . $apartmentCount);
    
} catch (PDOException $e) {
    error_log("Viga korterite laadimisel: " . $e->getMessage());
}

function getApartmentTypeInfo($type) {
    $types = [
        'apartment1' => ['icon' => 'fa-building', 'name' => 'Korter', 'color' => '#4f46e5'],
        'apartment2' => ['icon' => 'fa-building', 'name' => 'Korter', 'color' => '#4f46e5'],
        'apartment3' => ['icon' => 'fa-building', 'name' => 'Luksuskorter', 'color' => '#4ade80'],
        'house1' => ['icon' => 'fa-home', 'name' => 'Maja', 'color' => '#fbbf24'],
        'house2' => ['icon' => 'fa-home', 'name' => 'Luksusmaja', 'color' => '#4ade80'],
        'motel1' => ['icon' => 'fa-hotel', 'name' => 'Motell', 'color' => '#9ca3af'],
        'motel2' => ['icon' => 'fa-hotel', 'name' => 'Motell', 'color' => '#9ca3af']
    ];
    
    return $types[$type] ?? ['icon' => 'fa-door-open', 'name' => 'Kinnisvara', 'color' => '#6b7280'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>takenncsui - Kinnisvara</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/aparatements.css">
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
          <div class="nav-item"><i class="fas fa-car"></i> Sõidukid</div>
        </a>

        <a href="ban.php" style="text-decoration: none;">
          <div class="nav-item"><i class="fas fa-ban"></i> Keelustused</div>
        </a>
        
        <a href="aparatements.php" style="text-decoration: none;">
          <div class="nav-item active"><i class="fas fa-house"></i> Kinnisvara</div>
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
          <h1>Minu kinnisvara</h1>
          <p>Siin näed kõiki enda kortereid ja kinnisvaraobjekte</p>
        </div>
      </header>

      <div class="content">
        <div class="recent-table">
          <div class="table-header">
            <span class="table-title"><i class="fas fa-house"></i> Kinnisvara nimekiri</span>
            <span class="badge"><?= $apartmentCount ?> objekti</span>
          </div>

          <?php if (!empty($apartments)): ?>
            <div class="stats-bar">
              <?php 
                $apartmentTypes = array_count_values(array_column($apartments, 'type'));
                $totalProperties = $apartmentCount;
              ?>
              <div class="stat-item">
                <div class="stat-value"><?= $totalProperties ?></div>
                <div class="stat-label">Objekte kokku</div>
              </div>
              <div class="stat-item">
                <div class="stat-value"><?= count(array_unique(array_column($apartments, 'citizenid'))) ?></div>
                <div class="stat-label">Karakterite peale</div>
              </div>
            </div>

            <div class="apartments-grid">
              <?php foreach ($apartments as $apartment): ?>
                <?php 
                  $typeInfo = getApartmentTypeInfo($apartment['type']);
                  $citizenShort = substr($apartment['citizenid'], -6);
                ?>
                <div class="apartment-card">
                  <div class="apartment-header">
                    <div class="apartment-icon" style="background: <?= $typeInfo['color'] ?>">
                      <i class="fas <?= $typeInfo['icon'] ?>"></i>
                    </div>
                    <div class="apartment-title">
                      <div class="apartment-name"><?= htmlspecialchars($apartment['label']) ?></div>
                      <div class="apartment-label">
                        <i class="fas fa-tag"></i> <?= htmlspecialchars($apartment['name']) ?>
                      </div>
                    </div>
                  </div>

                  <div class="apartment-details">
                    <div class="detail-item">
                      <div class="detail-label">Tüüp</div>
                      <div class="detail-value">
                        <span class="type-badge" style="background: <?= $typeInfo['color'] ?>20; color: <?= $typeInfo['color'] ?>">
                          <i class="fas <?= $typeInfo['icon'] ?>"></i> <?= $typeInfo['name'] ?>
                        </span>
                      </div>
                    </div>

                    <div class="detail-item">
                      <div class="detail-label">ID</div>
                      <div class="detail-value">
                        <i class="fas fa-hashtag" style="color: #9ca3af;"></i>
                        #<?= $apartment['id'] ?>
                      </div>
                    </div>

                    <div class="detail-item">
                      <div class="detail-label">Asukoht</div>
                      <div class="detail-value">
                        <i class="fas fa-map-marker-alt" style="color: #fbbf24;"></i>
                        <?= htmlspecialchars($apartment['label']) ?>
                      </div>
                    </div>

                    <div class="detail-item">
                      <div class="detail-label">Omanik</div>
                      <div class="detail-value">
                        <i class="fas fa-user" style="color: #4ade80;"></i>
                        <span class="citizenid-tag"><?= $citizenShort ?></span>
                      </div>
                    </div>
                  </div>

                  <div style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid #253141; display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #9ca3af; font-size: 0.8rem;">
                      <i class="fas fa-door-open"></i> Sisenemispunkt: <?= htmlspecialchars($apartment['name']) ?>
                    </span>
                    <span style="color: #4f46e5; font-size: 0.8rem;">
                      <i class="fas fa-key"></i>
                    </span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="empty-state">
              <i class="fas fa-house-circle-exclamation"></i>
              <h3>Pole ühtegi kinnisvaraobjekti</h3>
              <p>Sul pole veel ühtegi korterit või maja registreeritud</p>
            </div>
          <?php endif; ?>

          <div style="padding:1.5rem 2rem;">
            <div style="border-top:1px solid #253141; padding-top:1rem; text-align:center; color:#6b7280; font-size:0.85rem;">
              <i class="fas fa-info-circle"></i> Kinnisvara andmed uuendatakse automaateselt
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