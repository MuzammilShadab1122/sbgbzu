<?php
// quiz.php - Interactive Quiz Generation, Anti-Cheat Exam Portal & Settings Hub
// Fully self-contained. Does not modify or depend on changes to existing files.

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

// Self-healing database table initialization
try {
    $db->exec("CREATE TABLE IF NOT EXISTS `quizzes` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT,
        `is_active` TINYINT(1) DEFAULT 1,
        `award_points` TINYINT(1) DEFAULT 1,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");

    $db->exec("CREATE TABLE IF NOT EXISTS `quiz_questions` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `quiz_id` INT NOT NULL,
        `question_text` TEXT NOT NULL,
        `option_a` TEXT NOT NULL,
        `option_b` TEXT NOT NULL,
        `option_c` TEXT NOT NULL,
        `option_d` TEXT NOT NULL,
        `correct_option` CHAR(1) NOT NULL,
        `points` INT DEFAULT 10,
        `time_limit` INT DEFAULT 10,
        `sort_order` INT DEFAULT 1,
        INDEX (`quiz_id`)
    ) ENGINE=InnoDB;");

    $db->exec("CREATE TABLE IF NOT EXISTS `quiz_attempts` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `quiz_id` INT NOT NULL,
        `member_id` INT NOT NULL,
        `member_name` VARCHAR(255) NOT NULL,
        `total_score` DECIMAL(10,2) DEFAULT 0,
        `max_score` DECIMAL(10,2) DEFAULT 0,
        `speed_bonus` DECIMAL(10,2) DEFAULT 0,
        `correct_count` INT DEFAULT 0,
        `total_questions` INT DEFAULT 0,
        `time_spent_seconds` INT DEFAULT 0,
        `answers_json` TEXT,
        `completed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (`quiz_id`),
        INDEX (`member_id`)
    ) ENGINE=InnoDB;");

    // Optional migration for award_points if column missing in pre-existing table
    try {
        $chk = $db->query("SHOW COLUMNS FROM `quizzes` LIKE 'award_points'")->rowCount();
        if ($chk === 0) {
            $db->exec("ALTER TABLE `quizzes` ADD COLUMN `award_points` TINYINT(1) DEFAULT 1 AFTER `is_active`");
        }
    } catch (Exception $e) {}

} catch (Exception $e) {
    // Graceful fallback if database connection has temporary issues
}

$currentUserRole = $_SESSION['role'] ?? 'guest';
$isAdmin = is_admin();
$isMember = is_member();
$loggedMember = get_logged_member();

$success = '';
$error = '';

// ---------------------------------------------------------
// AJAX / API: Submit Quiz Attempt
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['api_action']) && $_POST['api_action'] === 'submit_attempt') {
    header('Content-Type: application/json');
    if (!$isMember && !$isAdmin) {
        echo json_encode(['success' => false, 'error' => 'You must be logged in as a registered member or admin to submit.']);
        exit;
    }

    $quiz_id = intval($_POST['quiz_id'] ?? 0);
    $answers = json_decode($_POST['answers'] ?? '[]', true);
    $time_spent = intval($_POST['time_spent'] ?? 0);

    if (!$quiz_id || !is_array($answers)) {
        echo json_encode(['success' => false, 'error' => 'Invalid submission data.']);
        exit;
    }

    // Fetch quiz questions
    $stmt = $db->prepare("SELECT * FROM `quiz_questions` WHERE `quiz_id` = ? ORDER BY `sort_order` ASC, `id` ASC");
    $stmt->execute([$quiz_id]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($questions)) {
        echo json_encode(['success' => false, 'error' => 'Quiz questions not found.']);
        exit;
    }

    $qMap = [];
    $maxScore = 0;
    foreach ($questions as $q) {
        $qMap[$q['id']] = $q;
        $maxScore += intval($q['points']);
    }

    $totalScore = 0.0;
    $totalSpeedBonus = 0.0;
    $correctCount = 0;
    $processedAnswers = [];

    foreach ($answers as $ans) {
        $qid = intval($ans['question_id'] ?? 0);
        $selected = strtoupper(trim($ans['selected_option'] ?? ''));
        $remTime = floatval($ans['remaining_seconds'] ?? 0);

        if (!isset($qMap[$qid])) continue;
        $q = $qMap[$qid];
        $isCorrect = ($selected === strtoupper($q['correct_option']));
        $basePoints = intval($q['points']);
        $timeLimit = max(1, intval($q['time_limit']));
        $speedBonus = 0.0;
        $earnedPoints = 0.0;

        if ($isCorrect) {
            $correctCount++;
            // Speed Bonus Formula: Up to 50% extra points for instant answers!
            // Early answering earns more points as requested.
            $ratio = max(0, min(1, $remTime / $timeLimit));
            $speedBonus = round($basePoints * $ratio * 0.5, 2);
            $earnedPoints = round($basePoints + $speedBonus, 2);
            $totalScore += $earnedPoints;
            $totalSpeedBonus += $speedBonus;
        }

        $processedAnswers[] = [
            'question_id' => $qid,
            'question_text' => $q['question_text'],
            'selected' => $selected,
            'correct' => $q['correct_option'],
            'is_correct' => $isCorrect,
            'base_points' => $basePoints,
            'speed_bonus' => $speedBonus,
            'earned_points' => $earnedPoints,
            'remaining_seconds' => $remTime,
            'time_limit' => $timeLimit
        ];
    }

    $member_id = $loggedMember ? $loggedMember['id'] : ($_SESSION['user_id'] ?? 0);
    $member_name = $loggedMember ? $loggedMember['name'] : ($_SESSION['user_name'] ?? 'Authorized Admin');

    // Record attempt in database
    $stmt = $db->prepare("INSERT INTO `quiz_attempts` (`quiz_id`, `member_id`, `member_name`, `total_score`, `max_score`, `speed_bonus`, `correct_count`, `total_questions`, `time_spent_seconds`, `answers_json`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $quiz_id,
        $member_id,
        $member_name,
        $totalScore,
        $maxScore,
        $totalSpeedBonus,
        $correctCount,
        count($questions),
        $time_spent,
        json_encode($processedAnswers)
    ]);
    $attempt_id = $db->lastInsertId();

    // Check if points should be awarded to the student's member points profile
    $qMeta = $db->prepare("SELECT `award_points` FROM `quizzes` WHERE `id` = ?");
    $qMeta->execute([$quiz_id]);
    $quizMeta = $qMeta->fetch();
    $pointsAwarded = false;
    if ($quizMeta && !empty($quizMeta['award_points']) && $loggedMember && $totalScore > 0) {
        $upd = $db->prepare("UPDATE `members` SET `points` = `points` + ? WHERE `id` = ?");
        $upd->execute([$totalScore, $loggedMember['id']]);
        $pointsAwarded = true;
    }

    echo json_encode([
        'success' => true,
        'attempt_id' => $attempt_id,
        'total_score' => $totalScore,
        'max_score' => $maxScore,
        'speed_bonus' => $totalSpeedBonus,
        'correct_count' => $correctCount,
        'total_questions' => count($questions),
        'points_awarded' => $pointsAwarded,
        'results' => $processedAnswers
    ]);
    exit;
}

// ---------------------------------------------------------
// Admin Actions Handling (Create Quiz, Add Questions, Delete, Sample Seed)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin && isset($_POST['action'])) {
    $action = $_POST['action'];

    try {
        if ($action === 'create_quiz') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            $award_points = isset($_POST['award_points']) ? 1 : 0;

            if (empty($title)) {
                $error = "Quiz title cannot be empty.";
            } else {
                $stmt = $db->prepare("INSERT INTO `quizzes` (`title`, `description`, `is_active`, `award_points`) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $description, $is_active, $award_points]);
                $quiz_id = $db->lastInsertId();

                // Process questions
                $q_texts = $_POST['q_text'] ?? [];
                $q_opt_as = $_POST['q_opt_a'] ?? [];
                $q_opt_bs = $_POST['q_opt_b'] ?? [];
                $q_opt_cs = $_POST['q_opt_c'] ?? [];
                $q_opt_ds = $_POST['q_opt_d'] ?? [];
                $q_corrects = $_POST['q_correct'] ?? [];
                $q_points = $_POST['q_points'] ?? [];
                $q_timers = $_POST['q_timer'] ?? [];

                $addedCount = 0;
                if (is_array($q_texts)) {
                    $insertQ = $db->prepare("INSERT INTO `quiz_questions` (`quiz_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `points`, `time_limit`, `sort_order`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    foreach ($q_texts as $idx => $text) {
                        $text = trim($text);
                        if (empty($text)) continue;

                        $opt_a = trim($q_opt_as[$idx] ?? '');
                        $opt_b = trim($q_opt_bs[$idx] ?? '');
                        $opt_c = trim($q_opt_cs[$idx] ?? '');
                        $opt_d = trim($q_opt_ds[$idx] ?? '');
                        $correct = strtoupper(trim($q_corrects[$idx] ?? 'A'));
                        $pts = max(1, intval($q_points[$idx] ?? 10));
                        $timer = max(3, intval($q_timers[$idx] ?? 10)); // Admin configurable timer per question

                        $insertQ->execute([$quiz_id, $text, $opt_a, $opt_b, $opt_c, $opt_d, $correct, $pts, $timer, $addedCount + 1]);
                        $addedCount++;
                    }
                }

                $success = "Quiz '$title' generated successfully with $addedCount question(s)!";
            }

        } elseif ($action === 'delete_quiz') {
            $quiz_id = intval($_POST['quiz_id'] ?? 0);
            $db->prepare("DELETE FROM `quizzes` WHERE `id` = ?")->execute([$quiz_id]);
            $db->prepare("DELETE FROM `quiz_questions` WHERE `quiz_id` = ?")->execute([$quiz_id]);
            $db->prepare("DELETE FROM `quiz_attempts` WHERE `quiz_id` = ?")->execute([$quiz_id]);
            $success = "Quiz and its questions/attempts were deleted.";

        } elseif ($action === 'toggle_quiz') {
            $quiz_id = intval($_POST['quiz_id'] ?? 0);
            $db->prepare("UPDATE `quizzes` SET `is_active` = 1 - `is_active` WHERE `id` = ?")->execute([$quiz_id]);
            $success = "Quiz visibility updated.";

        } elseif ($action === 'seed_sample_quiz') {
            // Seed a high-quality AWS quiz template so admin can test instantly
            $stmt = $db->prepare("INSERT INTO `quizzes` (`title`, `description`, `is_active`, `award_points`) VALUES (?, ?, 1, 1)");
            $stmt->execute(['AWS Cloud Practitioner Challenge', 'Fast-paced AWS quiz. Answer quickly before the timer runs out to gain speed bonuses! Screenshots are disabled.']);
            $sampleId = $db->lastInsertId();

            $samples = [
                [
                    'q' => 'Which AWS service provides on-demand resizable virtual computing capacity in the cloud?',
                    'a' => 'Amazon S3', 'b' => 'Amazon EC2', 'c' => 'AWS Lambda', 'd' => 'Amazon DynamoDB',
                    'correct' => 'B', 'points' => 10, 'timer' => 10
                ],
                [
                    'q' => 'What is the serverless compute service in AWS that runs code in response to events?',
                    'a' => 'Amazon EC2', 'b' => 'Amazon ECS', 'c' => 'AWS Lambda', 'd' => 'AWS Elastic Beanstalk',
                    'correct' => 'C', 'points' => 15, 'timer' => 8
                ],
                [
                    'q' => 'Which cloud architecture principle is demonstrated by distributing workloads across multiple Availability Zones?',
                    'a' => 'High Availability & Fault Tolerance', 'b' => 'Loose Coupling', 'c' => 'Statelessness', 'd' => 'Cost Minimization',
                    'correct' => 'A', 'points' => 20, 'timer' => 12
                ],
                [
                    'q' => 'Which AWS service is designed for object storage with 99.999999999% (11 9s) of durability?',
                    'a' => 'Amazon EBS', 'b' => 'Amazon EFS', 'c' => 'Amazon S3', 'd' => 'AWS Snowball',
                    'correct' => 'C', 'points' => 10, 'timer' => 7
                ],
                [
                    'q' => 'In the AWS Shared Responsibility Model, which responsibility belongs exclusively to AWS?',
                    'a' => 'Customer Data Encryption', 'b' => 'Physical Security of Data Centers', 'c' => 'IAM User Management', 'd' => 'Guest OS Patching',
                    'correct' => 'B', 'points' => 25, 'timer' => 15
                ]
            ];

            $ins = $db->prepare("INSERT INTO `quiz_questions` (`quiz_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `points`, `time_limit`, `sort_order`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($samples as $i => $s) {
                $ins->execute([$sampleId, $s['q'], $s['a'], $s['b'], $s['c'], $s['d'], $s['correct'], $s['points'], $s['timer'], $i + 1]);
            }
            $success = "Sample AWS Challenge Quiz seeded with 5 questions, custom timers, and point structures!";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// ---------------------------------------------------------
// View Router & Data Fetching
// ---------------------------------------------------------
$activeQuizId = intval($_GET['take'] ?? 0);
$viewMode = $_GET['view'] ?? ($isAdmin ? 'admin' : 'student');
if ($activeQuizId > 0) {
    $viewMode = 'take_quiz';
}

// Fetch all quizzes
$stmt = $db->query("SELECT q.*, 
    (SELECT COUNT(*) FROM `quiz_questions` WHERE `quiz_id` = q.id) AS question_count,
    (SELECT COALESCE(SUM(points), 0) FROM `quiz_questions` WHERE `quiz_id` = q.id) AS total_points,
    (SELECT COUNT(*) FROM `quiz_attempts` WHERE `quiz_id` = q.id) AS attempt_count
    FROM `quizzes` q ORDER BY q.id DESC");
$allQuizzes = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

// If student is attempting a quiz, load quiz + questions
$activeQuiz = null;
$activeQuestions = [];
if ($viewMode === 'take_quiz' && $activeQuizId > 0) {
    if (!$isMember && !$isAdmin) {
        header('Location: login.php?msg=quiz_auth_required');
        exit;
    }

    $qStmt = $db->prepare("SELECT * FROM `quizzes` WHERE `id` = ?");
    $qStmt->execute([$activeQuizId]);
    $activeQuiz = $qStmt->fetch(PDO::FETCH_ASSOC);

    if ($activeQuiz) {
        $qqStmt = $db->prepare("SELECT `id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `points`, `time_limit`, `sort_order` FROM `quiz_questions` WHERE `quiz_id` = ? ORDER BY `sort_order` ASC, `id` ASC");
        $qqStmt->execute([$activeQuizId]);
        $activeQuestions = $qqStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Fetch recent attempts for leaderboard
$attemptsStmt = $db->query("SELECT a.*, q.title AS quiz_title FROM `quiz_attempts` a JOIN `quizzes` q ON a.quiz_id = q.id ORDER BY a.total_score DESC, a.time_spent_seconds ASC LIMIT 20");
$topAttempts = $attemptsStmt ? $attemptsStmt->fetchAll(PDO::FETCH_ASSOC) : [];

require_once __DIR__ . '/includes/header.php';
?>

<!-- Anti-Screenshot CSS & Privacy Protection Rules -->
<style>
/* Prevent text highlighting, dragging, context menu selection */
.quiz-protected-zone {
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
    user-select: none !important;
    -webkit-touch-callout: none !important;
}

/* Print CSS Blocker: Prevents Ctrl+P / print-to-PDF / print-screen printing */
@media print {
    html, body, * {
        display: none !important;
        visibility: hidden !important;
    }
}

/* Privacy Shield: Blurs out quiz when window loses focus (e.g. Snipping tool / Screenshot apps) */
#privacy-shield {
    transition: opacity 0.2s ease, backdrop-filter 0.2s ease;
}

/* Repeating diagonal watermark */
.quiz-watermark-overlay {
    pointer-events: none;
    position: absolute;
    inset: 0;
    overflow: hidden;
    opacity: 0.045;
    z-index: 10;
    background-image: repeating-linear-gradient(
        -45deg,
        rgba(168, 85, 247, 0.4),
        rgba(168, 85, 247, 0.4) 1px,
        transparent 1px,
        transparent 60px
    );
}

.timer-ring-circle {
    transition: stroke-dashoffset 0.1s linear, stroke 0.3s ease;
}

@keyframes pulse-urgent {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.05); opacity: 0.85; }
}

.pulse-urgent-timer {
    animation: pulse-urgent 0.8s infinite ease-in-out;
}
</style>

<div class="mx-auto max-w-[1440px] px-4 sm:px-6 pb-24 pt-8 lg:px-8 relative min-h-screen">

    <!-- Breadcrumb & Status Ribbon -->
    <div class="mb-4 flex flex-wrap items-center justify-between gap-2 text-[10px] font-black uppercase tracking-[0.24em] text-slate-400 dark:text-zinc-400">
        <div class="flex items-center gap-2">
            <span>AWS Student Builders</span>
            <span>/</span>
            <span class="text-purple-600 dark:text-purple-400">Cloud Exam & Quiz Engine</span>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 px-3 py-1 text-[9px] font-black text-emerald-600 dark:text-emerald-400">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                🛡️ Anti-Screenshot Guard Active
            </span>
            <?php if ($isAdmin): ?>
                <span class="rounded-full bg-purple-600/10 border border-purple-500/20 px-3 py-1 text-[9px] font-black text-purple-600 dark:text-purple-400">
                    Lead Administrator
                </span>
            <?php elseif ($isMember): ?>
                <span class="rounded-full bg-blue-500/10 border border-blue-500/20 px-3 py-1 text-[9px] font-black text-blue-600 dark:text-blue-400">
                    Builder: <?php echo htmlspecialchars($loggedMember['name'] ?? 'Member'); ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Notification Banners -->
    <?php if ($success): ?>
        <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4 mb-6 text-sm text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-2">
            ✓ <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="rounded-2xl border border-red-500/20 bg-red-500/10 p-4 mb-6 text-sm text-red-600 dark:text-red-400 font-bold flex items-center gap-2">
            ⚠️ <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Header Section -->
    <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6 border-b border-slate-200 dark:border-white/10 pb-6">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.28em] text-purple-600 dark:text-purple-400">AWS Builder Competency Assessment</p>
            <h1 class="mt-2 text-3xl sm:text-5xl font-black text-slate-900 dark:text-white leading-none font-space">
                Quiz <span class="text-glow-gradient">Generator & Arena</span>
            </h1>
            <p class="mt-3 max-w-2xl text-xs sm:text-sm text-slate-500 dark:text-zinc-400 font-medium">
                Admin-crafted timed cloud quizzes. Answer faster to capture speed bonus multiplier points! High-security environment with screenshot blocking enabled.
            </p>
        </div>

        <!-- Mode Switcher Controls -->
        <div class="flex items-center gap-2 self-start md:self-center shrink-0">
            <?php if ($isAdmin): ?>
                <a href="quiz.php?view=admin" class="rounded-full px-5 py-2.5 text-xs font-black uppercase tracking-wider transition-all <?php echo $viewMode === 'admin' ? 'bg-purple-600 text-white shadow-md shadow-purple-600/20' : 'border border-slate-200 dark:border-white/10 text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-white/5'; ?>">
                    ⚙️ Quiz Generator & Settings
                </a>
                <a href="quiz.php?view=student" class="rounded-full px-5 py-2.5 text-xs font-black uppercase tracking-wider transition-all <?php echo $viewMode === 'student' ? 'bg-purple-600 text-white shadow-md shadow-purple-600/20' : 'border border-slate-200 dark:border-white/10 text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-white/5'; ?>">
                    🎯 Student Quiz View
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- VIEW 1: ACTIVE QUIZ PLAYER (TIMED WITH ANTI-SCREENSHOT PROTECTION)         -->
    <!-- ========================================================================= -->
    <?php if ($viewMode === 'take_quiz' && $activeQuiz): ?>
        <div id="quiz-player-container" class="quiz-protected-zone relative rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-[#0c0817] p-6 sm:p-10 shadow-2xl overflow-hidden max-w-4xl mx-auto">
            
            <!-- Watermark Canvas Overlay -->
            <div class="quiz-watermark-overlay"></div>
            <div class="pointer-events-none absolute inset-0 z-10 flex items-center justify-center opacity-[0.035] text-5xl sm:text-7xl font-black font-space uppercase rotate-[-25deg] select-none text-purple-600">
                <?php echo htmlspecialchars($loggedMember['name'] ?? 'AUTHORIZED BUILDER'); ?> • <?php echo htmlspecialchars($loggedMember['member_code'] ?? 'SBG'); ?>
            </div>

            <!-- Quiz Header Info -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-white/10 pb-6 mb-8 relative z-20">
                <div>
                    <span class="rounded-full bg-purple-500/10 border border-purple-500/20 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-purple-600 dark:text-purple-400">
                        Exam Session
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-space mt-2">
                        <?php echo htmlspecialchars($activeQuiz['title']); ?>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">
                        Question <span id="current-q-num" class="font-bold text-purple-600 dark:text-purple-400">1</span> of <?php echo count($activeQuestions); ?> • 
                        Candidate: <strong class="text-slate-800 dark:text-white"><?php echo htmlspecialchars($loggedMember['name'] ?? 'Admin Tester'); ?></strong>
                    </p>
                </div>

                <!-- Per-Question Circular Countdown Timer -->
                <div class="flex items-center gap-4 bg-slate-50 dark:bg-white/5 p-3 rounded-2xl border border-slate-200 dark:border-white/10 shrink-0 self-start sm:self-auto">
                    <div class="relative w-16 h-16 flex items-center justify-center">
                        <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-slate-200 dark:text-white/10 stroke-current" stroke-width="3.5" fill="none"
                                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path id="timer-progress-ring" class="timer-ring-circle text-purple-600 dark:text-purple-500 stroke-current" stroke-width="3.5" stroke-linecap="round" fill="none"
                                  stroke-dasharray="100, 100" stroke-dashoffset="0"
                                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                            <span id="timer-seconds-display" class="text-lg font-black font-space text-slate-900 dark:text-white leading-none">10</span>
                            <span class="text-[7px] font-bold uppercase text-slate-400 tracking-tighter">SEC</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-wider text-slate-400">Timer Limit</p>
                        <p id="timer-limit-label" class="text-xs font-bold text-slate-700 dark:text-zinc-200">10s max</p>
                        <span class="text-[9px] font-extrabold text-amber-500">⚡ Fast = +Bonus</span>
                    </div>
                </div>
            </div>

            <!-- Anti-Cheat Status Ticker -->
            <div class="mb-6 rounded-2xl bg-amber-500/10 border border-amber-500/20 p-3 flex items-center justify-between text-xs text-amber-600 dark:text-amber-400 font-medium relative z-20">
                <span class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-amber-500 animate-ping"></span>
                    🔒 Proctored View: Right-click, PrintScreen, and window switching are restricted.
                </span>
                <span class="text-[10px] font-bold uppercase tracking-wider opacity-80">Auto-moves when timer expires</span>
            </div>

            <!-- Active Question Body -->
            <div id="question-card" class="relative z-20 space-y-6">
                <!-- Question Statement -->
                <div class="rounded-2xl bg-slate-50/80 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 p-6">
                    <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-purple-600 dark:text-purple-400 mb-2">
                        <span id="question-badge">Question 1</span>
                        <span id="question-points-badge" class="rounded-full bg-purple-500/10 px-2.5 py-0.5 border border-purple-500/20">10 PTS</span>
                    </div>
                    <h3 id="question-text" class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white font-space leading-relaxed">
                        Loading question...
                    </h3>
                </div>

                <!-- MCQs Options (A, B, C, D) -->
                <div id="options-grid" class="grid gap-3 sm:grid-cols-2">
                    <!-- Options injected via JavaScript -->
                </div>

                <!-- Synchronized Timer & Lock Status Bar -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-slate-200 dark:border-white/10">
                    <div id="answer-lock-status" class="flex items-center gap-2 text-xs text-slate-500 dark:text-zinc-400 font-medium">
                        <span class="h-2 w-2 rounded-full bg-purple-500 animate-pulse"></span>
                        <span id="speed-bonus-hint">💡 Click an option above. Answering earlier earns higher speed bonus points!</span>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <span id="lock-status-badge" class="hidden rounded-full bg-emerald-500/10 border border-emerald-500/20 px-3.5 py-1.5 text-[10px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                            ✓ Answer Locked
                        </span>
                        <div class="rounded-full bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 px-4 py-2 text-xs font-black text-slate-800 dark:text-zinc-200 font-space flex items-center gap-1.5">
                            <span>⏳ Full Timer:</span>
                            <span id="btn-timer-count" class="text-purple-600 dark:text-purple-400 font-bold">10</span>s
                        </div>
                    </div>
                </div>
            </div>

            <!-- Final Results View (Hidden Initially) -->
            <div id="quiz-results-card" class="hidden relative z-20 text-center py-8">
                <div class="mx-auto w-20 h-20 rounded-full bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-4xl mb-4">
                    🏆
                </div>
                <h3 class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white font-space">
                    Quiz Completed!
                </h3>
                <p class="text-sm text-slate-500 dark:text-zinc-400 mt-2 max-w-md mx-auto">
                    Great effort! Your answers have been recorded in the chapter assessment index.
                </p>

                <!-- Scorecard Summary Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-2xl mx-auto my-8">
                    <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/[0.02] p-4 text-center">
                        <span class="block text-[9px] font-black uppercase text-slate-400 tracking-wider">Total Score</span>
                        <span id="res-total-score" class="text-2xl font-black text-purple-600 dark:text-purple-400 font-space">0.0</span>
                        <span class="text-[9px] text-slate-400 block mt-0.5">PTS</span>
                    </div>
                    <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/[0.02] p-4 text-center">
                        <span class="block text-[9px] font-black uppercase text-slate-400 tracking-wider">Speed Bonus</span>
                        <span id="res-speed-bonus" class="text-2xl font-black text-amber-500 font-space">+0.0</span>
                        <span class="text-[9px] text-slate-400 block mt-0.5">Extra PTS</span>
                    </div>
                    <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/[0.02] p-4 text-center">
                        <span class="block text-[9px] font-black uppercase text-slate-400 tracking-wider">Accuracy</span>
                        <span id="res-accuracy" class="text-2xl font-black text-emerald-500 font-space">0%</span>
                        <span id="res-correct-count" class="text-[9px] text-slate-400 block mt-0.5">0 / 0</span>
                    </div>
                    <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/[0.02] p-4 text-center">
                        <span class="block text-[9px] font-black uppercase text-slate-400 tracking-wider">Time Taken</span>
                        <span id="res-time-taken" class="text-2xl font-black text-slate-800 dark:text-zinc-200 font-space">0s</span>
                        <span class="text-[9px] text-slate-400 block mt-0.5">Duration</span>
                    </div>
                </div>

                <div id="res-award-banner" class="hidden max-w-md mx-auto mb-6 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 p-3 text-xs text-emerald-600 dark:text-emerald-400 font-bold">
                    ✓ Points have been automatically added to your Chapter Builder PTS balance!
                </div>

                <div class="flex justify-center gap-3">
                    <a href="quiz.php?view=student" class="rounded-full bg-purple-600 hover:bg-purple-500 px-6 py-3 text-xs font-black uppercase tracking-wider text-white transition-all shadow-md shadow-purple-600/20">
                        View All Quizzes
                    </a>
                    <?php if ($isAdmin): ?>
                        <a href="quiz.php?view=admin" class="rounded-full border border-slate-200 dark:border-white/10 hover:bg-slate-100 dark:hover:bg-white/5 px-6 py-3 text-xs font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 transition-all">
                            Back to Admin Generator
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Privacy Blackout Shield (Activated if window is blurred or screenshot attempted) -->
            <div id="privacy-shield" class="hidden absolute inset-0 z-50 flex flex-col items-center justify-center p-6 bg-slate-950/95 backdrop-blur-3xl text-center">
                <div class="w-16 h-16 rounded-full bg-red-500/20 border border-red-500/40 flex items-center justify-center text-3xl mb-4">
                    🛡️
                </div>
                <h4 class="text-xl sm:text-2xl font-black text-white font-space">
                    Security Shield Active
                </h4>
                <p class="text-xs sm:text-sm text-slate-400 max-w-md mt-2 leading-relaxed">
                    Browser window has lost focus or a screen capture utility was detected. Click inside this window to resume your test.
                </p>
                <button type="button" onclick="dismissPrivacyShield()" class="mt-6 rounded-full bg-purple-600 hover:bg-purple-500 px-6 py-2.5 text-xs font-black uppercase tracking-wider text-white transition-all cursor-pointer">
                    Resume Quiz
                </button>
            </div>
        </div>

    <!-- ========================================================================= -->
    <!-- VIEW 2: ADMIN QUIZ GENERATION & SETTINGS PANEL                             -->
    <!-- ========================================================================= -->
    <?php elseif ($viewMode === 'admin' && $isAdmin): ?>
        <div class="space-y-10">

            <!-- Quick Template Generation Banner -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-3xl border border-purple-500/20 bg-purple-500/5 p-6 sm:p-8">
                <div>
                    <span class="text-[9px] font-black uppercase tracking-widest text-purple-600 dark:text-purple-400">Quick Start</span>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white font-space mt-1">Generate Instant AWS Practice Quiz</h3>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">Want to test the quiz arena right away? Click to seed 5 sample questions with custom timers and scores.</p>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="seed_sample_quiz">
                    <button type="submit" class="rounded-full bg-purple-600 hover:bg-purple-500 px-6 py-3 text-xs font-black uppercase tracking-wider text-white transition-all shadow-md shadow-purple-600/20 whitespace-nowrap cursor-pointer">
                        ⚡ Seed Sample AWS Quiz
                    </button>
                </form>
            </div>

            <!-- Quiz Generator & Questions Builder Form -->
            <div class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.02] p-6 sm:p-8 shadow-md">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-white/10 pb-4 mb-6">
                    <div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white font-space">
                            ➕ Create New Quiz Scenario
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">
                            Define the quiz title, description, and add custom MCQs with per-question timers and point values.
                        </p>
                    </div>
                </div>

                <form method="POST" id="create-quiz-form" class="space-y-6">
                    <input type="hidden" name="action" value="create_quiz">

                    <!-- Quiz Meta Details -->
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1">Quiz Title</label>
                            <input type="text" name="title" required placeholder="e.g. AWS Solutions Architect Associate Sprint" class="form-input w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-4 py-3 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1">Quiz Instructions / Description</label>
                            <textarea name="description" rows="2" placeholder="Explain the topics covered, rules, and scoring incentives..." class="form-input w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-4 py-3 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500"></textarea>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" id="is_active_cb" checked class="rounded border-slate-300 text-purple-600 focus:ring-purple-500 h-4 w-4">
                            <label for="is_active_cb" class="text-xs font-bold text-slate-700 dark:text-zinc-300 cursor-pointer">Make Quiz Active (Visible to students immediately)</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="award_points" id="award_points_cb" checked class="rounded border-slate-300 text-purple-600 focus:ring-purple-500 h-4 w-4">
                            <label for="award_points_cb" class="text-xs font-bold text-slate-700 dark:text-zinc-300 cursor-pointer">Award earned score directly to Member PTS balance</label>
                        </div>
                    </div>

                    <!-- Dynamic Questions Container -->
                    <div class="pt-6 border-t border-slate-200 dark:border-white/10">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="text-base font-black text-slate-900 dark:text-white font-space">Quiz Questions (<span id="total-q-counter">1</span>)</h4>
                                <p class="text-xs text-slate-500 dark:text-zinc-400">Set the custom time limit (seconds) and score (points) individually for each question.</p>
                            </div>
                            <button type="button" onclick="addNewQuestionBlock()" class="rounded-full bg-purple-500/10 border border-purple-500/20 hover:bg-purple-500/20 px-4 py-2 text-xs font-black uppercase tracking-wider text-purple-600 dark:text-purple-400 transition-all cursor-pointer">
                                ➕ Add Another Question
                            </button>
                        </div>

                        <div id="questions-builder-list" class="space-y-6">
                            <!-- Question Card Template Block (First Question) -->
                            <div class="question-builder-item rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-white/[0.01] p-5 relative" data-index="1">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="question-index-label rounded-full bg-purple-600 text-white px-3 py-0.5 text-[9px] font-black uppercase tracking-widest">
                                        Question #1
                                    </span>
                                    <button type="button" onclick="removeQuestionBlock(this)" class="text-xs text-red-500 hover:text-red-700 font-bold cursor-pointer">
                                        ✕ Remove
                                    </button>
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1">Question Statement</label>
                                        <textarea name="q_text[]" required rows="2" placeholder="e.g. Which AWS service is used for serverless relational databases?" class="form-input w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500"></textarea>
                                    </div>

                                    <!-- 4 Options Inputs -->
                                    <div class="grid gap-2 sm:grid-cols-2">
                                        <div>
                                            <label class="block text-[9px] font-black uppercase text-slate-400 mb-1">Option A</label>
                                            <input type="text" name="q_opt_a[]" required placeholder="Option A text" class="form-input w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500">
                                        </div>
                                        <div>
                                            <label class="block text-[9px] font-black uppercase text-slate-400 mb-1">Option B</label>
                                            <input type="text" name="q_opt_b[]" required placeholder="Option B text" class="form-input w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500">
                                        </div>
                                        <div>
                                            <label class="block text-[9px] font-black uppercase text-slate-400 mb-1">Option C</label>
                                            <input type="text" name="q_opt_c[]" required placeholder="Option C text" class="form-input w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500">
                                        </div>
                                        <div>
                                            <label class="block text-[9px] font-black uppercase text-slate-400 mb-1">Option D</label>
                                            <input type="text" name="q_opt_d[]" required placeholder="Option D text" class="form-input w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500">
                                        </div>
                                    </div>

                                    <!-- Correct Option, Points Box, Timer Box -->
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                                        <div>
                                            <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1">Correct Answer</label>
                                            <select name="q_correct[]" class="form-input w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-[#0d0a15] px-3 py-2 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500">
                                                <option value="A">Option A</option>
                                                <option value="B">Option B</option>
                                                <option value="C">Option C</option>
                                                <option value="D">Option D</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[9px] font-black uppercase tracking-wider text-purple-600 dark:text-purple-400 mb-1">Score for this MCQ (Points)</label>
                                            <input type="number" name="q_points[]" required min="1" value="10" placeholder="10" class="form-input w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500 font-bold">
                                        </div>
                                        <div>
                                            <label class="block text-[9px] font-black uppercase tracking-wider text-amber-500 mb-1">Timer for this Question (Seconds)</label>
                                            <input type="number" name="q_timer[]" required min="3" value="10" placeholder="e.g. 5, 10, 40, 50" class="form-input w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500 font-bold">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full rounded-full bg-purple-600 hover:bg-purple-500 py-3.5 text-xs font-black uppercase tracking-widest text-white transition-all shadow-md shadow-purple-600/20 cursor-pointer">
                        🚀 Generate & Publish Quiz Scenario
                    </button>
                </form>
            </div>

            <!-- Existing Quizzes Management Table -->
            <div class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.02] p-6 sm:p-8 shadow-md">
                <h3 class="text-xl font-black text-slate-900 dark:text-white font-space mb-2">
                    📚 Generated Quizzes Directory (<?php echo count($allQuizzes); ?>)
                </h3>
                <p class="text-xs text-slate-500 dark:text-zinc-400 mb-6 font-medium">Manage existing quizzes, toggle student availability, preview exam flow, or delete.</p>

                <?php if (empty($allQuizzes)): ?>
                    <p class="text-xs text-slate-400 italic py-4">No quizzes generated yet. Use the form above to create your first quiz!</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($allQuizzes as $qz): ?>
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-2xl border border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-white/[0.01] p-5">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="rounded-full px-2.5 py-0.5 text-[9px] font-black uppercase tracking-widest <?php echo $qz['is_active'] ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-slate-200 dark:bg-white/10 text-slate-500'; ?>">
                                            <?php echo $qz['is_active'] ? 'Live' : 'Hidden Draft'; ?>
                                        </span>
                                        <span class="text-[9px] font-bold text-slate-400">
                                            Created: <?php echo date('M d, Y', strtotime($qz['created_at'])); ?>
                                        </span>
                                    </div>
                                    <h4 class="text-base font-bold text-slate-900 dark:text-white font-space">
                                        <?php echo htmlspecialchars($qz['title']); ?>
                                    </h4>
                                    <div class="flex flex-wrap gap-3 mt-2 text-xs text-slate-500 dark:text-zinc-400 font-medium">
                                        <span>📝 <?php echo $qz['question_count']; ?> Questions</span>
                                        <span>⚡ Max <?php echo $qz['total_points']; ?> PTS</span>
                                        <span>👥 <?php echo $qz['attempt_count']; ?> Submissions</span>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="quiz.php?take=<?php echo $qz['id']; ?>" class="rounded-full bg-purple-600/10 border border-purple-500/20 hover:bg-purple-600/20 px-3.5 py-1.5 text-[9px] font-black uppercase tracking-widest text-purple-600 dark:text-purple-400 transition-colors">
                                        ▶ Test as Student
                                    </a>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="action" value="toggle_quiz">
                                        <input type="hidden" name="quiz_id" value="<?php echo $qz['id']; ?>">
                                        <button type="submit" class="rounded-full border border-slate-300 dark:border-white/10 hover:bg-slate-200 dark:hover:bg-white/10 px-3 py-1.5 text-[9px] font-black uppercase tracking-widest text-slate-600 dark:text-zinc-400 cursor-pointer">
                                            <?php echo $qz['is_active'] ? 'Hide' : 'Publish'; ?>
                                        </button>
                                    </form>
                                    <form method="POST" onsubmit="return confirm('Delete quiz \'<?php echo addslashes($qz['title']); ?>\' and all attempt records?');" class="inline">
                                        <input type="hidden" name="action" value="delete_quiz">
                                        <input type="hidden" name="quiz_id" value="<?php echo $qz['id']; ?>">
                                        <button type="submit" class="rounded-full bg-red-500/10 border border-red-500/20 hover:bg-red-500/20 px-3 py-1.5 text-[9px] font-black uppercase tracking-widest text-red-500 cursor-pointer">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Quiz Submissions Table -->
            <div class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.02] p-6 sm:p-8 shadow-md">
                <h3 class="text-xl font-black text-slate-900 dark:text-white font-space mb-2">
                    🏆 Top Quiz Performers & Submissions
                </h3>
                <p class="text-xs text-slate-500 dark:text-zinc-400 mb-6 font-medium">Students who answered correctly and fastest earned extra multiplier speed bonus points.</p>

                <?php if (empty($topAttempts)): ?>
                    <p class="text-xs text-slate-400 italic py-4">No submissions yet.</p>
                <?php else: ?>
                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-white/10 text-[9px] font-black uppercase tracking-wider text-slate-400">
                                    <th class="py-3 px-3">Student Name</th>
                                    <th class="py-3 px-3">Quiz</th>
                                    <th class="py-3 px-3">Accuracy</th>
                                    <th class="py-3 px-3">Speed Bonus</th>
                                    <th class="py-3 px-3">Total Score</th>
                                    <th class="py-3 px-3">Time</th>
                                    <th class="py-3 px-3">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                                <?php foreach ($topAttempts as $att): ?>
                                    <tr>
                                        <td class="py-3 px-3 font-bold text-slate-900 dark:text-white">
                                            <?php echo htmlspecialchars($att['member_name']); ?>
                                        </td>
                                        <td class="py-3 px-3 text-slate-600 dark:text-zinc-400">
                                            <?php echo htmlspecialchars($att['quiz_title']); ?>
                                        </td>
                                        <td class="py-3 px-3">
                                            <span class="rounded-full bg-emerald-500/10 px-2 py-0.5 text-[9px] font-black text-emerald-600 dark:text-emerald-400">
                                                <?php echo $att['correct_count']; ?> / <?php echo $att['total_questions']; ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-amber-500 font-bold">
                                            +<?php echo number_format($att['speed_bonus'], 1); ?> PTS
                                        </td>
                                        <td class="py-3 px-3 font-black text-purple-600 dark:text-purple-400 font-space text-sm">
                                            <?php echo number_format($att['total_score'], 1); ?> PTS
                                        </td>
                                        <td class="py-3 px-3 text-slate-500">
                                            <?php echo $att['time_spent_seconds']; ?>s
                                        </td>
                                        <td class="py-3 px-3 text-slate-400 text-[10px]">
                                            <?php echo date('M d, H:i', strtotime($att['completed_at'])); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    <!-- ========================================================================= -->
    <!-- VIEW 3: STUDENT PORTAL (VIEW ACTIVE QUIZZES & START ATTEMPTS)              -->
    <!-- ========================================================================= -->
    <?php else: ?>
        <div class="space-y-8">
            <!-- Student Header Banner -->
            <div class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.02] p-6 sm:p-8 shadow-md">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="rounded-full bg-purple-500/10 border border-purple-500/20 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-purple-600 dark:text-purple-400">
                            Builder Exam Terminal
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-space mt-2">
                            Available Quizzes & Challenges
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">
                            Choose an active quiz below. Once you click Start, the secure timer starts. Answer early for speed multipliers!
                        </p>
                    </div>

                    <?php if (!$isMember && !$isAdmin): ?>
                        <a href="login.php" class="rounded-full bg-purple-600 hover:bg-purple-500 px-6 py-3 text-xs font-black uppercase tracking-wider text-white transition-all shadow-md shadow-purple-600/20 whitespace-nowrap self-start sm:self-auto">
                            🔑 Login with Member ID to Attempt
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quiz List Grid -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <?php 
                $activeList = array_filter($allQuizzes, function($q) { return !empty($q['is_active']); });
                if (empty($activeList)): 
                ?>
                    <div class="col-span-full rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.02] p-10 text-center">
                        <span class="text-4xl">⏳</span>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white font-space mt-3">No Active Quizzes Right Now</h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">The chapter leads have not released any active quizzes at this moment. Check back soon!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($activeList as $qz): ?>
                        <div class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.02] p-6 shadow-sm flex flex-col justify-between hover-glow-card">
                            <div>
                                <div class="flex items-center justify-between text-[9px] font-black uppercase tracking-widest text-purple-600 dark:text-purple-400 mb-3">
                                    <span>AWS Chapter Quiz</span>
                                    <span class="rounded-full bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 text-emerald-600 dark:text-emerald-400">
                                        ⚡ Active
                                    </span>
                                </div>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white font-space leading-snug">
                                    <?php echo htmlspecialchars($qz['title']); ?>
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-zinc-400 mt-2 line-clamp-2">
                                    <?php echo htmlspecialchars($qz['description'] ?: 'Complete this fast-paced chapter quiz to validate your cloud skills.'); ?>
                                </p>

                                <div class="grid grid-cols-2 gap-2 my-5 text-center">
                                    <div class="rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 p-2">
                                        <span class="block text-[8px] font-black uppercase text-slate-400">Questions</span>
                                        <span class="text-sm font-black text-slate-900 dark:text-white font-space"><?php echo $qz['question_count']; ?> MCQs</span>
                                    </div>
                                    <div class="rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 p-2">
                                        <span class="block text-[8px] font-black uppercase text-slate-400">Max Score</span>
                                        <span class="text-sm font-black text-purple-600 dark:text-purple-400 font-space"><?php echo $qz['total_points']; ?> PTS</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <?php if ($isMember || $isAdmin): ?>
                                    <a href="quiz.php?take=<?php echo $qz['id']; ?>" class="block w-full text-center rounded-full bg-purple-600 hover:bg-purple-500 py-3 text-xs font-black uppercase tracking-widest text-white transition-all shadow-md shadow-purple-600/20">
                                        Start Quiz Now →
                                    </a>
                                <?php else: ?>
                                    <a href="login.php" class="block w-full text-center rounded-full border border-purple-500/30 text-purple-600 dark:text-purple-400 hover:bg-purple-600/10 py-3 text-xs font-black uppercase tracking-widest transition-all">
                                        Login to Attempt
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Student Top Scores Showcase -->
            <?php if (!empty($topAttempts)): ?>
                <div class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.02] p-6 sm:p-8 shadow-md">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white font-space mb-4">
                        🌟 Hall of Speed & Accuracy
                    </h3>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <?php foreach (array_slice($topAttempts, 0, 4) as $idx => $top): ?>
                            <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-white/[0.01] p-4 text-left">
                                <span class="rounded-full bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 text-[8px] font-black uppercase tracking-widest text-amber-500">
                                    Rank #<?php echo $idx + 1; ?>
                                </span>
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white mt-2 truncate"><?php echo htmlspecialchars($top['member_name']); ?></h4>
                                <p class="text-base font-black text-purple-600 dark:text-purple-400 font-space mt-1"><?php echo number_format($top['total_score'], 1); ?> <span class="text-[9px] font-sans font-bold text-slate-400">PTS</span></p>
                                <span class="text-[9px] text-emerald-500 font-bold block mt-0.5">⚡ +<?php echo number_format($top['speed_bonus'], 1); ?> speed bonus</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

