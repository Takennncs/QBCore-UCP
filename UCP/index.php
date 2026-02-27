<!DOCTYPE html>
<html lang="et" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YOUR RP NAME Roleplay - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>

    <div class="hero-bg" style="background-image: url('img/background.jpg');"></div>
    <div class="overlay"></div>

    <div class="content">

        <img 
            src="https://avatars.githubusercontent.com/u/116774376?s=400&u=29e95803ea32e15d2b5a16045305ce962615bad9&v=4g" 
            alt="YOUR RP NAME ROLEPLAY" 
            class="logo"
        >

        <div>
            <h1>YOUR RP NAME ROLEPLAY</h1>
            <p class="subtitle">Lisa oma tekst!</p>
        </div>

        <a href="steamauth/login.php" style="text-decoration: none;">
            <button class="steam-btn">
                <img 
                    src="https://cdn-icons-png.flaticon.com/512/3/3782.png" 
                    alt="Steam logo" 
                    class="steam-logo"
                >
                <span>Logi sisse Steamiga</span>
            </button>
        </a>

    <div class="status-bar">
        <div class="status-online" id="server-status">
            <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
                <circle cx="12" cy="12" r="10"/>
            </svg>
            <span id="server-text">Kontrollib...</span>
        </div>

        <div class="status-divider"></div>

        <div class="server-count">
            Mängijaid: <strong id="player-count">0</strong>
        </div>
    </div>

    <div class="footer-note">
      Created by takenncs
    </div>

<script>
async function fetchServerStatus() {
    try {
        const response = await fetch("addon/server-status.php");
        const data = await response.json();

        const statusText = document.getElementById("server-text");
        const playerCount = document.getElementById("player-count");
        const statusContainer = document.getElementById("server-status");

        if (!data.online) {
            statusText.textContent = "Server Offline";
            playerCount.textContent = "0";
            statusContainer.style.color = "#ff4d4d";
            return;
        }

        statusText.textContent = "Server Online";
        playerCount.textContent = `${data.playerCount} / ${data.maxPlayers}`;
        statusContainer.style.color = "#4CAF50";

    } catch (error) {
        document.getElementById("server-text").textContent = "Server Offline";
        document.getElementById("player-count").textContent = "0";
    }
}

fetchServerStatus();
setInterval(fetchServerStatus, 15000);
</script>
</body>
</html>