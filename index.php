<?php
// index.php
require_once 'includes/header.php';

// Find Star of the Month details
$star_member = null;
if (isset($highlights['starOfMonthId'])) {
    foreach ($participants as $p) {
        if ($p['id'] == $highlights['starOfMonthId']) {
            $star_member = $p;
            break;
        }
    }
}

// Find Monthly Grinders details
$grinders = [];
if (isset($highlights['monthlyGrinders'])) {
    foreach ($highlights['monthlyGrinders'] as $gid) {
        foreach ($participants as $p) {
            if ($p['id'] == $gid) {
                $grinders[] = $p;
                break;
            }
        }
    }
}

// Calculate some insights
$total_points = 0;
$team_set = [];
foreach ($participants as $p) {
    $total_points += $p['points'];
    $team_set[$p['team']] = true;
}
$active_teams = count($team_set);

$stat_rows = [
    [
        'label' => 'Student Builders',
        'value' => count($participants),
        'max' => 40,
        'color' => 'from-purple-500 to-fuchsia-500',
        'display' => count($participants) . '+'
    ],
    [
        'label' => 'Monthly Grinders',
        'value' => count($grinders),
        'max' => 5,
        'color' => 'from-purple-500 to-indigo-500',
        'display' => count($grinders)
    ],
    [
        'label' => 'Partners Collaboration',
        'value' => 4,
        'max' => 10,
        'color' => 'from-indigo-400 to-purple-300',
        'display' => '4'
    ],
    [
        'label' => 'Campus Chapters',
        'value' => 2,
        'max' => 5,
        'color' => 'from-purple-300 to-purple-600',
        'display' => '2'
    ],
    [
        'label' => 'Active Teams',
        'value' => $active_teams,
        'max' => 10,
        'color' => 'from-indigo-400 to-fuchsia-500',
        'display' => $active_teams
    ]
];
?>