<!-- Client-side Logic & Anti-Cheat Engine -->
<script>
// =========================================================================
// 1. Anti-Screenshot & Screen Capture Protection Suite
// =========================================================================
const isQuizTaking = <?php echo $viewMode === 'take_quiz' ? 'true' : 'false'; ?>;

if (isQuizTaking) {
    // A. Disable Context Menu
    document.addEventListener('contextmenu', (e) => {
        e.preventDefault();
        return false;
    });

    // B. Disable Key Combinations (PrintScreen, Ctrl+P, Ctrl+S, DevTools, etc.)
    window.addEventListener('keydown', (e) => {
        // Intercept PrintScreen
        if (e.key === 'PrintScreen' || e.keyCode === 44) {
            e.preventDefault();
            triggerScreenshotProtection();
            return false;
        }

        // Intercept Ctrl+P, Ctrl+S, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U, F12
        if ((e.ctrlKey || e.metaKey) && (e.key === 'p' || e.key === 'P' || e.key === 's' || e.key === 'S' || e.key === 'u' || e.key === 'U')) {
            e.preventDefault();
            return false;
        }

        if (e.key === 'F12' || ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'C' || e.key === 'c'))) {
            e.preventDefault();
            return false;
        }
    });

    // C. Intercept KeyUp on PrintScreen (clears clipboard)
    window.addEventListener('keyup', (e) => {
        if (e.key === 'PrintScreen' || e.keyCode === 44) {
            try {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText('⚠️ Screenshots are disabled during chapter examinations.');
                }
            } catch (err) {}
            triggerScreenshotProtection();
        }
    });

    // D. Window Blur & Visibility Loss: Covers the screen with black shield
    // (Snipping Tool & external screenshot software triggers window blur before capture)
    window.addEventListener('blur', () => {
        triggerScreenshotProtection();
    });

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden') {
            triggerScreenshotProtection();
        }
    });
}

