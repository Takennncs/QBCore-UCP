<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['steamid'])) {
    header("Location: index.php");
    exit;
}

$steamid64 = $_SESSION['steamid'];

function steamID64ToSteamHex($steamid64) {
    if (!function_exists('bcsub')) {
        throw new Exception('BCMath extension puudub');
    }

    $accountId = bcsub($steamid64, '76561197960265728');
    $prefix = '1100001';
    $hex = '';

    do {
        $last = bcmod($accountId, '16');
        $hex = dechex($last) . $hex;
        $accountId = bcdiv(bcsub($accountId, $last), '16');
    } while (bccomp($accountId, '0') > 0);

    return 'steam:' . $prefix . $hex;
}

function getSteamProfile($steamid64, $apikey) {
    $url = "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/?key={$apikey}&steamids={$steamid64}";
    $response = @file_get_contents($url);

    if ($response === false) {
        return [
            "personaname" => "Tundmatu kasutaja",
            "avatarfull" => "https://steamcommunity-a.akamaihd.net/public/images/avatars/ee/default_avatar_full.jpg",
            "profileurl" => "#"
        ];
    }

    $data = json_decode($response, true);
    return $data['response']['players'][0] ?? [
        "personaname" => "Tundmatu kasutaja",
        "avatarfull" => "https://steamcommunity-a.akamaihd.net/public/images/avatars/ee/default_avatar_full.jpg",
        "profileurl" => "#"
    ];
}

try {
    $steamhex = steamID64ToSteamHex($steamid64);
    $steamprofile = getSteamProfile($steamid64, $steamauth['apikey']);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM ucp_users WHERE steamhex = ? LIMIT 1");
    $stmt->execute([$steamhex]);
    $db_user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$db_user) {
        $stmt = $pdo->prepare("
            INSERT INTO ucp_users (steamhex, name, role, whitelisted, is_admin, registered_at)
            VALUES (?, ?, 'Kasutaja', 0, 0, NOW())
        ");
        $stmt->execute([$steamhex, $steamprofile['personaname']]);

        $stmt = $pdo->prepare("SELECT * FROM ucp_users WHERE steamhex = ?");
        $stmt->execute([$steamhex]);
        $db_user = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $_SESSION['user_id'] = $db_user['id'];
    $_SESSION['is_admin'] = (int)$db_user['is_admin'];
    $_SESSION['whitelisted'] = (int)$db_user['whitelisted'];

    $username = htmlspecialchars($db_user['name'], ENT_QUOTES, 'UTF-8');
    $user_role = htmlspecialchars($db_user['role'], ENT_QUOTES, 'UTF-8');

} catch (Throwable $e) {
    error_log($e->getMessage());
    die("Serveri viga. Palun proovi hiljem uuesti.");
}