<?php
// login.php
require_once 'includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'unified_login') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        
        $loginResult = login_user($username, $password);
        if ($loginResult === 'admin') {
            header('Location: admin.php');
            exit;
        } elseif ($loginResult === 'member') {
            header('Location: store.php');
            exit;
        } else {
            $error = 'Invalid credentials. Check your name/username and password.';
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'guest') {
    login_guest();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Gate | AWS Student Builder Group BZU</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .glass-gate {
            background: rgba(18, 10, 32, 0.6);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        @keyframes pulseGlow {
            0% { transform: scale(1); opacity: 0.15; }
            50% { transform: scale(1.1); opacity: 0.25; }
            100% { transform: scale(1); opacity: 0.15; }
        }
        .animate-glow {
            animation: pulseGlow 8s ease-in-out infinite;
        }
        .text-glow-gradient {
            background: linear-gradient(135deg, #a855f7 0%, #ec4899 50%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200% auto;
            animation: textShine 6s linear infinite;
        }
        @keyframes textShine {
            to { background-position: 200% center; }
        }
    </style>
</head>
<body class="bg-[#05030a] text-slate-100 min-h-screen flex items-center justify-center p-6 relative overflow-hidden font-sans">

    <!-- Glowing Background Blurs -->
    <div class="absolute top-1/4 left-1/4 w-[500px] h-[500px] bg-purple-700/20 rounded-full blur-3xl animate-glow pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-[500px] h-[500px] bg-fuchsia-600/15 rounded-full blur-3xl animate-glow pointer-events-none" style="animation-delay: 4s;"></div>

    <div class="relative z-10 w-full max-w-xl text-center">
        <!-- Brand Logo -->
        <div class="mb-6 flex justify-center">
            <img src="public/images/sbg-logo.png" alt="AWS SBG BZU logo" class="h-24 w-24 rounded-full border border-white/10 shadow-2xl object-cover">
        </div>

        <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-none mb-3 text-white font-outfit">
            AWS <span class="text-glow-gradient">Student Builder</span> Portal
        </h1>
        <p class="mx-auto max-w-md text-xs sm:text-sm leading-relaxed text-zinc-400 mb-8">
            Enter your credentials to access your builder account or lead operations.
        </p>

        <!-- Unified Login Card -->
        <div class="rounded-3xl p-8 glass-gate text-left shadow-2xl relative overflow-hidden">
            <div class="mb-6">
                <h2 class="text-xl font-black text-white">Sign In to Platform</h2>
                <p class="text-xs text-zinc-400 mt-1">Builders enter your <strong class="text-purple-300">Member ID</strong> and assigned password. Chapter Leads use administrator credentials.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="mb-6 rounded-2xl border border-red-500/20 bg-red-500/10 p-3.5 text-xs text-red-400 font-bold">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="unified_login">
                <div>
                    <label class="block text-[9px] font-black uppercase text-zinc-400 tracking-wider mb-1.5">Member ID / Admin Email</label>
                    <input type="text" name="username" required placeholder="e.g. SBG-001 or admin@sbg.bzu" class="w-full rounded-2xl border border-white/10 bg-black/40 px-4 py-3 text-xs text-white outline-none focus:border-purple-500 placeholder:text-zinc-600 font-medium">
                </div>
                <div>
                    <label class="block text-[9px] font-black uppercase text-zinc-400 tracking-wider mb-1.5">Password</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full rounded-2xl border border-white/10 bg-black/40 px-4 py-3 text-xs text-white outline-none focus:border-purple-500 placeholder:text-zinc-600">
                </div>

                <button type="submit" class="w-full rounded-full bg-purple-600 hover:bg-purple-500 py-3.5 text-xs font-black uppercase tracking-wider text-white transition-all shadow-lg shadow-purple-600/25 cursor-pointer mt-2">
                    Authenticate Account →
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-white/10 flex items-center justify-between text-xs">
                <span class="text-zinc-500 font-medium">Just exploring?</span>
                <a href="login.php?action=guest" class="rounded-full border border-white/10 bg-white/5 px-4 py-1.5 text-[10px] font-black uppercase tracking-wider text-zinc-300 hover:bg-white/10 transition-all">
                    Enter as Guest →
                </a>
            </div>
        </div>

        <p class="mt-8 text-[10px] uppercase tracking-widest text-zinc-600">
            All Rights Reserved © 2026 • AWS Student Builder Group, BZU
        </p>
    </div>

</body>
</html>