function triggerScreenshotProtection() {
    const shield = document.getElementById('privacy-shield');
    if (shield) {
        shield.classList.remove('hidden');
        shield.style.display = 'flex';
    }
}

function dismissPrivacyShield() {
    const shield = document.getElementById('privacy-shield');
    if (shield) {
        shield.classList.add('hidden');
        shield.style.display = 'none';
    }
}

// =========================================================================
// 2. Timed Quiz Engine (Per-Question Timer + Auto-Next + Speed Bonus)
// =========================================================================
<?php if ($viewMode === 'take_quiz' && !empty($activeQuestions)): ?>
const quizData = <?php echo json_encode($activeQuestions); ?>;
const activeQuizId = <?php echo $activeQuizId; ?>;

let currentQuestionIndex = 0;
let questionRemainingSeconds = 0;
let questionTotalTimer = 0;
let timerInterval = null;
let userAnswers = [];
let quizStartTime = Date.now();
let selectedOption = null;
let recordedRemainingSeconds = 0;
let hasSelected = false;

function initQuizPlayer() {
    if (!quizData || quizData.length === 0) return;
    loadQuestion(0);
}

function loadQuestion(index) {
    if (index >= quizData.length) {
        finalizeQuizSubmission();
        return;
    }

    currentQuestionIndex = index;
    selectedOption = null;
    recordedRemainingSeconds = 0;
    hasSelected = false;
    const q = quizData[index];

    // Update Question statement & numbering
    document.getElementById('current-q-num').innerText = (index + 1);
    document.getElementById('question-badge').innerText = `Question ${index + 1} of ${quizData.length}`;
    document.getElementById('question-points-badge').innerText = `${q.points} PTS`;
    document.getElementById('question-text').innerText = q.question_text;

    const lockBadge = document.getElementById('lock-status-badge');
    if (lockBadge) {
        lockBadge.classList.add('hidden');
    }

    const hint = document.getElementById('speed-bonus-hint');
    if (hint) {
        hint.innerHTML = '💡 Click an option above. Answering earlier locks in higher speed bonus points!';
    }

    // Render Options A, B, C, D
    const optionsGrid = document.getElementById('options-grid');
    optionsGrid.innerHTML = '';
    const letters = ['A', 'B', 'C', 'D'];
    const optTexts = [q.option_a, q.option_b, q.option_c, q.option_d];

    letters.forEach((letter, idx) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'quiz-option-btn group flex items-start gap-3 p-4 rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.02] hover:border-purple-500 hover:bg-purple-500/5 transition-all text-left cursor-pointer';
        btn.setAttribute('data-option', letter);
        btn.onclick = () => selectOption(letter, btn);

        btn.innerHTML = `
            <span class="opt-letter-circle flex h-7 w-7 shrink-0 items-center justify-center rounded-xl bg-slate-100 dark:bg-white/10 text-xs font-black text-slate-700 dark:text-zinc-300 group-hover:bg-purple-600 group-hover:text-white transition-all">
                ${letter}
            </span>
            <span class="text-xs sm:text-sm font-medium text-slate-800 dark:text-zinc-200 leading-snug pt-0.5">
                ${escapeHtml(optTexts[idx])}
            </span>
        `;
        optionsGrid.appendChild(btn);
    });

    // Setup Per-Question Timer - Runs full duration for all candidates!
    questionTotalTimer = parseInt(q.time_limit) || 10;
    questionRemainingSeconds = questionTotalTimer;
    document.getElementById('timer-limit-label').innerText = `${questionTotalTimer}s full timer`;

    clearInterval(timerInterval);
    updateTimerVisuals();

    timerInterval = setInterval(() => {
        questionRemainingSeconds -= 0.1;
        if (questionRemainingSeconds <= 0) {
            clearInterval(timerInterval);
            questionRemainingSeconds = 0;
            updateTimerVisuals();
            // Automatically transitions to the next question when the full timer finishes!
            onQuestionTimerComplete();
        } else {
            updateTimerVisuals();
        }
    }, 100);
}

