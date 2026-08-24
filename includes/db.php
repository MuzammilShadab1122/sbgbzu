<?php
// includes/db.php

$db_host = getenv('MYSQLHOST') ?: ($_ENV['MYSQLHOST'] ?? 'localhost');
$db_port = getenv('MYSQLPORT') ?: ($_ENV['MYSQLPORT'] ?? '3306');
$db_user = getenv('MYSQLUSER') ?: ($_ENV['MYSQLUSER'] ?? 'root');
$db_pass = getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : ($_ENV['MYSQLPASSWORD'] ?? '');
$db_name = getenv('MYSQLDATABASE') ?: ($_ENV['MYSQLDATABASE'] ?? 'sbg_portfolio');

try {
    // Try to connect directly to the database first (standard for cloud platforms like Railway where DB is pre-created)
    try {
        $db = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // If direct connection fails (e.g. database does not exist locally), connect to host and create it
        $pdo = new PDO("mysql:host=$db_host;port=$db_port", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Reconnect to the newly created database
        $db = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
    
    // Check if badges table exists, if not, create and seed it
    $badgesTableExists = $db->query("SHOW TABLES LIKE 'badges'")->rowCount() > 0;
    if (!$badgesTableExists) {
        $db->exec("CREATE TABLE IF NOT EXISTS `badges` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `image` VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB;");
        
        $db->exec("INSERT INTO `badges` (`name`, `image`) VALUES ('Silver Tier Community', 'public/images/badges/silver_tier.svg')");
    }

    // Check if partners table exists, if not, create and seed it
    $partnersTableExists = $db->query("SHOW TABLES LIKE 'partners'")->rowCount() > 0;
    if (!$partnersTableExists) {
        $db->exec("CREATE TABLE IF NOT EXISTS `partners` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `logo` VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB;");
        
        $db->exec("INSERT INTO `partners` (`name`, `logo`) VALUES 
            ('GoClouds', 'public/images/partners/gocloud.png'),
            ('BitOps Technologies', 'public/images/partners/bitsops.jpeg'),
            ('Pie & Ai', 'public/images/partners/pie-ai.png'),
            ('Bahauddin Zakariya University', 'public/images/partners/bzu.png')");
    }

    // Check if swags table exists, if not, create and seed default AWS Swags
    $swagsTableExists = $db->query("SHOW TABLES LIKE 'swags'")->rowCount() > 0;
    if (!$swagsTableExists) {
        $db->exec("CREATE TABLE IF NOT EXISTS `swags` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `points` DECIMAL(12,4) NOT NULL DEFAULT 0.0000,
            `image` VARCHAR(255) NOT NULL,
            `description` TEXT,
            `stock` INT DEFAULT 10
        ) ENGINE=InnoDB;");
        
        $db->exec("INSERT INTO `swags` (`name`, `points`, `image`, `description`, `stock`) VALUES 
            ('AWS Builder T-Shirt', 50.0000, 'public/images/swags/tshirt.svg', 'Official AWS Student Builder Group branded premium cotton t-shirt.', 10),
            ('AWS Stainless Water Bottle', 40.0000, 'public/images/swags/bottle.svg', 'Insulated AWS matte black metal water flask.', 15),
            ('AWS Laptop Sleeve', 35.0000, 'public/images/swags/sleeve.svg', 'Neoprene protective laptop sleeve with AWS Cloud logo.', 8),
            ('AWS Cloud Ceramic Mug', 25.0000, 'public/images/swags/mug.svg', 'Premium ceramic coffee mug for late night cloud building sessions.', 20),
            ('AWS Stickers & Badges Pack', 15.0000, 'public/images/swags/stickers_pack.svg', 'High quality vinyl AWS cloud service & architecture stickers pack.', 50)");
    }

    // Check if swag_requests table exists, if not, create it
    $swagRequestsTableExists = $db->query("SHOW TABLES LIKE 'swag_requests'")->rowCount() > 0;
    if (!$swagRequestsTableExists) {
        $db->exec("CREATE TABLE IF NOT EXISTS `swag_requests` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `member_id` INT NOT NULL,
            `swag_id` INT NOT NULL,
            `points_spent` DECIMAL(12,4) NOT NULL DEFAULT 0.0000,
            `status` VARCHAR(50) DEFAULT 'pending',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;");
    }

    // Dynamic column migrations for existing databases
    try {
        $colCheck = $db->query("SHOW COLUMNS FROM `members` LIKE 'member_code'")->rowCount();
        if ($colCheck === 0) {
            $db->exec("ALTER TABLE `members` ADD COLUMN `member_code` VARCHAR(100) UNIQUE NULL AFTER `id`");
        }
        // Auto-assign member_code for any member where member_code is NULL or empty
        $nullCodes = $db->query("SELECT id FROM `members` WHERE `member_code` IS NULL OR `member_code` = ''")->fetchAll();
        if (!empty($nullCodes)) {
            $upd = $db->prepare("UPDATE `members` SET `member_code` = ? WHERE `id` = ?");
            foreach ($nullCodes as $nc) {
                $upd->execute(['SBG-' . sprintf('%03d', $nc['id']), $nc['id']]);
            }
        }
    } catch (Exception $e) {}

    try {
        $colCheck = $db->query("SHOW COLUMNS FROM `members` LIKE 'password'")->rowCount();
        if ($colCheck === 0) {
            $db->exec("ALTER TABLE `members` ADD COLUMN `password` VARCHAR(255) NULL AFTER `image`");
        }
    } catch (Exception $e) {}

    // Check if tables exist, otherwise construct schema and import initial seeds
    $tableExists = $db->query("SHOW TABLES LIKE 'members'")->rowCount() > 0;
    
    if (!$tableExists) {
        // Users Table (Admin authentication credentials)
        $db->exec("CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `email` VARCHAR(255) UNIQUE NOT NULL,
            `password` VARCHAR(255) NOT NULL,
            `role` VARCHAR(50) DEFAULT 'admin'
        ) ENGINE=InnoDB;");
        
        // Members Table (Directory database with authentication)
        $db->exec("CREATE TABLE IF NOT EXISTS `members` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `member_code` VARCHAR(100) UNIQUE NULL,
            `name` VARCHAR(255) NOT NULL,
            `role` VARCHAR(255) NOT NULL,
            `team` VARCHAR(100) NOT NULL,
            `level` VARCHAR(100) NOT NULL,
            `points` DECIMAL(12,4) DEFAULT 0.0000,
            `campus` VARCHAR(255) DEFAULT 'BZU',
            `responsibilities` TEXT,
            `image` VARCHAR(255),
            `password` VARCHAR(255) DEFAULT NULL
        ) ENGINE=InnoDB;");
        
        // Events Table (Workshops, meetups, galleries)
        $db->exec("CREATE TABLE IF NOT EXISTS `events` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `type` VARCHAR(50) NOT NULL,
            `date` VARCHAR(100) NOT NULL,
            `location` VARCHAR(255) NOT NULL,
            `description` TEXT,
            `image` VARCHAR(255),
            `link` VARCHAR(255),
            `gallery` TEXT
        ) ENGINE=InnoDB;");
        
        // Posts Table (Bulletin/Notices database)
        $db->exec("CREATE TABLE IF NOT EXISTS `posts` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `category` VARCHAR(100) NOT NULL,
            `date` VARCHAR(100) NOT NULL,
            `excerpt` TEXT
        ) ENGINE=InnoDB;");
        
        // Highlights Table (Star member & weekly grinders indices)
        $db->exec("CREATE TABLE IF NOT EXISTS `highlights` (
            `id` INT PRIMARY KEY,
            `month_label` VARCHAR(100) NOT NULL,
            `star_of_month_id` INT DEFAULT NULL,
            `monthly_grinders` TEXT
        ) ENGINE=InnoDB;");
        
        // Seed default Admin User (admin@sbg.bzu / awsbzu2026)
        $adminEmail = 'admin@sbg.bzu';
        $adminPassHash = password_hash('awsbzu2026', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO `users` (`email`, `password`, `role`) VALUES (?, ?, 'admin')");
        $stmt->execute([$adminEmail, $adminPassHash]);
        
        // Import seed data from includes/data.php
        $seedDataPath = __DIR__ . '/data.php';
        if (file_exists($seedDataPath)) {
            require $seedDataPath;
            
            // Seed members
            if (isset($participants) && is_array($participants)) {
                $stmt = $db->prepare("INSERT INTO `members` (`id`, `member_code`, `name`, `role`, `team`, `level`, `points`, `campus`, `responsibilities`, `image`, `password`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                foreach ($participants as $p) {
                    $code = $p['member_code'] ?? ('SBG-' . sprintf('%03d', $p['id']));
                    $pass = !empty($p['password']) ? password_hash($p['password'], PASSWORD_DEFAULT) : password_hash('awsbzu' . $p['id'], PASSWORD_DEFAULT);
                    $stmt->execute([
                        $p['id'],
                        $code,
                        $p['name'],
                        $p['role'],
                        $p['team'],
                        $p['level'],
                        $p['points'],
                        $p['campus'],
                        $p['responsibilities'],
                        $p['image'],
                        $pass
                    ]);
                }
            }
            
            // Seed events
            if (isset($events) && is_array($events)) {
                $stmt = $db->prepare("INSERT INTO `events` (`id`, `title`, `type`, `date`, `location`, `description`, `image`, `link`, `gallery`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                foreach ($events as $e) {
                    $galleryJson = isset($e['gallery']) && is_array($e['gallery']) ? json_encode($e['gallery']) : null;
                    $stmt->execute([
                        $e['id'],
                        $e['title'],
                        $e['type'],
                        $e['date'],
                        $e['location'],
                        $e['description'],
                        $e['image'] ?? null,
                        $e['link'] ?? null,
                        $galleryJson
                    ]);
                }
            }
            
            // Seed posts (Notices)
            if (isset($posts) && is_array($posts)) {
                $stmt = $db->prepare("INSERT INTO `posts` (`id`, `title`, `category`, `date`, `excerpt`) VALUES (?, ?, ?, ?, ?)");
                foreach ($posts as $post) {
                    $stmt->execute([
                        $post['id'],
                        $post['title'],
                        $post['category'],
                        $post['date'],
                        $post['excerpt']
                    ]);
                }
            }
            
            // Seed highlights
            if (isset($highlights) && is_array($highlights)) {
                $grindersJson = isset($highlights['monthlyGrinders']) && is_array($highlights['monthlyGrinders']) ? json_encode($highlights['monthlyGrinders']) : null;
                $stmt = $db->prepare("INSERT INTO `highlights` (`id`, `month_label`, `star_of_month_id`, `monthly_grinders`) VALUES (1, ?, ?, ?)");
                $stmt->execute([
                    $highlights['monthLabel'],
                    $highlights['starOfMonthId'],
                    $grindersJson
                ]);
            }
        }
    }

    // Migration: Ensure member_code and password columns exist on members table
    $cols = $db->query("SHOW COLUMNS FROM `members` LIKE 'member_code'")->fetchAll();
    if (count($cols) === 0) {
        $db->exec("ALTER TABLE `members` ADD COLUMN `member_code` VARCHAR(100) UNIQUE NULL AFTER `id`");
    }
    // Auto-assign member_code for any member where member_code is NULL or empty
    $nullCodes = $db->query("SELECT id FROM `members` WHERE `member_code` IS NULL OR `member_code` = ''")->fetchAll();
    if (!empty($nullCodes)) {
        $upd = $db->prepare("UPDATE `members` SET `member_code` = ? WHERE `id` = ?");
        foreach ($nullCodes as $nc) {
            $upd->execute(['SBG-' . sprintf('%03d', $nc['id']), $nc['id']]);
        }
    }

    $colsPass = $db->query("SHOW COLUMNS FROM `members` LIKE 'password'")->fetchAll();
    if (count($colsPass) === 0) {
        $db->exec("ALTER TABLE `members` ADD COLUMN `password` VARCHAR(255) DEFAULT NULL AFTER `image`");
    }

    // Migration: Standardize all member levels to the strict 5 allowed levels ('Core Team', 'Directorate', 'Manager', 'Lead', 'Member')
    try {
        $db->exec("UPDATE `members` SET `level` = 'Core Team' WHERE `level` = 'Core'");
        $db->exec("UPDATE `members` SET `level` = 'Member' WHERE `level` IN ('Builder', 'Developer') OR `level` IS NULL OR `level` = ''");
    } catch (Exception $e) {}

} catch (PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}

// Global data variables populated dynamically from database tables
try {
    // 1. Fetch all members sorted by points descending
    $stmt = $db->query("SELECT * FROM `members` ORDER BY `points` DESC");
    $participants = $stmt->fetchAll();
    
    // 1.5 Fetch all community badges
    $stmt = $db->query("SELECT * FROM `badges` ORDER BY `id` ASC");
    $badges = $stmt->fetchAll();
    
    // 1.8 Fetch all campus partners
    $stmt = $db->query("SELECT * FROM `partners` ORDER BY `id` ASC");
    $partners = $stmt->fetchAll();
    
    // 2. Fetch all events sorted by ID descending
    $stmt = $db->query("SELECT * FROM `events` ORDER BY `id` DESC");
    $raw_events = $stmt->fetchAll();
    $events = [];
    foreach ($raw_events as $e) {
        $e['gallery'] = !empty($e['gallery']) ? json_decode($e['gallery'], true) : [];
        $events[] = $e;
    }
    
    // 3. Fetch all posts (notices) sorted by ID descending
    $stmt = $db->query("SELECT * FROM `posts` ORDER BY `id` DESC");
    $posts = $stmt->fetchAll();
    
    // 4. Fetch highlights
    $stmt = $db->query("SELECT * FROM `highlights` WHERE `id` = 1");
    $raw_hl = $stmt->fetch();
    if ($raw_hl) {
        $highlights = [
            'monthLabel' => $raw_hl['month_label'],
            'starOfMonthId' => $raw_hl['star_of_month_id'],
            'monthlyGrinders' => !empty($raw_hl['monthly_grinders']) ? json_decode($raw_hl['monthly_grinders'], true) : []
        ];
    } else {
        $highlights = [
            'monthLabel' => 'No Highlights',
            'starOfMonthId' => null,
            'monthlyGrinders' => []
        ];
    }
    
    // 1.9 Fetch all swags
    $stmt = $db->query("SELECT * FROM `swags` ORDER BY `id` ASC");
    $swags = $stmt->fetchAll();

    // 1.95 Fetch all swag requests with member and swag details
    $stmt = $db->query("SELECT r.*, m.name AS member_name, m.member_code AS member_code, m.role AS member_role, m.points AS member_current_points, m.image AS member_image, s.name AS swag_name, s.image AS swag_image, s.points AS swag_required_points 
                        FROM `swag_requests` r 
                        JOIN `members` m ON r.member_id = m.id 
                        JOIN `swags` s ON r.swag_id = s.id 
                        ORDER BY r.id DESC");
    $swag_requests = $stmt->fetchAll();
} catch (Exception $e) {
    $participants = [];
    $events = [];
    $posts = [];
    $highlights = ['monthLabel' => 'Offline', 'starOfMonthId' => null, 'monthlyGrinders' => []];
    $badges = [];
    $partners = [];
    $swags = [];
    $swag_requests = [];
}

// Meta metadata for club structure
$team_meta = [
    'Core' => ['title' => 'Core Team', 'blurb' => 'Chapter leadership and core coordinators.'],
    'Technical' => ['title' => 'Technical Team', 'blurb' => 'Cloud development, DevOps, and AI engineering builders.'],
    'Media & Design' => ['title' => 'Media & Design Team', 'blurb' => 'Graphic design, UI/UX design, and videography creators.'],
    'Marketing' => ['title' => 'Marketing Team', 'blurb' => 'Social media marketing, promotion, and public relations.'],
    'Events' => ['title' => 'Events Team', 'blurb' => 'Event planning, orchestration, and operational execution.'],
    'Operations' => ['title' => 'Operations Team', 'blurb' => 'Daily operations management, documentation, and coordination.'],
];
$team_order = ['Core', 'Technical', 'Media & Design', 'Marketing', 'Events', 'Operations'];

if (!function_exists('get_team_members')) {
    function get_team_members($team_name, $members) {
        $filtered = [];
        foreach ($members as $m) {
            if ($m['team'] === $team_name) {
                $filtered[] = $m;
            }
        }
        usort($filtered, function($a, $b) {
            return $b['points'] <=> $a['points'];
        });
        return $filtered;
    }
}
