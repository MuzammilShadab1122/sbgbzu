<?php
// leaderboard.php
require_once 'includes/header.php';

// Include all active participants including team leads
$active_participants = $participants;

// Sort active by points descending
usort($active_participants, function($a, $b) {
    return $b['points'] <=> $a['points'];
});

// Segment Top 3 podium
$podium = array_slice($active_participants, 0, 3);
$rest_rankings = array_slice($active_participants, 3);
?>

<div class="mx-auto max-w-[1440px] px-4 sm:px-6 pb-24 pt-8 lg:px-8">
    <!-- Breadcrumbs -->
    <div class="mb-4 text-[9px] font-black uppercase tracking-[0.24em] text-slate-400 dark:text-zinc-400">
        <span>AWS Student Builders</span>
        <span class="mx-2">/</span>
        <span class="text-purple-650 dark:text-purple-400">Leaderboard Scoreboard</span>
    </div>

    <!-- Page Header -->
    <div class="mb-10">
        <p class="text-[10px] font-black uppercase tracking-[0.28em] text-purple-605 dark:text-purple-405">Gamified Scoreboard</p>
        <h2 class="mt-2 text-3xl sm:text-5xl font-black text-slate-900 dark:text-white leading-none font-space"><span class="text-glow-gradient">Top Builders</span> Leaderboard</h2>
        <p class="mt-4 max-w-xl text-xs sm:text-sm leading-relaxed text-slate-500 dark:text-zinc-400 font-medium">Search members, track rankings, and explore monthly contributions based on active cloud milestones.</p>
    </div>

    <!-- TOP 3 PODIUM (RESPONSIVE GLASS CARDS) -->
    <section class="mb-12">
        <div class="text-center mb-6">
            <h3 class="text-[10px] font-black uppercase tracking-[0.26em] text-purple-605 dark:text-purple-400 flex items-center justify-center gap-1.5">
                <svg class="h-3.5 w-3.5 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94.63 1.5 1.98 2.63 3.61 2.96V19H7v2h10v-2h-4v-3.1c1.63-.33 2.98-1.46 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/></svg>
                Podium Standings
            </h3>
        </div>
        
        <!-- On mobile: vertical stack. On desktop: side-by-side with 1st place in center & scaled -->
        <div class="flex flex-col md:flex-row md:items-end gap-5 max-w-4xl mx-auto justify-center">
            
            <!-- 2nd Place (Silver) - Rendered first on desktop, second on mobile -->
            <?php if (isset($podium[1])): 
                $p = $podium[1];
            ?>
                <a href="#" class="member-modal-trigger order-2 md:order-1 w-full md:w-1/3 group flex flex-col items-center rounded-3xl p-6 tier-silver reflective-card relative text-center"
                   data-id="<?php echo $p['id']; ?>"
                   data-name="<?php echo htmlspecialchars($p['name']); ?>"
                   data-role="<?php echo htmlspecialchars($p['role']); ?>"
                   data-team="<?php echo htmlspecialchars($p['team']); ?>"
                   data-level="<?php echo htmlspecialchars($p['level']); ?>"
                   data-campus="<?php echo htmlspecialchars($p['campus']); ?>"
                   data-points="<?php echo htmlspecialchars($p['points']); ?>"
                   data-responsibilities="<?php echo htmlspecialchars($p['responsibilities']); ?>"
                   data-img="<?php echo htmlspecialchars($p['image']); ?>"
                   data-rank="2">
                    
                    <span class="absolute top-4 left-4 flex h-7 w-7 items-center justify-center rounded-full bg-slate-200 dark:bg-zinc-700 text-[10px] font-black text-slate-800 dark:text-zinc-300">
                        02
                    </span>
                    <img src="<?php echo $p['image'] ?: 'public/images/AWS-MembersPics/default.png'; ?>" class="h-16 w-16 rounded-2xl object-cover border-2 border-slate-350 dark:border-zinc-500 shadow-md mb-3" alt="">
                    <h4 class="text-base font-black text-slate-900 dark:text-white group-hover:text-purple-650 dark:group-hover:text-purple-450 transition-colors font-space truncate w-full"><?php echo $p['name']; ?></h4>
                    <p class="text-[10px] text-slate-550 dark:text-zinc-450 truncate w-full"><?php echo $p['role']; ?></p>
                    <p class="mt-3 text-lg font-black text-slate-700 dark:text-zinc-300 leading-none"><?php echo number_format($p['points'], 2); ?> <span class="text-[10px] font-bold uppercase">PTS</span></p>
                </a>
            <?php endif; ?>

            <!-- 1st Place (Gold) - Rendered center on desktop, first on mobile -->
            <?php if (isset($podium[0])): 
                $p = $podium[0];
            ?>
                <a href="#" class="member-modal-trigger order-1 md:order-2 w-full md:w-[36%] group flex flex-col items-center rounded-3xl p-7 relative text-center md:scale-105 z-10 animate-float tier-gold reflective-card"
                   data-id="<?php echo $p['id']; ?>"
                   data-name="<?php echo htmlspecialchars($p['name']); ?>"
                   data-role="<?php echo htmlspecialchars($p['role']); ?>"
                   data-team="<?php echo htmlspecialchars($p['team']); ?>"
                   data-level="<?php echo htmlspecialchars($p['level']); ?>"
                   data-campus="<?php echo htmlspecialchars($p['campus']); ?>"
                   data-points="<?php echo htmlspecialchars($p['points']); ?>"
                   data-responsibilities="<?php echo htmlspecialchars($p['responsibilities']); ?>"
                   data-img="<?php echo htmlspecialchars($p['image']); ?>"
                   data-rank="1">
                    
                    <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 flex items-center justify-center h-7 w-7 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-500 shadow-md backdrop-blur-md">
                        <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                    </span>
                    <span class="absolute top-4 left-4 flex h-7 w-7 items-center justify-center rounded-full bg-amber-500 text-[10px] font-black text-black shadow-sm">
                        01
                    </span>
                    <img src="<?php echo $p['image'] ?: 'public/images/AWS-MembersPics/default.png'; ?>" class="h-20 w-20 rounded-2xl object-cover border-2 border-amber-500 shadow-lg mb-3" alt="">
                    <h4 class="text-lg font-black text-slate-900 dark:text-white group-hover:text-purple-650 dark:group-hover:text-purple-450 transition-colors font-space truncate w-full"><?php echo $p['name']; ?></h4>
                    <p class="text-xs text-amber-600 dark:text-amber-300 font-bold truncate w-full"><?php echo $p['role']; ?></p>
                    <p class="mt-3 text-2xl font-black text-amber-605 dark:text-amber-400 leading-none"><?php echo number_format($p['points'], 2); ?> <span class="text-xs font-bold uppercase">PTS</span></p>
                </a>
            <?php endif; ?>

            <!-- 3rd Place (Bronze) - Rendered third on desktop and mobile -->
            <?php if (isset($podium[2])): 
                $p = $podium[2];
            ?>
                <a href="#" class="member-modal-trigger order-3 w-full md:w-1/3 group flex flex-col items-center rounded-3xl p-6 tier-bronze reflective-card relative text-center"
                   data-id="<?php echo $p['id']; ?>"
                   data-name="<?php echo htmlspecialchars($p['name']); ?>"
                   data-role="<?php echo htmlspecialchars($p['role']); ?>"
                   data-team="<?php echo htmlspecialchars($p['team']); ?>"
                   data-level="<?php echo htmlspecialchars($p['level']); ?>"
                   data-campus="<?php echo htmlspecialchars($p['campus']); ?>"
                   data-points="<?php echo htmlspecialchars($p['points']); ?>"
                   data-responsibilities="<?php echo htmlspecialchars($p['responsibilities']); ?>"
                   data-img="<?php echo htmlspecialchars($p['image']); ?>"
                   data-rank="3">
                    
                    <span class="absolute top-4 left-4 flex h-7 w-7 items-center justify-center rounded-full bg-amber-700 text-[10px] font-black text-white">
                        03
                    </span>
                    <img src="<?php echo $p['image'] ?: 'public/images/AWS-MembersPics/default.png'; ?>" class="h-16 w-16 rounded-2xl object-cover border-2 border-amber-700 shadow-md mb-3" alt="">
                    <h4 class="text-base font-black text-slate-900 dark:text-white group-hover:text-purple-650 dark:group-hover:text-purple-450 transition-colors font-space truncate w-full"><?php echo $p['name']; ?></h4>
                    <p class="text-[10px] text-slate-550 dark:text-zinc-450 truncate w-full"><?php echo $p['role']; ?></p>
                    <p class="mt-3 text-lg font-black text-amber-700 dark:text-amber-650 leading-none"><?php echo number_format($p['points'], 2); ?> <span class="text-[10px] font-bold uppercase">PTS</span></p>
                </a>
            <?php endif; ?>

        </div>
    </section>

    <!-- LIVE SEARCHABLE LIST SECTION -->
    <section class="mt-12">
        <!-- Search Filtering Bar -->
        <div class="flex flex-col gap-4 border-b border-slate-200 dark:border-white/10 pb-5 mb-6 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-lg font-black text-slate-900 dark:text-white font-space">Rankings Scoreboard (<?php echo count($active_participants); ?>)</p>
            <input
                id="leaderboard-search"
                type="text"
                placeholder="Search name, team, track..."
                class="w-full rounded-full border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 px-5 py-3 text-sm text-slate-900 dark:text-white outline-none focus:border-purple-500 focus:shadow-[0_0_15px_rgba(168,85,247,0.25)] placeholder:text-slate-400 dark:placeholder:text-zinc-500 transition-all sm:max-w-sm"
            >
        </div>

        <!-- Leaderboard Table List (MOBILE-FIRST COMPACT FLEX ROW OVERHAUL) -->
        <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.01] shadow-sm">
            <div class="divide-y divide-slate-100 dark:divide-white/5">
                <?php
                // Logical Bug Fix: Loop over $rest_rankings instead of $active_participants to prevent duplicating the Gold, Silver, Bronze podium builders!
                if (empty($rest_rankings)):
                ?>
                    <div class="p-6 text-center text-slate-500">
                        No additional rankings.
                    </div>
                <?php
                else:
                    foreach ($rest_rankings as $idx => $p):
                        $rank = $idx + 4; // Podium occupies ranks 1, 2, and 3
                        $pts = floatval($p['points']);
                        
                        if ($pts >= 4000) {
                            $tier_row_class = 'border-l-4 border-l-cyan-400/80 dark:border-l-cyan-400';
                            $rank_color = 'text-cyan-500 dark:text-cyan-400 font-black';
                        } elseif ($pts >= 2500) {
                            $tier_row_class = 'border-l-4 border-l-amber-500/70 dark:border-l-amber-500/90';
                            $rank_color = 'text-amber-500 font-extrabold';
                        } elseif ($pts >= 1000) {
                            $tier_row_class = 'border-l-4 border-l-slate-400/60 dark:border-l-slate-400/80';
                            $rank_color = 'text-slate-450 dark:text-zinc-350 font-bold';
                        } else {
                            $tier_row_class = 'border-l-4 border-l-amber-700/40 dark:border-l-amber-700/60';
                            $rank_color = 'text-slate-400 dark:text-zinc-550';
                        }
                        
                        $search_string = implode(' ', [$p['name'], $p['role'], $p['team'], $p['campus'], $p['level']]);
                    ?>
                        <a href="#" class="leaderboard-row member-modal-trigger flex items-center justify-between gap-3 px-4 sm:px-6 py-4 rounded-2xl hover:bg-slate-100 dark:hover:bg-white/[0.05] hover:scale-[1.01] hover:border-purple-500/20 border border-transparent transition-all duration-200 text-left <?php echo $tier_row_class; ?>"
                           data-search="<?php echo htmlspecialchars($search_string); ?>"
                           data-id="<?php echo $p['id']; ?>"
                           data-name="<?php echo htmlspecialchars($p['name']); ?>"
                           data-role="<?php echo htmlspecialchars($p['role']); ?>"
                           data-team="<?php echo htmlspecialchars($p['team']); ?>"
                           data-level="<?php echo htmlspecialchars($p['level']); ?>"
                           data-campus="<?php echo htmlspecialchars($p['campus']); ?>"
                           data-points="<?php echo htmlspecialchars($p['points']); ?>"
                           data-responsibilities="<?php echo htmlspecialchars($p['responsibilities']); ?>"
                           data-img="<?php echo htmlspecialchars($p['image']); ?>"
                           data-rank="<?php echo $rank; ?>">
                            
                            <!-- Left Block: Rank Index & Member Info -->
                            <div class="flex items-center gap-3.5 min-w-0">
                                <!-- Medal Rank -->
                                <span class="text-xl sm:text-2xl font-black tracking-tight shrink-0 w-8 text-center <?php echo $rank_color; ?>">
                                    <?php echo $rank; ?>
                                </span>
                                <!-- Member Avatar -->
                                <img src="<?php echo $p['image'] ?: 'public/images/AWS-MembersPics/default.png'; ?>" alt="<?php echo $p['name']; ?>" class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl object-cover border border-slate-200 dark:border-white/10 shrink-0">
                                <!-- Text Details -->
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm font-black text-slate-900 dark:text-white truncate font-space"><?php echo $p['name']; ?></p>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-purple-600 dark:text-purple-400 block mt-0.5">
                                        <?php echo $p['team']; ?> Team
                                    </span>
                                </div>
                            </div>

                            <!-- Right Block: Score Points -->
                            <div class="text-right shrink-0">
                                <p class="text-sm sm:text-base font-black text-slate-900 dark:text-white"><?php echo number_format($p['points'], 2); ?></p>
                                <p class="text-[8px] font-bold uppercase text-slate-450 dark:text-zinc-500 tracking-wider">PTS</p>
                            </div>
                        </a>
                    <?php 
                    endforeach; 
                endif;
                ?>
            </div>
        </div>
    </section>
</div>

<?php
require_once 'includes/footer.php';
?>