function selectOption(letter, btn) {
    selectedOption = letter;
    // Lock in the remaining seconds at click time to reward early answers with speed bonus
    recordedRemainingSeconds = Math.max(0, questionRemainingSeconds);
    hasSelected = true;

    // Highlight selected option
    document.querySelectorAll('.quiz-option-btn').forEach(b => {
        b.classList.remove('border-purple-600', 'bg-purple-500/10', 'ring-2', 'ring-purple-500/40');
        const c = b.querySelector('.opt-letter-circle');
        if (c) {
            c.classList.remove('bg-purple-600', 'text-white');
            c.classList.add('bg-slate-100', 'dark:bg-white/10', 'text-slate-700', 'dark:text-zinc-300');
        }
    });

    btn.classList.add('border-purple-600', 'bg-purple-500/10', 'ring-2', 'ring-purple-500/40');
    const circle = btn.querySelector('.opt-letter-circle');
    if (circle) {
        circle.classList.add('bg-purple-600', 'text-white');
        circle.classList.remove('bg-slate-100', 'dark:bg-white/10');
    }

    // Display locked indicator
    const lockBadge = document.getElementById('lock-status-badge');
    if (lockBadge) {
        lockBadge.classList.remove('hidden');
        lockBadge.innerHTML = `✓ Option ${letter} Locked`;
    }

    // Inform the student that their answer is locked and they must wait for the timer to finish
    const hint = document.getElementById('speed-bonus-hint');
    if (hint) {
        hint.innerHTML = `<span class="text-purple-600 dark:text-purple-400 font-bold">✓ Option ${letter} locked with ${recordedRemainingSeconds.toFixed(1)}s left! Please wait for timer to finish...</span>`;
    }
}