<!-- 1. HERO GREETING BLOCK -->
<section class="relative px-4 sm:px-6 pt-12 pb-8 overflow-hidden">
    <div class="mx-auto max-w-[1440px] relative z-10">
        <div class="grid gap-12 lg:grid-cols-12 items-center">
            
            <!-- Left content: Text & Badge -->
            <div class="lg:col-span-7 space-y-6">
                <div class="flex flex-col gap-3">
                    <div class="inline-flex items-center gap-2">
                        <span class="rounded-full bg-purple-500/10 border border-purple-500/20 px-3.5 py-1.5 text-[9px] font-black uppercase tracking-widest text-purple-600 dark:text-purple-350">
                            AWS Student Builder Group • BZU Multan
                        </span>
                    </div>
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="rounded-full bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 px-3 py-1 text-[9px] font-bold text-slate-600 dark:text-zinc-400">
                            First Ever SBG Chapter in Multan
                        </span>
                        <span class="rounded-full bg-purple-500/10 border border-purple-500/20 px-3 py-1 text-[9px] font-black text-purple-600 dark:text-purple-350">
                            First Ever Silver Community
                        </span>
                    </div>
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.1] text-slate-900 dark:text-white font-space">
                    Empowering innovation <br class="hidden sm:block">through the power of <span class="text-glow-gradient">AWS.</span>
                </h1>
                <p class="text-xs sm:text-sm leading-relaxed text-slate-650 dark:text-zinc-400 max-w-xl font-medium">
                    A student-led platform for team profiles, leaderboards, and collaboration portfolios. We bridge post-secondary learning with modern cloud infrastructure to build cloud careers.
                </p>
                <div class="flex flex-wrap gap-3 pt-2">
                    <a href="team.php" class="reflective-card rounded-full bg-purple-600 hover:bg-purple-500 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white hover:scale-[1.02] transform transition-all shadow-lg shadow-purple-500/25">
                        Meet the Builders
                    </a>
                    <a href="events.php" class="reflective-card rounded-full border border-slate-300 dark:border-white/10 bg-white/5 hover:bg-white/10 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-700 dark:text-white hover:scale-[1.02] transform transition-all">
                        Explore Events
                    </a>
                </div>
            </div>

            <!-- Right content: Mehdi Hassan (Leader) Photo Block -->
            <div class="lg:col-span-5 flex justify-center">
                <div class="relative">
                    <div class="absolute -inset-6 rounded-full bg-purple-500/10 blur-2xl pointer-events-none"></div>
                    <div class="relative">
                        <!-- rotating border decal -->
                        <div class="absolute -inset-4 rounded-full border-2 border-dashed border-purple-500/20 animate-[spin_35s_linear_infinite] pointer-events-none"></div>
                        <img src="public/images/AWS-MembersPics/Mehdi Hassan.jpeg" alt="Mehdi Hassan" class="h-64 w-64 sm:h-72 sm:w-72 rounded-full border-4 border-slate-200 dark:border-purple-650 object-cover shadow-xl">
                        
                        <!-- Top-right BZU Multan badge -->
                        <div class="absolute -right-4 top-8 rounded-full bg-white border border-slate-200/50 px-4 py-2 flex items-center gap-1.5 shadow-lg z-20">
                            <span class="flex items-center justify-center bg-purple-600 rounded-full w-4 h-4 p-0.5 shadow-sm">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            <span class="text-[10px] sm:text-[11px] font-black tracking-wide text-purple-700">BZU Multan</span>
                        </div>

                        <!-- Left-stacked glassmorphic stickers -->
                        <div class="absolute -left-8 sm:-left-12 top-10 sm:top-14 h-16 w-16 sm:h-20 sm:w-20 rounded-2xl backdrop-blur-xl bg-white/10 dark:bg-white/5 border border-white/20 shadow-lg flex items-center justify-center p-3 z-20 animate-float">
                            <img src="public/images/stickers/cloud.png" alt="Cloud Sticker" class="w-full h-full object-contain">
                        </div>
                        <div class="absolute -left-6 sm:-left-10 top-[110px] sm:top-[160px] h-16 w-16 sm:h-20 sm:w-20 rounded-2xl backdrop-blur-xl bg-white/10 dark:bg-white/5 border border-white/20 shadow-lg flex items-center justify-center p-3.5 z-20 animate-float" style="animation-delay: 2s;">
                            <img src="public/images/stickers/aws.png" alt="AWS Sticker" class="w-full h-full object-contain">
                        </div>

                        <!-- Right-side 1st In Multan floating card -->
                        <div class="absolute -right-8 top-[110px] sm:top-[150px] bg-white border border-slate-200/50 rounded-2xl shadow-xl px-5 py-4 text-center z-20 w-24 sm:w-28">
                            <span class="block text-2xl sm:text-3xl font-black text-fuchsia-600 leading-none">1st</span>
                            <span class="block text-[9px] sm:text-[10px] font-black text-slate-700 uppercase tracking-wider mt-1.5 leading-none">In Multan</span>
                        </div>

                        <!-- Bottom-left Leader pill badge -->
                        <div class="absolute -bottom-4 -left-4 rounded-full bg-gradient-to-r from-purple-600 to-fuchsia-600 px-5 py-2.5 flex items-center gap-1.5 shadow-lg text-white z-20">
                            <svg class="w-3 h-3 fill-current text-white animate-pulse" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span class="text-[10px] font-black uppercase tracking-widest leading-none">Leader</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 2. COMMUNITY BADGES -->
