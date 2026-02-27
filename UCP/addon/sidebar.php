<link rel="stylesheet" href="../css/dashboard.css">

<?php 
$current = basename($_SERVER['PHP_SELF']); 
?>

<nav class="nav">

    <a href="dashboard.php" class="nav-link">
        <div class="nav-item <?= ($current == 'dashboard.php') ? 'active' : '' ?>">
            <i class="fas fa-th-large"></i> Avaleht
        </div>
    </a>

    <a href="vehicles.php" class="nav-link">
        <div class="nav-item <?= ($current == 'vehicles.php') ? 'active' : '' ?>">
            <i class="fas fa-car"></i> Sõidukid
        </div>
    </a>

    <a href="ban.php" class="nav-link">
        <div class="nav-item <?= ($current == 'ban.php') ? 'active' : '' ?>">
            <i class="fas fa-ban"></i> Keelustused
        </div>
    </a>

    <a href="apartments.php" class="nav-link">
        <div class="nav-item <?= ($current == 'apartments.php') ? 'active' : '' ?>">
            <i class="fas fa-house"></i> Kinnisvara
        </div>
    </a>

</nav>