function updateTimerVisuals() {
    const secDisplay = document.getElementById('timer-seconds-display');
    const ring = document.getElementById('timer-progress-ring');
    const btnCount = document.getElementById('btn-timer-count');
    const roundedSec = Math.ceil(questionRemainingSeconds);

    if (secDisplay) secDisplay.innerText = roundedSec;
    if (btnCount) btnCount.innerText = roundedSec;

    // Stroke Dashoffset percentage
    const ratio = Math.max(0, questionRemainingSeconds / questionTotalTimer);
    const strokeOffset = 100 - (ratio * 100);
    if (ring) {
        ring.style.strokeDashoffset = strokeOffset;
        if (ratio < 0.25) {
            ring.style.stroke = '#ef4444'; // Red urgency
            if (secDisplay) secDisplay.classList.add('text-red-500');
        } else if (ratio < 0.5) {
            ring.style.stroke = '#f59e0b'; // Amber warning
            if (secDisplay) secDisplay.classList.remove('text-red-500');
        } else {
            ring.style.stroke = '#a855f7'; // Purple normal
            if (secDisplay) secDisplay.classList.remove('text-red-500');
        }
    }
}

function onQuestionTimerComplete() {
    clearInterval(timerInterval);
    const q = quizData[currentQuestionIndex];

    // Push the recorded answer (keeps speed bonus recorded at click time, or 0 if unattempted)
    userAnswers.push({
        question_id: q.id,
        selected_option: hasSelected ? selectedOption : '',
        remaining_seconds: hasSelected ? recordedRemainingSeconds : 0
    });

    const hint = document.getElementById('speed-bonus-hint');
    if (hint) {
        if (hasSelected) {
            hint.innerHTML = '<span class="text-emerald-500 font-bold">⏱️ Timer complete! Opening next question...</span>';
        } else {
            hint.innerHTML = '<span class="text-red-500 font-bold">⏱️ Time Expired without selection! Opening next question...</span>';
        }
    }

    setTimeout(() => {
        loadQuestion(currentQuestionIndex + 1);
    }, 450);
}

