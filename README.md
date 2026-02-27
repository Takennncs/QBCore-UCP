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

CREATE TABLE `whitelist_answers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_answer` char(1) NOT NULL,
  `is_correct` tinyint(1) NOT NULL,
  `attempt_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `whitelist_answers` (`id`, `user_id`, `question_id`, `selected_answer`, `is_correct`, `attempt_date`) VALUES
(1, 2, 1, 'D', 0, '2026-02-27 17:03:07'),
(2, 2, 2, 'C', 0, '2026-02-27 17:03:07'),
(3, 2, 3, 'C', 1, '2026-02-27 17:03:07'),
(4, 2, 4, 'C', 1, '2026-02-27 17:03:07'),
(5, 2, 5, 'C', 1, '2026-02-27 17:03:07'),
(6, 2, 6, 'C', 0, '2026-02-27 17:03:07'),
(7, 2, 7, 'D', 0, '2026-02-27 17:03:07'),
(8, 2, 8, 'C', 1, '2026-02-27 17:03:07'),
(9, 2, 9, 'D', 0, '2026-02-27 17:03:07'),
(10, 2, 10, 'C', 1, '2026-02-27 17:03:07'),
(11, 2, 1, 'C', 1, '2026-02-27 17:08:40'),
(12, 2, 2, 'B', 1, '2026-02-27 17:08:40'),
(13, 2, 3, 'C', 1, '2026-02-27 17:08:40'),
(14, 2, 4, 'C', 1, '2026-02-27 17:08:40'),
(15, 2, 5, 'C', 1, '2026-02-27 17:08:40'),
(16, 2, 6, 'B', 1, '2026-02-27 17:08:40'),
(17, 2, 7, 'B', 1, '2026-02-27 17:08:40'),
(18, 2, 8, 'C', 1, '2026-02-27 17:08:40'),
(19, 2, 9, 'C', 1, '2026-02-27 17:08:40'),
(20, 2, 10, 'C', 1, '2026-02-27 17:08:40'),
(21, 2, 1, 'C', 1, '2026-02-27 17:10:02'),
(22, 2, 2, 'C', 0, '2026-02-27 17:10:02'),
(23, 2, 3, 'B', 0, '2026-02-27 17:10:02'),
(24, 2, 4, 'B', 0, '2026-02-27 17:10:02'),
(25, 2, 5, 'C', 1, '2026-02-27 17:10:02'),
(26, 2, 6, 'D', 0, '2026-02-27 17:10:02'),
(27, 2, 7, 'A', 0, '2026-02-27 17:10:02'),
(28, 2, 8, 'C', 1, '2026-02-27 17:10:02'),
(29, 2, 9, 'B', 0, '2026-02-27 17:10:02'),
(30, 2, 10, 'D', 0, '2026-02-27 17:10:02');

CREATE TABLE `whitelist_attempts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `attempts` int(11) DEFAULT 0,
  `last_attempt` datetime DEFAULT NULL,
  `cooldown_until` datetime DEFAULT NULL,
  `passed` tinyint(1) DEFAULT 0,
  `passed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `whitelist_questions` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `option_a` text NOT NULL,
  `option_b` text NOT NULL,
  `option_c` text NOT NULL,
  `option_d` text NOT NULL,
  `correct_answer` char(1) NOT NULL COMMENT 'A, B, C või D',
  `explanation` text DEFAULT NULL,
  `order_num` int(11) NOT NULL,
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `whitelist_questions` (`id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `explanation`, `order_num`, `active`) VALUES
(1, 'ADD HERE.', 1, 1);
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