<section class="px-4 sm:px-6 py-6">
    <div class="mx-auto max-w-[1440px]">
        <div class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.02] p-8 shadow-lg neon-glow-active">
            <div class="text-center sm:text-left mb-8">
                <span class="text-[9px] font-black uppercase tracking-[0.2em] text-purple-600 dark:text-purple-400">Official Status Recognition</span>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-space mt-1">Community Recognition Badges</h3>
            </div>
            
            <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <?php foreach ($badges as $badge): ?>
                    <div class="flex flex-col items-center text-center p-6 rounded-2xl bg-slate-50/30 dark:bg-white/[0.01] border border-slate-200/50 dark:border-white/5 hover:border-purple-500/30 transition-all duration-300 group hover:-translate-y-1">
                        <div class="relative w-28 h-28 mb-4 flex items-center justify-center shrink-0">
                            <div class="absolute -inset-4 rounded-full bg-purple-500/10 blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                            <div class="animate-[float_4s_ease-in-out_infinite] w-24 h-24 flex items-center justify-center text-slate-350">
                                <?php 
                                    $isSvg = pathinfo($badge['image'], PATHINFO_EXTENSION) === 'svg';
                                    if ($isSvg && file_exists($badge['image'])) {
                                        echo file_get_contents($badge['image']);
                                    } else {
                                        echo '<img src="' . htmlspecialchars($badge['image']) . '" alt="' . htmlspecialchars($badge['name']) . '" class="w-full h-full object-contain">';
                                    }
                                ?>
                            </div>
                        </div>
                        <h4 class="text-sm font-black text-slate-900 dark:text-white font-space leading-tight mt-2"><?php echo htmlspecialchars($badge['name']); ?></h4>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>


