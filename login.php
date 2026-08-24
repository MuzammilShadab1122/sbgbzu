<?php
// login.php
require_once 'includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'admin_login') {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        
        if (login_admin($email, $password)) {
            header('Location: admin.php');
            exit;
        } else {
            $error = 'Invalid admin email or password.';
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
            background: rgba(18, 10, 32, 0.5);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.08);
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

    <div class="relative z-10 w-full max-w-4xl text-center">
        <!-- Brand Logo -->
        <div class="mb-6 flex justify-center">
            <img src="public/images/sbg-logo.png" alt="AWS SBG BZU logo" class="h-28 w-28 rounded-full border border-white/10 shadow-2xl object-cover">
        </div>

        <h1 class="text-4xl sm:text-6xl font-black tracking-tight leading-none mb-3 text-white font-outfit">
            AWS <span class="text-glow-gradient">Student Builder</span> Group, BZU
        </h1>
        <p class="mx-auto max-w-xl text-sm leading-relaxed text-zinc-400 mb-12">
            Select your access credentials to enter the official learning platform.
        </p>

        <!-- Access Choice Cards -->
        <div class="grid gap-6 sm:grid-cols-2 text-left">
            
            <!-- Guest Portal -->
            <div class="flex flex-col justify-between rounded-3xl p-8 glass-gate hover:border-violet-500/40 hover:-translate-y-1 hover:shadow-2xl hover:shadow-violet-500/15 transition-all duration-300 group">
                <div>
                    <!-- Badge Icon -->
                    <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-500/10 border border-violet-500/20 text-violet-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0zM21 21l-4.35-4.35" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-2">Guest Access</h3>
                    <p class="text-sm leading-relaxed text-zinc-400">
                        Explore BZU's first AWS community directory, view team points, blog posts, and active leaderboards.
                    </p>
                </div>
                <a href="login.php?action=guest" class="mt-8 block text-center rounded-full border border-white/20 bg-white/10 px-5 py-3 text-xs font-black uppercase tracking-widest text-white hover:bg-white/20 transition-all">
                    Enter as Guest →
                </a>
            </div>

            <!-- Admin Portal -->
            <div class="flex flex-col justify-between rounded-3xl p-8 glass-gate hover:border-violet-500/40 hover:-translate-y-1 hover:shadow-2xl hover:shadow-violet-500/15 transition-all duration-300 <?php echo isset($_GET['view']) && $_GET['view'] === 'admin' || !empty($error) ? '' : 'justify-between'; ?>">
                <div>
                    <!-- Badge Icon -->
                    <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-500/10 border border-violet-500/20 text-violet-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-2">Lead Login</h3>
                    <p class="text-sm leading-relaxed text-zinc-400 mb-5">
                        Authorized configuration portal for SBG chapter leads to announce highlights.
                    </p>
                    
                    <!-- Admin login form -->
                    <?php if (isset($_GET['view']) && $_GET['view'] === 'admin' || !empty($error)): ?>
                        <form action="login.php" method="POST" class="space-y-3 mt-4">
                            <input type="hidden" name="action" value="admin_login">
                            <input type="email" name="email" required placeholder="Email address" class="w-full rounded-full border border-white/10 bg-black/40 px-4 py-2.5 text-xs text-white outline-none focus:border-violet-500 placeholder:text-zinc-550">
                            <input type="password" name="password" required placeholder="Password" class="w-full rounded-full border border-white/10 bg-black/40 px-4 py-2.5 text-xs text-white outline-none focus:border-violet-500 placeholder:text-zinc-550">
                            
                            <?php if (!empty($error)): ?>
                                <p class="text-xs text-red-400 font-semibold bg-red-500/10 border border-red-500/20 px-3 py-2 rounded-xl">
                                    <?php echo $error; ?>
                                </p>
                            <?php endif; ?>

                            <div class="flex gap-2 pt-2">
                                <button type="submit" class="flex-grow rounded-full bg-violet-600 hover:bg-violet-500 py-2.5 text-xs font-black uppercase tracking-wider text-white transition-all cursor-pointer">
                                    Verify credentials
                                </button>
                                <a href="login.php" class="rounded-full border border-white/10 hover:bg-white/5 px-4 py-2.5 text-xs font-bold text-zinc-400 text-center">
                                    Back
                                </a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>

                <?php if (!isset($_GET['view']) || ($_GET['view'] !== 'admin' && empty($error))): ?>
                    <a href="login.php?view=admin" class="mt-8 block text-center rounded-full bg-violet-600 px-5 py-3 text-xs font-black uppercase tracking-widest text-white hover:bg-violet-500 transition-all">
                        Admin Sign In →
                    </a>
                <?php endif; ?>
            </div>

        </div>

        <p class="mt-12 text-[10px] uppercase tracking-widest text-zinc-650">
            All Rights Reserved © 2026 • AWS Student Builder Group, BZU
        </p>
    </div>

</body>
</html>
