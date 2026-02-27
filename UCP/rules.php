<?php
require_once 'steamauth/userInfo.php';
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serveri reeglid</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/rules.css">
</head>
<body>
    <div class="hero-bg"></div>
    <div class="overlay"></div>

    <div class="container">
        <div class="rules-card">
            <div class="header">
                <div class="header-icon">
                    <i class="fas fa-gavel"></i>
                </div>
                <div class="header-text">
                    <h1>Serveri reeglid</h1>
                    <p>Roleplay serveri üldised reeglid ja käitumisjuhised</p>
                </div>
            </div>

            <div class="rule-section">
                <div class="rule-title">
                    <i class="fas fa-globe"></i>
                    <h2>1. Üldised reeglid</h2>
                </div>
                <ul class="rule-list">
                    <li>Respecti kõiki mängijaid ja administratoreid</li>
                    <li>Keelatud on igasugune diskrimineerimine (rass, sugu, vanus, religioon)</li>
                    <li>Keelatud on reaalse elu probleemide toomine mängu</li>
                    <li>Mängusisest suhtluskeelt on eesti keel (vajadusel inglise keel)</li>
                    <li>Mikrofoni kasutamine on kohustuslik (v.a juhul kui on vältimatud põhjused)</li>
                </ul>
            </div>

            <div class="rule-section">
                <div class="rule-title">
                    <i class="fas fa-mask"></i>
                    <h2>2. Roleplay reeglid</h2>
                </div>
                <ul class="rule-list">
                    <li><strong>Powergaming</strong> - keelatud (teiste mängijate sunnimine oma tahtmist mööda)</li>
                    <li><strong>Metagaming</strong> - keelatud (reaalse elu info kasutamine mängus)</li>
                    <li><strong>Fail RP</strong> - keelatud (ebareaalne käitumine, nt surma ignoreerimine)</li>
                    <li><strong>RDM / VDM</strong> - keelatud (põhjuseta tapmine / autoga tapmine)</li>
                    <li><strong>Combat logging</strong> - keelatud (konflikti ajal mängust lahkumine)</li>
                </ul>
            </div>

            <div class="rule-section">
                <div class="rule-title">
                    <i class="fas fa-heartbeat"></i>
                    <h2>3. New Life Rule (NLR)</h2>
                </div>
                <ul class="rule-list">
                    <li>Pärast surma pead ootama vähemalt 5 minutit enne uuesti tegevusse asumist</li>
                    <li>Sa ei mäleta midagi, mis juhtus enne sinu surma (RP mälu kaotus)</li>
                    <li>Sa ei tohi naasta sinna kohta, kus sa surid, vähemalt 30 minutit</li>
                    <li>Sa ei tohi kohe minna kätte maksma oma tapjale</li>
                </ul>
            </div>

            <div class="rule-section">
                <div class="rule-title">
                    <i class="fas fa-car"></i>
                    <h2>4. Sõidukite reeglid</h2>
                </div>
                <ul class="rule-list">
                    <li>Sõidukiga sõitmine peab olema realistlik (ei sõideta 200 km/h linnas)</li>
                    <li>Õhusõidukite kasutamiseks peab olema korralik RP põhjus</li>
                    <li>Politseil on õigus sind peatada - pead kuuletuma</li>
                    <li>Vargusega kaasneb alati korralik RP situatsioon</li>
                </ul>
            </div>

            <div class="rule-section">
                <div class="rule-title">
                    <i class="fas fa-handcuffs"></i>
                    <h2>5. Kriminaalne RP</h2>
                </div>
                <ul class="rule-list">
                    <li>Panga- ja juveeliröövideks peab olema korralik ettevalmistus</li>
                    <li>Pantvangide võtmisel pead andma neile võimaluse RP-ks</li>
                    <li>Politseiga läbirääkimised on kohustuslikud</li>
                    <li>Gangide tegevus peab olema kooskõlastatud adminidega</li>
                </ul>
            </div>

            <div class="rule-section">
                <div class="rule-title">
                    <i class="fas fa-shield-alt"></i>
                    <h2>6. Politsei ja riigiametite RP</h2>
                </div>
                <ul class="rule-list">
                    <li>Politseinikud peavad täitma oma töökohustusi korrektselt</li>
                    <li>Korruptsioon politseis on lubatud vaid vastava loaga</li>
                    <li>Medicud peavad alati aitama vigastatuid, sõltumata nende taustast</li>
                    <li>Ametnikele valeinfo andmine on keelatud</li>
                </ul>
            </div>

            <div class="rule-note">
                <i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>
                Reeglite rikkumisel on administraatoril �igus määrata karistus vastavalt rikkumise raskusele (hoiatus, kick, ban). Adminite otsused on lõplikud.
            </div>

            <a href="javascript:history.back()" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Tagasi eelmisele lehele
            </a>
        </div>

        <footer>
            <p>All rights reserved © 2026</p>
            <p style="margin-top:0.25rem;">Made by takenncs</p>
        </footer>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>