<!-- 2. BUILDERS SPOTLIGHT PANEL (STAR & GRINDERS HIGHLIGHT AT THE TOP) -->
<section class="px-4 sm:px-6 py-4">
    <div class="mx-auto max-w-[1440px]">
        <div class="grid gap-6 lg:grid-cols-12 items-stretch">
            
            <!-- Star of the Month Card (Glow card) -->
            <div class="lg:col-span-7 rounded-3xl p-6 relative overflow-hidden flex flex-col justify-between shadow-lg tier-gold reflective-card transition-all duration-300">
                <!-- Aligned crown badge in top-right corner -->
                <div class="absolute top-5 right-5 h-10 w-10 rounded-2xl bg-amber-500/15 border border-amber-500/30 text-amber-500 shadow-sm flex items-center justify-center shrink-0 pointer-events-none">
                    <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                </div>
                
                <div>
                    <span class="text-[9px] font-black uppercase tracking-[0.28em] text-amber-600 dark:text-amber-400 flex items-center gap-1.5 mb-6">
                        <svg class="h-3 w-3 fill-current text-amber-500 shrink-0" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        STAR OF THE MONTH (<?php echo $highlights['monthLabel']; ?>)
                    </span>
                    
                    <?php if ($star_member): ?>
                        <a href="#" class="member-modal-trigger flex flex-col sm:flex-row gap-5 items-center sm:items-start text-center sm:text-left group"
                           data-id="<?php echo $star_member['id']; ?>"
                           data-name="<?php echo htmlspecialchars($star_member['name']); ?>"
                           data-role="<?php echo htmlspecialchars($star_member['role']); ?>"
                           data-team="<?php echo htmlspecialchars($star_member['team']); ?>"
                           data-level="<?php echo htmlspecialchars($star_member['level']); ?>"
                           data-campus="<?php echo htmlspecialchars($star_member['campus']); ?>"
                           data-points="<?php echo htmlspecialchars($star_member['points']); ?>"
                           data-responsibilities="<?php echo htmlspecialchars($star_member['responsibilities']); ?>"
                           data-img="<?php echo htmlspecialchars($star_member['image']); ?>"
                           data-rank="0">
                            
                            <img src="<?php echo $star_member['image'] ?: 'public/images/AWS-MembersPics/default.png'; ?>" alt="<?php echo $star_member['name']; ?>" class="h-20 w-20 sm:h-24 sm:w-24 rounded-2xl object-cover border border-amber-500 shadow-md group-hover:scale-105 transition-transform duration-300">
                            
                            <div class="min-w-0 flex-grow pt-1">
                                <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white group-hover:text-purple-650 dark:group-hover:text-amber-400 transition-colors font-space"><?php echo $star_member['name']; ?></h3>
                                <p class="text-xs sm:text-sm text-slate-500 dark:text-zinc-400 mt-1"><?php echo $star_member['role']; ?></p>
                                <div class="mt-4 flex flex-wrap gap-1.5 justify-center sm:justify-start">
                                    <span class="rounded-full bg-purple-500/10 border border-purple-500/20 px-2.5 py-0.5 text-[8px] font-black uppercase tracking-widest text-purple-600 dark:text-purple-300">
                                        <?php echo $star_member['team']; ?> Team
                                    </span>
                                    <span class="rounded-full bg-slate-200 dark:bg-white/5 border border-slate-300 dark:border-white/10 px-2.5 py-0.5 text-[8px] font-black uppercase tracking-widest text-slate-700 dark:text-white/80">
                                        <?php echo $star_member['level']; ?>
                                    </span>
                                </div>
                            </div>
                        </a>
                    <?php else: ?>
                        <p class="text-xs font-medium text-zinc-500 italic">Not announced yet.</p>
                    <?php endif; ?>
                </div>

                <div class="mt-6 pt-5 border-t border-slate-200 dark:border-white/5 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <p class="text-xs text-slate-500 dark:text-zinc-400 font-medium">Honoring outstanding commitment to student orientation.</p>
                    <a href="leaderboard.php" class="rounded-full bg-purple-600 hover:bg-purple-500 px-5 py-2.5 text-[9px] font-black uppercase tracking-widest text-white hover:scale-[1.02] transform transition-all shrink-0">
                        View leaderboard →
                    </a>
                </div>
            </div>

            <!-- Monthly Grinders Card -->
            <div class="lg:col-span-5 rounded-3xl p-6 flex flex-col justify-between shadow-lg tier-silver reflective-card transition-all duration-300">
                <div>
                    <span class="text-[9px] font-black uppercase tracking-[0.28em] text-purple-600 dark:text-purple-300 flex items-center gap-1.5 mb-5">
                        <svg class="h-3.5 w-3.5 text-purple-500 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94.63 1.5 1.98 2.63 3.61 2.96V19H7v2h10v-2h-4v-3.1c1.63-.33 2.98-1.46 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/></svg>
                        MONTHLY GRINDERS LIST
                    </span>
                    
                    <div class="grid gap-2.5">
                        <?php foreach ($grinders as $idx => $g): ?>
                            <a href="#" class="member-modal-trigger flex items-center gap-3 rounded-2xl border border-slate-200 dark:border-white/5 bg-slate-50/50 dark:bg-white/[0.01] px-4 py-3 hover:bg-slate-100 dark:hover:bg-white/[0.06] transition-all duration-300 group"
                               data-id="<?php echo $g['id']; ?>"
                               data-name="<?php echo htmlspecialchars($g['name']); ?>"
                               data-role="<?php echo htmlspecialchars($g['role']); ?>"
                               data-team="<?php echo htmlspecialchars($g['team']); ?>"
                               data-level="<?php echo htmlspecialchars($g['level']); ?>"
                               data-campus="<?php echo htmlspecialchars($g['campus']); ?>"
                               data-points="<?php echo htmlspecialchars($g['points']); ?>"
                               data-responsibilities="<?php echo htmlspecialchars($g['responsibilities']); ?>"
                               data-img="<?php echo htmlspecialchars($g['image']); ?>"
                               data-rank="0">
                                
                                <span class="text-xs font-black text-purple-500 dark:text-purple-550/50 w-4"><?php echo $idx + 1; ?></span>
                                <img src="<?php echo $g['image'] ?: 'public/images/AWS-MembersPics/default.png'; ?>" class="h-10 w-10 rounded-xl object-cover border border-slate-200 dark:border-white/10" alt="">
                                <div class="min-w-0 flex-grow">
                                    <p class="truncate text-sm font-bold text-slate-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors"><?php echo $g['name']; ?></p>
                                    <p class="truncate text-[9px] text-slate-505 dark:text-zinc-550 mt-0.5"><?php echo $g['role']; ?></p>
                                </div>
                                <span class="text-sm font-black text-slate-900 dark:text-white"><?php echo number_format($g['points']); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="pt-5 border-t border-slate-200 dark:border-white/5 mt-6">
                    <p class="text-[9px] text-slate-450 dark:text-zinc-500 uppercase tracking-widest text-center">Top grinders updated weekly</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 3. HORIZONTAL DYNAMIC INSIGHT STATS (MOBILE-FRIENDLY SWIPE DECK) -->
