# takenncs UCP - FiveM QBCore User Control Panel

A fully functional User Control Panel (UCP) for FiveM QBCore servers. Allows players to manage their characters, vehicles, properties, and account through a web interface.

## 📋 Requirements

- PHP 7.4 or newer
- MySQL 5.7 or newer
- Apache/Nginx web server
- FiveM server with QBCore framework
- Steam API key (https://steamcommunity.com/dev/apikey)

## 🚀 Installation

### 1. Database Setup

Create a new database and import the following SQL:

```sql
CREATE TABLE `ucp_users` (
  `id` int(11) NOT NULL,
  `steamhex` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` varchar(50) DEFAULT 'Kasutaja',
  `whitelisted` tinyint(1) DEFAULT 0,
  `is_admin` tinyint(1) DEFAULT 0,
  `registered_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `players` ADD COLUMN `steamhex` VARCHAR(50) NULL DEFAULT NULL;
```
* In config add your own apikey $steamauth['apikey'] = "YOUR_STEAM_API_KEY"; 

# 📁 File Structure

```
├── index.php                 # Landing page / login
├── dashboard.php             # Main dashboard after login
├── vehicles.php              # Vehicle management
├── ban.php                   # Ban history
├── aparatements.php          # Property management  
├── settings.php              # User settings
├── css/
│   ├── style.css            # Main styles
│   ├── vehicles.css         # Vehicle page styles
│   ├── ban.css              # Ban page styles
│   ├── settings.css         # Settings page styles
│   └── aparatements.css     # Property page styles
├── img/
│   ├── logo.png             # Site logo
│   └── background.jpg       # Login page background
├── steamauth/
│   ├── config.php           # Database & Steam config
│   ├── login.php            # Steam login handler
│   ├── logout.php           # Logout handler
│   ├── OpenID.php           # Steam OpenID class
│   └── userInfo.php         # User session management
└── fxmanifest.lua           # FiveM resource manifest
└── main.lua                 # FiveM server script


```

# User Features
* 🔐 Steam OpenID authentication
* 👥 Character management (view all characters)
* 🚗 Vehicle overview with status (fuel, engine, body)
* 🏠 Property management (apartments, houses)
* ⚖️ Ban history viewer
* ⚙️ Account settings (profile, preferences)
* 💰 View character money (cash & bank)

# 🔄 Automatic SteamHex Updates
The included FiveM resource automatically adds steamhex to your players table when they join:

Updates players table with steamhex on join

Retries if player data isn't loaded yet

Works with both citizenid and license matching

# 👨‍💻 Credits
Created by takenncs
GitHub: @takennncs
Discord: Join our server
