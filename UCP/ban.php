<?php
require_once 'steamauth/userInfo.php';

$userLicenses = [];
$bans = [];
$activeBans = [];
$expiredBans = [];
$banCount = 0;
$activeBanCount = 0;

try {
    $stmt = $pdo->prepare("SELECT license FROM players WHERE steamhex = ? OR license LIKE ?");
    $licenseSearch = "%" . $steamhex . "%";
    $stmt->execute([$steamhex, $licenseSearch]);
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($players as $player) {
        if (!empty($player['license'])) {
            $userLicenses[] = $player['license'];
        }
    }
    
    $userLicenses[] = $steamhex;
    
    $userLicenses = array_unique($userLicenses);
    
    if (!empty($userLicenses)) {
        $placeholders = implode(',', array_fill(0, count($userLicenses), '?'));
        $stmt = $pdo->prepare("SELECT * FROM bans WHERE license IN ($placeholders) ORDER BY 
            CASE 
                WHEN expire = 0 THEN 9999999999 
                ELSE expire 
            END DESC");
        $stmt->execute($userLicenses);
        $bans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $banCount = count($bans);
    }
    
    $currentTime = time();
    foreach ($bans as $ban) {
        if ($ban['expire'] == 0 || $ban['expire'] > $currentTime) {
            $activeBans[] = $ban;
        } else {
            $expiredBans[] = $ban;
        }
    }
    $activeBanCount = count($activeBans);
    
    error_log("Leitud license'd: " . implode(', ', $userLicenses));
    error_log("Leitud bänne: " . $banCount);
    
} catch (PDOException $e) {
    error_log("Viga bännide laadimisel: " . $e->getMessage());
}

function formatBanExpire($expire) {
    if ($expire == 0) {
        return ['text' => 'Igavene', 'color' => '#ef4444'];
    }
    
    $date = date('d.m.Y H:i', $expire);
    $timeLeft = $expire - time();
    
    if ($timeLeft <= 0) {
        return ['text' => 'Aegunud', 'color' => '#6b7280'];
    }
    
    $days = floor($timeLeft / 86400);
    $hours = floor(($timeLeft % 86400) / 3600);
    
    if ($days > 0) {
        return ['text' => $date . ' (' . $days . ' päeva)', 'color' => '#fbbf24'];
    } else {
        return ['text' => $date . ' (' . $hours . ' tundi)', 'color' => '#fbbf24'];
    }
}

function getBanIcon($reason) {
    $reason = strtolower($reason);
    if (strpos($reason, 'cheat') !== false || strpos($reason, 'hack') !== false) {
        return 'fa-skull-crossbones';
    } elseif (strpos($reason, 'toxic') !== false || strpos($reason, 'insult') !== false) {
        return 'fa-face-angry';
    } elseif (strpos($reason, 'advert') !== false || strpos($reason, 'reklam') !== false) {
        return 'fa-bullhorn';
    } elseif (strpos($reason, 'exploit') !== false) {
        return 'fa-bug';
    } else {
        return 'fa-gavel';
    }
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
  <title>takenncsui - Keelustused</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/ban.css">
</head>

<body>
  <div class="dashboard">
    <aside class="sidebar">
      <div class="logo-area">
        <div class="logo-icon"><i class="fas fa-hammer"></i></div>
        <div class="logo-text">takenncs<span> UCP</span></div>
      </div>
      <nav class="nav">
         <?php include 'addon/sidebar.php'; ?>
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
          <h1>Keelustused</h1>
          <p>Siin näed kõiki oma keelustusi ja nende olekut</p>
        </div>
      </header>

      <div class="content">
        <div class="recent-table">
          <div class="table-header">
            <span class="table-title"><i class="fas fa-ban"></i> Keelustuste ajalugu</span>
            <span class="badge <?= $activeBanCount > 0 ? 'badge-red' : '' ?>"><?= $activeBanCount ?> aktiivset</span>
          </div>

          <?php if ($activeBanCount > 0): ?>
            <div class="warning-box">
              <i class="fas fa-exclamation-triangle"></i>
              <p>Sul on <?= $activeBanCount ?> aktiivne<?= $activeBanCount > 1 ? 't' : '' ?> keelustus<?= $activeBanCount > 1 ? 't' : '' ?>! Serverisse sisenemine on piiratud kuni keelustuse lõppemiseni.</p>
            </div>
          <?php endif; ?>

          <div class="stats-bar">
            <div class="stat-item">
              <div class="stat-value <?= $banCount > 0 ? 'red' : '' ?>"><?= $banCount ?></div>
              <div class="stat-label">Keelustusi kokku</div>
            </div>
            <div class="stat-item">
              <div class="stat-value <?= $activeBanCount > 0 ? 'red' : '' ?>"><?= $activeBanCount ?></div>
              <div class="stat-label">Aktiivsed</div>
            </div>
            <div class="stat-item">
              <div class="stat-value"><?= count($expiredBans) ?></div>
              <div class="stat-label">Aegunud</div>
            </div>
          </div>

          <?php if (!empty($bans)): ?>
            <div class="bans-container">
              <?php foreach ($bans as $ban): ?>
                <?php 
                  $expireInfo = formatBanExpire($ban['expire']);
                  $isActive = ($ban['expire'] == 0 || $ban['expire'] > time());
                  $cardClass = $isActive ? 'active' : 'expired';
                  $banIcon = getBanIcon($ban['reason']);
                ?>
                <div class="ban-card <?= $cardClass ?>">
                  <div class="ban-header">
                    <div class="ban-icon">
                      <i class="fas <?= $banIcon ?>"></i>
                    </div>
                    <div class="ban-title">
                      <div class="ban-reason">
                        <?= htmlspecialchars($ban['reason']) ?>
                        <?php if ($isActive): ?>
                          <span class="status-badge" style="background: #ef444420; color: #ef4444;">
                            <i class="fas fa-ban"></i> Aktiivne
                          </span>
                        <?php else: ?>
                          <span class="status-badge" style="background: #6b728020; color: #6b7280;">
                            <i class="fas fa-clock"></i> Aegunud
                          </span>
                        <?php endif; ?>
                      </div>
                      <div class="ban-meta">
                        <span><i class="fas fa-hammer"></i> <?= htmlspecialchars($ban['bannedby'] ?? 'Teadmata') ?></span>
                        <span><i class="fas fa-tag"></i> <?= htmlspecialchars($ban['license'] ?? 'License puudub') ?></span>
                      </div>
                    </div>
                  </div>

                  <div class="ban-details">
                    <?php if (!empty($ban['discord'])): ?>
                      <div class="detail-item">
                        <div class="detail-label">Discord</div>
                        <div class="detail-value">
                          <i class="fab fa-discord" style="color: #5865F2;"></i>
                          <?= htmlspecialchars($ban['discord']) ?>
                        </div>
                      </div>
                    <?php endif; ?>

                    <?php if (!empty($ban['ip'])): ?>
                      <div class="detail-item">
                        <div class="detail-label">IP Aadress</div>
                        <div class="detail-value">
                          <i class="fas fa-network-wired"></i>
                          <?= htmlspecialchars($ban['ip']) ?>
                        </div>
                      </div>
                    <?php endif; ?>

                    <div class="detail-item">
                      <div class="detail-label">Lõppemise aeg</div>
                      <div class="detail-value">
                        <i class="fas fa-clock" style="color: <?= $expireInfo['color'] ?>"></i>
                        <span style="color: <?= $expireInfo['color'] ?>"><?= $expireInfo['text'] ?></span>
                      </div>
                    </div>

                    <?php if ($ban['expire'] > 0 && $isActive): ?>
                      <div class="detail-item">
                        <div class="detail-label">Aega jäänud</div>
                        <div class="detail-value">
                          <div style="width: 100%;">
                            <?php 
                              $totalTime = $ban['expire'] - (isset($ban['banned_at']) ? strtotime($ban['banned_at']) : (time() - 86400));
                              $timePassed = time() - (isset($ban['banned_at']) ? strtotime($ban['banned_at']) : (time() - 86400));
                              $progress = ($timePassed / $totalTime) * 100;
                              if ($progress > 100) $progress = 100;
                            ?>
                            <div class="progress-bar" style="margin-top: 0;">
                              <div class="progress-fill" style="width: <?= $progress ?>%; background: #4f46e5;"></div>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-top: 4px; font-size: 0.7rem;">
                              <span style="color: #9ca3af;">Algus</span>
                              <span style="color: #4ade80;"><?= round(100 - $progress) ?>%</span>
                              <span style="color: #9ca3af;">Lõpp</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php endif; ?>
                  </div>

                  <?php if (isset($ban['banned_at'])): ?>
                    <div style="margin-top: 0.5rem; font-size: 0.75rem; color: #6b7280; text-align: right;">
                      <i class="fas fa-calendar"></i> Keelustatud: <?= date('d.m.Y H:i', strtotime($ban['banned_at'])) ?>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="empty-state">
              <i class="fas fa-check-circle"></i>
              <h3>Pole ühtegi keelustust</h3>
              <p>Sul pole ühtegi keelustust - oled hea käitumisega mängija! 🎉</p>
            </div>
          <?php endif; ?>

          <div style="padding:1.5rem 2rem;">
            <div style="border-top:1px solid #253141; padding-top:1rem; text-align:center; color:#6b7280; font-size:0.85rem;">
              <i class="fas fa-info-circle"></i> Keelustuste aegumisel saad automaatselt serverisse siseneda
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