<section class="px-4 sm:px-6 py-4">
    <div class="mx-auto max-w-[1440px]">
        <div class="flex items-center justify-between mb-4">
            <span class="text-[10px] font-black uppercase tracking-[0.24em] text-purple-600 dark:text-purple-400"> Ecosystem Metrics</span>
            <span class="text-[9px] font-bold text-slate-450 uppercase tracking-wider hidden sm:inline-block">Swipe horizontally ↔</span>
        </div>
        
        <!-- Swipeable Row deck (WITH VERTICAL ROOM FOR SMOOTH HOVER TRANSITION) -->
        <div class="flex gap-4 overflow-x-auto snap-x snap-mandatory no-scrollbar py-3 px-1">
            <?php foreach ($stat_rows as $row): 
                $percentage = min(100, ($row['value'] / $row['max']) * 100);
            ?>
                <div class="snap-start shrink-0 w-[240px] sm:w-[280px] rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.02] p-5 shadow-md relative overflow-hidden hover-glow-card transition-all duration-300 ease-out">
                    <div class="flex justify-between items-end mb-4">
                        <span class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest"><?php echo $row['label']; ?></span>
                        <span class="text-xl font-black text-slate-900 dark:text-white"><?php echo $row['display']; ?></span>
                    </div>
                    <!-- custom progress bar inside glass panel -->
                    <div class="h-1.5 w-full rounded-full bg-slate-100 dark:bg-white/10 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r <?php echo $row['color']; ?>" style="width: <?php echo $percentage; ?>%;"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 4. COMMUNITY LEADERSHIP & VISION -->
<section class="relative overflow-hidden px-4 sm:px-6 py-12">
    <div class="mx-auto max-w-[1440px]">
        <div class="grid gap-12 lg:grid-cols-2 items-center">
            
            <!-- Tech Illustration (Aligned to Left) -->
            <div class="flex justify-start order-2 lg:order-1">
                <div class="relative w-full max-w-lg">
                    <!-- Glowing backgrounds -->
                    <div class="absolute -inset-4 rounded-3xl bg-purple-500/10 blur-xl pointer-events-none"></div>
                    <div class="relative rounded-3xl border border-slate-200 dark:border-white/10 overflow-hidden bg-slate-950 shadow-2xl">
                        <img src="public/images/cloud_architecture_illustration.png" alt="AWS Cloud Infrastructure Illustration" class="w-full h-auto object-cover opacity-90 hover:scale-105 transition-transform duration-500">
                    </div>
                </div>
            </div>

            <!-- Vision text block -->
            <div class="order-1 lg:order-2 space-y-6">
                <div>
                    <span class="text-[9px] font-black uppercase tracking-[0.28em] text-purple-600 dark:text-purple-400">Chapter Vision</span>
                    <h2 class="mt-2 text-3xl sm:text-5xl font-black text-slate-900 dark:text-white leading-tight font-space">First <span class="text-glow-gradient">AWS Student Builder</span> Chapter in Multan</h2>
                    <p class="mt-4 text-sm sm:text-base md:text-lg leading-relaxed text-slate-650 dark:text-zinc-350 font-semibold">
                        Bridging post-secondary learning with modern cloud infrastructure. Our chapter fosters builder collaboration, and prepares students for cloud careers.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.01] p-6 shadow-sm hover-glow-card">
                    <h4 class="text-base sm:text-lg font-black text-slate-900 dark:text-white mb-3 font-space flex items-center gap-2">
                        <svg class="h-5 w-5 text-purple-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z" /></svg>
                        Real Hands-on Development
                    </h4>
                    <p class="text-xs sm:text-sm md:text-base text-slate-600 dark:text-zinc-350 leading-relaxed font-semibold">
                        We skip tutorial-only approaches to construct production-ready configurations using S3, CloudFormation, Cognito, and API Gateway.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 5. LATEST BLOG NOTICES (BUTTONS COMPLETELY REMOVED!) -->
