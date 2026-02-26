<?php
require_once 'steamauth/userInfo.php';

$characters = [];
$charCount = 0;
$maxCharacters = 2;

try {
    error_log("Otsin steamhex: " . $steamhex);
    
    $stmt = $pdo->prepare("SELECT * FROM players WHERE steamhex = ? OR license LIKE ? ORDER BY cid ASC");
    $licenseSearch = "%" . $steamhex . "%";
    $stmt->execute([$steamhex, $licenseSearch]);
    $characters = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $charCount = count($characters);
    
    error_log("Leitud karaktereid: " . $charCount);
    
} catch (PDOException $e) {
    error_log("Viga karakterite laadimisel: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>takenncsui</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <div class="dashboard">
    <aside class="sidebar">
      <div class="logo-area">
        <div class="logo-icon"><i class="fas fa-hammer"></i></div>
        <div class="logo-text">taeknncs<span> UCP</span></div>
      </div>
      <nav class="nav">
        <div class="nav-item active"><i class="fas fa-th-large"></i> Avaleht</div>

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
    <a href="settings.php" target="_blank" style="text-decoration: none;">
  <div class="nav-item"><i class="fas fa-cog"></i> Seaded</div>
  </a>

  <a href="https://github.com/takennncs" target="_blank" style="text-decoration: none;">
    <div class="nav-item">
      <i class="fab fa-github"></i>
      GitHub
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
          <h1>Tere, <small style="color:#4ade80"><?= htmlspecialchars($username) ?></small></h1>
          &nbsp;
          <p>QBCore UCP veebileht on keskne kasutajapaneel, mis võimaldab hallata sinu FiveM serveri tegelasi, kontot ja serveriga seotud funktsioone ühes turvalises ja kasutajasõbralikus keskkonnas.</p>
        </div>
        <div class="header-actions">
          <div>
          </div>
          <div>
          </div>
        </div>
      </header>

      <div class="content">

<div class="recent-table">
  <div class="table-header">
    <span class="table-title"><i class="fas fa-user-friends"></i> Karakterihaldus</span>
    <span class="badge"><?= $charCount ?> / <?= $maxCharacters ?> kasutusel</span>
  </div>

  <div style="padding:1.5rem 2rem; display:grid; grid-template-columns:repeat(2,1fr); gap:1rem;">

    <?php if (!empty($characters)): ?>
        <?php foreach ($characters as $char): ?>
            <?php 
                $charInfo = json_decode($char['charinfo'], true);
                $job = json_decode($char['job'], true);
                $money = json_decode($char['money'], true);
                
                if (empty($charInfo) && isset($char['name'])) {
                    $nameParts = explode(' ', $char['name'], 2);
                    $charInfo = [
                        'firstname' => $nameParts[0] ?? 'Tundmatu',
                        'lastname' => $nameParts[1] ?? '',
                        'birthdate' => '----'
                    ];
                }
                
                $fullName = ($charInfo['firstname'] ?? '') . ' ' . ($charInfo['lastname'] ?? '');
                if (trim($fullName) == '') {
                    $fullName = $char['name'] ?? 'Tundmatu karakter';
                }
                
                $faction = $job['label'] ?? 'Töötu';
                $cash = $money['cash'] ?? 0;
                $bank = $money['bank'] ?? 0;
                
                $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($fullName) . "&background=4f46e5&color=fff&size=48&bold=true";
            ?>
            <div style="
                display:flex;
                align-items:center;
                gap:14px;
                background:#131b28;
                border:1px solid #3e4a5f;
                border-radius:16px;
                padding:1rem;
                cursor:pointer;
                transition: all 0.2s;
                position:relative;
            " onmouseover="this.style.background='#1a2537'" onmouseout="this.style.background='#131b28'">
                
                <div style="position:absolute; top:8px; right:8px;">
                    <input type="checkbox" style="width:16px;height:16px;accent-color:#4f46e5;">
                </div>
                
                <img src="<?= $avatarUrl ?>" alt="Avatar"
                     style="width:48px;height:48px;border-radius:12px;object-fit:cover;">
                <div style="flex:1;">
                    <div style="font-weight:500;color:#e5e7eb;"><?= htmlspecialchars($fullName) ?></div>
                    <div style="font-size:0.85rem;color:#9ca3af;">CID: <?= htmlspecialchars($char['cid'] ?? $char['citizenid']) ?></div>
                    <div style="font-size:0.8rem;color:#6b7280;">Fraktsioon: <?= htmlspecialchars($faction) ?></div>
                    <div style="font-size:0.75rem;color:#4ade80; margin-top:4px;">
                        <i class="fas fa-coins"></i> $<?= number_format($cash) ?> | 
                        <i class="fas fa-building"></i> $<?= number_format($bank) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php for ($i = $charCount; $i < $maxCharacters; $i++): ?>
    <div style="
      display:flex;
      align-items:center;
      gap:14px;
      background:#0f1624;
      border:1px dashed #253141;
      border-radius:16px;
      padding:1rem;
      opacity:0.7;
    ">
      <div style="
        width:48px;
        height:48px;
        border-radius:12px;
        background:#1f2a3f;
        display:flex;
        align-items:center;
        justify-content:center;
      ">
        <svg style="width:24px;height:24px;color:#4b5563;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
      </div>
      <div>
        <div style="font-weight:500;color:#9ca3af;">Karakter puudub</div>
        <div style="font-size:0.8rem;color:#6b7280;">Vaba koht</div>
      </div>
    </div>
    <?php endfor; ?>

  </div>

  <div style="padding:0 2rem 1.5rem 2rem;">
    <div style="border-top:1px solid #253141; margin-bottom:1rem;"></div>
    
    <div style="display:flex; gap:10px;">
    
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