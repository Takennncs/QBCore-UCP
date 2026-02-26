<?php
require_once 'steamauth/userInfo.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['update_profile'])) {
            $displayName = trim($_POST['display_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $discord = trim($_POST['discord'] ?? '');
            
            if (!empty($displayName)) {
                $stmt = $pdo->prepare("UPDATE ucp_users SET name = ?, email = ?, discord_id = ? WHERE id = ?");
                $stmt->execute([$displayName, $email, $discord, $_SESSION['user_id']]);
                
                $_SESSION['username'] = $displayName;
                $username = $displayName;
                
                $message = 'Profiil uuendatud edukalt!';
                $messageType = 'success';
            }
        }
        
        if (isset($_POST['change_password'])) {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if ($newPassword !== $confirmPassword) {
                $message = 'Uued paroolid ei kattu!';
                $messageType = 'error';
            } elseif (strlen($newPassword) < 6) {
                $message = 'Parool peab olema vähemalt 6 tähemärki pikk!';
                $messageType = 'error';
            } else {

                $message = 'Parool edukalt muudetud!';
                $messageType = 'success';
            }
        }
        
        if (isset($_POST['toggle_2fa'])) {
            $enabled = isset($_POST['2fa_enabled']) ? 1 : 0;
            $stmt = $pdo->prepare("UPDATE ucp_users SET twofactor_enabled = ? WHERE id = ?");
            $stmt->execute([$enabled, $_SESSION['user_id']]);
            
            $message = $enabled ? 'Kahefaktoriline autentimine lubatud!' : 'Kahefaktoriline autentimine keelatud!';
            $messageType = 'success';
        }
        
        if (isset($_POST['update_preferences'])) {
            $theme = $_POST['theme'] ?? 'dark';
            $notifications = isset($_POST['notifications']) ? 1 : 0;
            $language = $_POST['language'] ?? 'et';
            
            $preferences = json_encode([
                'theme' => $theme,
                'notifications' => $notifications,
                'language' => $language
            ]);
            
            $stmt = $pdo->prepare("UPDATE ucp_users SET preferences = ? WHERE id = ?");
            $stmt->execute([$preferences, $_SESSION['user_id']]);
            
            $message = 'Eelistused salvestatud!';
            $messageType = 'success';
        }
        
        $stmt = $pdo->prepare("SELECT * FROM ucp_users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $db_user = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        $message = 'Viga andmete salvestamisel: ' . $e->getMessage();
        $messageType = 'error';
    }
}

$preferences = [];
if (!empty($db_user['preferences'])) {
    $preferences = json_decode($db_user['preferences'], true);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>takenncsui - Seaded</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/settings.css">
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
          <div class="nav-item"><i class="fas fa-house"></i> Kinnisvara</div>
        </a>
      </nav>
      
      <div class="bottom-nav nav">
        <a href="settings.php" style="text-decoration: none;">
          <div class="nav-item active"><i class="fas fa-cog"></i> Seaded</div>
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
          <h1>Seaded</h1>
          <p>Halda oma konto seadeid ja eelistusi</p>
        </div>
      </header>

      <div class="content">
        <div class="settings-container">
          
          <?php if ($message): ?>
            <div class="message <?= $messageType ?>">
              <i class="fas <?= $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
              <?= htmlspecialchars($message) ?>
            </div>
          <?php endif; ?>

          <div class="settings-grid">
            <div class="settings-card">
              <div class="card-header">
                <div class="card-icon">
                  <i class="fas fa-user"></i>
                </div>
                <div class="card-title">
                  <h3>Profiili andmed</h3>
                  <p>Muuda oma profiili informatsiooni</p>
                </div>
              </div>
              
              <form method="POST">
                <div class="form-group">
                  <label class="form-label">Steam Hex</label>
                  <input type="text" class="form-input" value="<?= htmlspecialchars($steamhex) ?>" disabled>
                </div>
                
                <div class="form-group">
                  <label class="form-label">Kasutajanimi</label>
                  <input type="text" name="display_name" class="form-input" value="<?= htmlspecialchars($db_user['name'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                  <label class="form-label">E-mail</label>
                  <input type="email" name="email" class="form-input" value="<?= htmlspecialchars($db_user['email'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                  <label class="form-label">Discord ID</label>
                  <input type="text" name="discord" class="form-input" value="<?= htmlspecialchars($db_user['discord_id'] ?? '') ?>" placeholder="123456789012345678">
                </div>
                
                <button type="submit" name="update_profile" class="btn">
                  <i class="fas fa-save"></i> Salvesta muudatused
                </button>
              </form>
            </div>

            <div class="settings-card">
              <div class="card-header">
                <div class="card-icon">
                  <i class="fas fa-info-circle"></i>
                </div>
                <div class="card-title">
                  <h3>Konto info</h3>
                  <p>Sinu konto statistika</p>
                </div>
              </div>
              
              <div class="info-box">
                <div class="info-row">
                  <span class="info-label"><i class="fas fa-calendar"></i> Registreeritud</span>
                  <span class="info-value"><?= isset($db_user['registered_at']) ? date('d.m.Y', strtotime($db_user['registered_at'])) : 'Pole teada' ?></span>
                </div>
                
                <div class="info-row">
                  <span class="info-label"><i class="fas fa-id-card"></i> Roll</span>
                  <span class="info-value">
                    <span class="badge"><?= htmlspecialchars($db_user['role'] ?? 'Kasutaja') ?></span>
                  </span>
                </div>
                
                <div class="info-row">
                  <span class="info-label"><i class="fas fa-coins"></i> Mündid</span>
                  <span class="info-value" style="color: #4ade80;"><?= number_format($db_user['coins'] ?? 0) ?></span>
                </div>
                
                <div class="info-row">
                  <span class="info-label"><i class="fas fa-check-circle"></i> Whitelist</span>
                  <span class="info-value">
                    <?php if ($db_user['whitelisted'] ?? 0): ?>
                      <span style="color: #4ade80;">Lubatud</span>
                    <?php else: ?>
                      <span style="color: #fbbf24;">Ootel</span>
                    <?php endif; ?>
                  </span>
                </div>
              </div>
            </div>

            <div class="settings-card full-width danger-zone">
              <div class="card-header">
                <div class="card-icon">
                  <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="card-title">
                  <h3>Ohutsoon</h3>
                  <p>Need toimingud on pöördumatud</p>
                </div>
              </div>
              
              <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <button class="btn btn-secondary" onclick="if(confirm('Kas oled kindel, et soovid oma seansi lõpetada kõikjal?')) { alert('Funktsioon pole veel implementeeritud'); }">
                  <i class="fas fa-sign-out-alt"></i> Logi välja kõikjal
                </button>
                
                <button class="btn btn-secondary" onclick="if(confirm('Kas oled kindel, et soovid oma konto ajutiselt peatada?')) { alert('Funktsioon pole veel implementeeritud'); }">
                  <i class="fas fa-pause-circle"></i> Peata konto
                </button>
                
                <button class="btn btn-secondary" style="color: #ef4444;" onclick="if(confirm('Kas oled ABSOLUUTSELT kindel? Seda toimingut ei saa tagasi võtta!')) { alert('Funktsioon pole veel implementeeritud'); }">
                  <i class="fas fa-trash-alt"></i> Kustuta konto
                </button>
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