function finalizeQuizSubmission() {
    clearInterval(timerInterval);
    const totalTimeSpent = Math.round((Date.now() - quizStartTime) / 1000);

    // Hide question card and show loading state
    document.getElementById('question-card').style.display = 'none';
    const resultsCard = document.getElementById('quiz-results-card');
    resultsCard.classList.remove('hidden');
    resultsCard.style.display = 'block';

    // Submit payload to server
    const formData = new FormData();
    formData.append('api_action', 'submit_attempt');
    formData.append('quiz_id', activeQuizId);
    formData.append('answers', JSON.stringify(userAnswers));
    formData.append('time_spent', totalTimeSpent);

    fetch('quiz.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('res-total-score').innerText = Number(data.total_score).toFixed(1);
            document.getElementById('res-speed-bonus').innerText = '+' + Number(data.speed_bonus).toFixed(1);
            const acc = Math.round((data.correct_count / Math.max(1, data.total_questions)) * 100);
            document.getElementById('res-accuracy').innerText = `${acc}%`;
            document.getElementById('res-correct-count').innerText = `${data.correct_count} / ${data.total_questions} Correct`;
            document.getElementById('res-time-taken').innerText = `${totalTimeSpent}s`;

            if (data.points_awarded) {
                document.getElementById('res-award-banner').classList.remove('hidden');
            }
        } else {
            alert(data.error || 'Failed to submit quiz attempt.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Attempt saved locally. Result evaluation finished.');
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.innerText = text;
    return div.innerHTML;
}

window.addEventListener('DOMContentLoaded', initQuizPlayer);
<?php endif; ?>

// =========================================================================
// 3. Admin Dynamic Question Form Builder
// =========================================================================
function addNewQuestionBlock() {
    const container = document.getElementById('questions-builder-list');
    if (!container) return;

    const count = container.querySelectorAll('.question-builder-item').length + 1;
    document.getElementById('total-q-counter').innerText = count;

    const div = document.createElement('div');
    div.className = 'question-builder-item rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-white/[0.01] p-5 relative';
    div.setAttribute('data-index', count);

    div.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <span class="question-index-label rounded-full bg-purple-600 text-white px-3 py-0.5 text-[9px] font-black uppercase tracking-widest">
                Question #${count}
            </span>
            <button type="button" onclick="removeQuestionBlock(this)" class="text-xs text-red-500 hover:text-red-700 font-bold cursor-pointer">
                ✕ Remove
            </button>
        </div>

        <div class="space-y-3">
            <div>
                <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1">Question Statement</label>
                <textarea name="q_text[]" required rows="2" placeholder="Write question statement..." class="form-input w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500"></textarea>
            </div>

            <div class="grid gap-2 sm:grid-cols-2">
                <div>
                    <label class="block text-[9px] font-black uppercase text-slate-400 mb-1">Option A</label>
                    <input type="text" name="q_opt_a[]" required placeholder="Option A text" class="form-input w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-[9px] font-black uppercase text-slate-400 mb-1">Option B</label>
                    <input type="text" name="q_opt_b[]" required placeholder="Option B text" class="form-input w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-[9px] font-black uppercase text-slate-400 mb-1">Option C</label>
                    <input type="text" name="q_opt_c[]" required placeholder="Option C text" class="form-input w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-[9px] font-black uppercase text-slate-400 mb-1">Option D</label>
                    <input type="text" name="q_opt_d[]" required placeholder="Option D text" class="form-input w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1">Correct Answer</label>
                    <select name="q_correct[]" class="form-input w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-[#0d0a15] px-3 py-2 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500">
                        <option value="A">Option A</option>
                        <option value="B">Option B</option>
                        <option value="C">Option C</option>
                        <option value="D">Option D</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-purple-600 dark:text-purple-400 mb-1">Score for this MCQ (Points)</label>
                    <input type="number" name="q_points[]" required min="1" value="10" placeholder="10" class="form-input w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500 font-bold">
                </div>
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-amber-500 mb-1">Timer for this Question (Seconds)</label>
                    <input type="number" name="q_timer[]" required min="3" value="10" placeholder="e.g. 5, 10, 40, 50" class="form-input w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500 font-bold">
                </div>
            </div>
        </div>
    `;

    container.appendChild(div);
}

function removeQuestionBlock(btn) {
    const item = btn.closest('.question-builder-item');
    if (!item) return;

    const container = document.getElementById('questions-builder-list');
    if (container.querySelectorAll('.question-builder-item').length <= 1) {
        alert('A quiz must have at least one question.');
        return;
    }

    item.remove();
    // Re-index remaining questions
    container.querySelectorAll('.question-builder-item').forEach((el, idx) => {
        el.querySelector('.question-index-label').innerText = `Question #${idx + 1}`;
    });
    document.getElementById('total-q-counter').innerText = container.querySelectorAll('.question-builder-item').length;
}
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