<section class="px-4 sm:px-6 py-12">
    <div class="mx-auto max-w-[1440px]">
        <div class="flex items-end justify-between border-b border-slate-200 dark:border-white/10 pb-5 mb-8">
            <div>
                <p class="text-[9px] font-black uppercase tracking-[0.28em] text-purple-600 dark:text-purple-400">Bulletin Updates</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white font-space">Latest Notices</h3>
            </div>
            <a href="blog.php" class="text-xs font-bold text-purple-600 dark:text-purple-300 hover:underline">
                All announcements →
            </a>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php
            $notices = array_slice($posts, 0, 3);
            foreach ($notices as $post):
            ?>
                <article class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.02] p-6 shadow-sm hover-glow-card">
                    <div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-purple-600 dark:text-purple-400">
                            <?php echo $post['category']; ?> / <?php echo $post['date']; ?>
                        </span>
                        <h4 class="text-lg font-black text-slate-900 dark:text-white font-space mt-3 mb-2 leading-tight"><?php echo $post['title']; ?></h4>
                        <p class="text-xs text-slate-600 dark:text-zinc-400 leading-relaxed font-medium">
                            <?php echo $post['excerpt']; ?>
                        </p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 6. PARTNERS MARQUEE LOOP (AT THE BOTTOM) -->
<section class="border-t border-slate-200 dark:border-white/10 py-12 overflow-hidden bg-slate-100/50 dark:bg-black/20">
    <div class="mx-auto max-w-[1440px] px-4 sm:px-6">
        <div class="mb-6">
            <p class="text-center text-[10px] font-black uppercase tracking-[0.24em] text-slate-500">Campus Chapter Partners</p>
        </div>
        
        <!-- Marquee loop container (CONSTRAINED TO SITE WIDTH) -->
        <div class="w-full overflow-hidden relative py-4 rounded-3xl">
            <div class="flex gap-6 w-max animate-marquee items-center">
                <?php
                if (!empty($partners)) {
                    // Loop array duplication to ensure seamless visual loop
                    $marquee_loop = array_merge($partners, $partners, $partners);
                    foreach ($marquee_loop as $partner):
                    ?>
                        <div class="flex items-center gap-4 rounded-3xl border border-slate-200 dark:border-white/10 bg-white/90 dark:bg-white/[0.03] px-6 py-4 min-w-[280px] sm:min-w-[320px] shrink-0 shadow-md backdrop-blur-md hover:border-purple-500/40 transition-all">
                            <div class="h-12 w-12 sm:h-14 sm:w-14 rounded-2xl bg-white dark:bg-white p-2 flex items-center justify-center shrink-0 border border-slate-200/80 dark:border-white/20 shadow-sm">
                                <img src="<?php echo htmlspecialchars($partner['logo']); ?>" alt="<?php echo htmlspecialchars($partner['name']); ?>" class="max-h-full max-w-full object-contain">
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm sm:text-base font-black text-slate-900 dark:text-white font-space truncate"><?php echo htmlspecialchars($partner['name']); ?></p>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400 mt-0.5">Official Partner</p>
                            </div>
                        </div>
                    <?php 
                    endforeach;
                } else {
                    echo '<p class="text-xs text-slate-500 italic py-2 text-center w-full">No active partners listed.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>
