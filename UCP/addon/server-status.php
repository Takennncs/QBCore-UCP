<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$serverIP = "http://YOUR_SERVER_IP:30120";

function fetchData($url) {
    $context = stream_context_create([
        "http" => [
            "timeout" => 3
        ]
    ]);
    return @file_get_contents($url, false, $context);
}

$info = fetchData($serverIP . "/info.json");
$players = fetchData($serverIP . "/players.json");

if ($info === FALSE || $players === FALSE) {
    echo json_encode([
        "online" => false,
        "playerCount" => 0,
        "maxPlayers" => 0
    ]);
    exit;
}

$infoData = json_decode($info, true);
$playersData = json_decode($players, true);

echo json_encode([
    "online" => true,
    "playerCount" => count($playersData),
    "maxPlayers" => $infoData["vars"]["sv_maxClients"] ?? 64
]);