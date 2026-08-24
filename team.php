<?php
// team.php
require_once 'includes/header.php';

// Group leads vs other members
$core_members = get_team_members('Core', $participants);
$leader_spotlight = null;
$other_leads = [];

foreach ($core_members as $c) {
    if ($c['level'] === 'Lead') {
        $leader_spotlight = $c;
    } else {
        $other_leads[] = $c;
    }
}

if (!function_exists('get_team_badge_class')) {
    function get_team_badge_class($team_name) {
        switch ($team_name) {
            case 'Core':
                return 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20';
            case 'Technical':
                return 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20';
            case 'Media & Design':
                return 'bg-fuchsia-500/10 text-fuchsia-600 dark:text-fuchsia-400 border border-fuchsia-500/20';
            case 'Marketing':
                return 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20';
            case 'Events':
                return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-450 border border-emerald-505/20';
            case 'Operations':
                return 'bg-violet-500/10 text-violet-600 dark:text-violet-400 border border-violet-500/20';
            default:
                return 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20';
        }
    }
}

if (!function_exists('get_member_tier_class')) {
    function get_member_tier_class($points) {
        $pts = floatval($points);
        if ($pts >= 60) {
            return 'tier-gold';
        } elseif ($pts >= 40) {
            return 'tier-silver';
        } else {
            return 'tier-bronze';
        }
    }
}
?>

<div class="mx-auto max-w-7xl px-4 sm:px-6 pb-24 pt-8 lg:px-8">
    <!-- Breadcrumbs -->
    <div class="mb-4 text-[9px] font-black uppercase tracking-[0.24em] text-slate-400 dark:text-zinc-400">
        <span>AWS Student Builders</span>
        <span class="mx-2">/</span>
        <span class="text-purple-650 dark:text-purple-400">Our Teams</span>
    </div>

    <!-- Page Header -->
    <div class="mb-10">
        <p class="text-[10px] font-black uppercase tracking-[0.28em] text-purple-605 dark:text-purple-405">Our Ambassadors</p>
        <h2 class="mt-2 text-3xl sm:text-5xl font-black text-slate-900 dark:text-white leading-none font-space">Meet the <span class="text-glow-gradient">Chapter Leadership & Teams</span></h2>
        <p class="mt-4 max-w-2xl text-xs sm:text-sm leading-relaxed text-slate-550 dark:text-zinc-400 font-medium">A comprehensive directory of our student builder chapter organized by committees, skills, and technical contributions.</p>
    </div>

    <!-- CORE / LEADERSHIP SECTION -->
    <section class="mt-10">
        <div class="relative overflow-hidden rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.02] p-8 shadow-md">
            <!-- Subtle tint glow -->
            <div class="pointer-events-none absolute inset-0 opacity-40 bg-[radial-gradient(1200px_220px_at_20%_10%,rgba(139,92,246,0.12),transparent_60%)]"></div>

            <div class="relative z-10 flex items-end justify-between gap-6">
                <div>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white font-space">Core Leadership Team</h3>
                    <p class="mt-1 text-xs sm:text-sm text-slate-500 dark:text-zinc-400 font-medium">Chapter leaders and core committee organizers.</p>
                </div>
                <span class="rounded-full px-4 py-2 text-[10px] font-black uppercase tracking-widest shrink-0 <?php echo get_team_badge_class('Core'); ?>">
                    <?php echo count($core_members); ?> members
                </span>
            </div>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <!-- Lead Spotlight Card -->
            <?php if ($leader_spotlight): ?>
                <a href="#" class="member-modal-trigger group relative overflow-hidden rounded-3xl p-6 tier-gold reflective-card shadow-lg flex items-start gap-5"
                   data-id="<?php echo $leader_spotlight['id']; ?>"
                   data-name="<?php echo htmlspecialchars($leader_spotlight['name']); ?>"
                   data-role="<?php echo htmlspecialchars($leader_spotlight['role']); ?>"
                   data-team="<?php echo htmlspecialchars($leader_spotlight['team']); ?>"
                   data-level="<?php echo htmlspecialchars($leader_spotlight['level']); ?>"
                   data-campus="<?php echo htmlspecialchars($leader_spotlight['campus']); ?>"
                   data-points="<?php echo htmlspecialchars($leader_spotlight['points']); ?>"
                   data-responsibilities="<?php echo htmlspecialchars($leader_spotlight['responsibilities']); ?>"
                   data-img="<?php echo htmlspecialchars($leader_spotlight['image']); ?>"
                   data-rank="0">
                    
                    <div class="pointer-events-none absolute -inset-24 opacity-0 group-hover:opacity-100 transition-opacity duration-500 bg-[radial-gradient(closest-side,rgba(139,92,246,0.2),rgba(217,70,239,0.1),transparent_70%)]"></div>
                    
                    <img src="<?php echo $leader_spotlight['image'] ?: 'public/images/AWS-MembersPics/default.png'; ?>" alt="<?php echo $leader_spotlight['name']; ?>" class="h-20 w-20 sm:h-24 sm:w-24 rounded-2xl object-cover border border-slate-200 dark:border-white/10 shadow-md">
                    
                    <div class="min-w-0 flex-grow relative z-10">
                        <p class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white group-hover:text-purple-650 dark:group-hover:text-purple-400 transition-colors font-space"><?php echo $leader_spotlight['name']; ?></p>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-zinc-400 mt-1"><?php echo $leader_spotlight['role']; ?></p>
                        <div class="mt-4 flex flex-wrap gap-1.5">
                            <span class="rounded-full bg-purple-500/10 border border-purple-500/20 px-2.5 py-0.5 text-[8px] font-black uppercase tracking-widest text-purple-650 dark:text-purple-305">
                                <?php echo $leader_spotlight['team']; ?>
                            </span>
                            <span class="rounded-full bg-slate-100 dark:bg-white/5 border border-slate-250 dark:border-white/10 px-2.5 py-0.5 text-[8px] font-black uppercase tracking-widest text-slate-700 dark:text-white/80">
                                <?php echo $leader_spotlight['level']; ?>
                            </span>
                        </div>
                    </div>
                </a>
            <?php endif; ?>

            <!-- Other core leaders list -->
            <div class="grid gap-3">
                <?php foreach ($other_leads as $lead): 
                    $tier_class = get_member_tier_class($lead['points']);
                ?>
                    <a href="#" class="member-modal-trigger group flex items-center gap-4 rounded-2xl p-4 reflective-card shadow-sm text-left <?php echo $tier_class; ?>"
                       data-id="<?php echo $lead['id']; ?>"
                       data-name="<?php echo htmlspecialchars($lead['name']); ?>"
                       data-role="<?php echo htmlspecialchars($lead['role']); ?>"
                       data-team="<?php echo htmlspecialchars($lead['team']); ?>"
                       data-level="<?php echo htmlspecialchars($lead['level']); ?>"
                       data-campus="<?php echo htmlspecialchars($lead['campus']); ?>"
                       data-points="<?php echo htmlspecialchars($lead['points']); ?>"
                       data-responsibilities="<?php echo htmlspecialchars($lead['responsibilities']); ?>"
                       data-img="<?php echo htmlspecialchars($lead['image']); ?>"
                       data-rank="0">
                        
                        <img src="<?php echo $lead['image'] ?: 'public/images/AWS-MembersPics/default.png'; ?>" alt="<?php echo $lead['name']; ?>" class="h-11 w-11 rounded-xl object-cover border border-slate-200 dark:border-white/10">
                        <div class="min-w-0 flex-grow">
                            <p class="truncate text-sm font-black text-slate-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors"><?php echo $lead['name']; ?></p>
                            <p class="truncate text-xs text-slate-500 dark:text-zinc-400"><?php echo $lead['role']; ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-black text-purple-605 dark:text-purple-200"><?php echo number_format($lead['points']); ?></p>
                            <p class="text-[8px] font-black uppercase tracking-widest text-purple-650 dark:text-purple-405">PTS</p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- OTHER TEAMS (TECHNICAL, DESIGN, OPERATIONS, ETC) -->
    <section class="mt-16 space-y-12">
        <?php
        foreach ($team_order as $team_name):
            if ($team_name === 'Core') continue; // Core already rendered above
            
            $team_members = get_team_members($team_name, $participants);
            if (empty($team_members)) continue;
            
            $meta = $team_meta[$team_name] ?: ['title' => $team_name, 'blurb' => ''];
            
            // Technical Team Spotlight logic (Developer-level)
            $spotlights = [];
            $rest = [];
            
            if ($team_name === 'Technical') {
                foreach ($team_members as $m) {
                    if ($m['level'] === 'Developer') {
                        $spotlights[] = $m;
                    } else {
                        $rest[] = $m;
                    }
                }
            } else {
                $spotlights = array_slice($team_members, 0, 1);
                $rest = array_slice($team_members, 1);
            }
        ?>
            <div>
                <!-- Header panel -->
                <div class="relative border-b border-slate-200 dark:border-white/10 pb-4 mb-5">
                    <div class="flex items-end justify-between gap-6">
                        <div>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white font-space"><?php echo $meta['title']; ?></h3>
                            <p class="mt-1 text-xs sm:text-sm text-slate-500 dark:text-zinc-400 font-medium"><?php echo $meta['blurb']; ?></p>
                        </div>
                        <span class="rounded-full px-4 py-2 text-[9px] font-black uppercase tracking-widest shrink-0 <?php echo get_team_badge_class($team_name); ?>">
                            <?php echo count($team_members); ?> members
                        </span>
                    </div>
                </div>

                <!-- Content grid layout -->
                <div class="grid gap-6 <?php echo ($team_name === 'Technical' && count($spotlights) > 0) ? 'lg:grid-cols-3' : 'lg:grid-cols-2'; ?>">
                    
                    <!-- Spotlights column -->
                    <?php if (count($spotlights) > 0): ?>
                        <div class="<?php echo ($team_name === 'Technical' && count($spotlights) > 1) ? 'lg:col-span-2 grid gap-6 lg:grid-cols-2' : ''; ?>">
                            <?php foreach ($spotlights as $spot): 
                                $tier_class = get_member_tier_class($spot['points']);
                            ?>
                                <a href="#" class="member-modal-trigger group relative overflow-hidden rounded-3xl p-5 reflective-card shadow-lg flex flex-col justify-between <?php echo $tier_class; ?>"
                                   data-id="<?php echo $spot['id']; ?>"
                                   data-name="<?php echo htmlspecialchars($spot['name']); ?>"
                                   data-role="<?php echo htmlspecialchars($spot['role']); ?>"
                                   data-team="<?php echo htmlspecialchars($spot['team']); ?>"
                                   data-level="<?php echo htmlspecialchars($spot['level']); ?>"
                                   data-campus="<?php echo htmlspecialchars($spot['campus']); ?>"
                                   data-points="<?php echo htmlspecialchars($spot['points']); ?>"
                                   data-responsibilities="<?php echo htmlspecialchars($spot['responsibilities']); ?>"
                                   data-img="<?php echo htmlspecialchars($spot['image']); ?>"
                                   data-rank="0">
                                    
                                    <div class="pointer-events-none absolute -inset-24 opacity-0 group-hover:opacity-100 transition-opacity duration-500 bg-[radial-gradient(closest-side,rgba(139,92,246,0.18),rgba(217,70,239,0.1),transparent_70%)]"></div>
                                    
                                    <div class="flex items-start gap-4">
                                        <img src="<?php echo $spot['image'] ?: 'public/images/AWS-MembersPics/default.png'; ?>" alt="<?php echo $spot['name']; ?>" class="h-16 w-16 sm:h-20 sm:w-20 rounded-2xl object-cover border border-slate-200 dark:border-white/10 shadow-md">
                                        <div class="min-w-0 flex-grow relative z-10">
                                            <h4 class="text-lg font-black text-slate-900 dark:text-white group-hover:text-purple-650 dark:group-hover:text-purple-450 transition-colors font-space leading-tight"><?php echo $spot['name']; ?></h4>
                                            <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1"><?php echo $spot['role']; ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-6 flex items-end justify-between relative z-10">
                                        <div class="flex gap-1">
                                            <span class="rounded-full bg-purple-500/10 border border-purple-500/20 px-2.5 py-0.5 text-[8px] font-black uppercase tracking-widest text-purple-655 dark:text-purple-305">
                                                <?php echo $spot['level']; ?>
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-base font-black text-slate-900 dark:text-white leading-none"><?php echo number_format($spot['points']); ?></p>
                                            <p class="text-[8px] font-black uppercase tracking-widest text-purple-650 dark:text-purple-400 mt-0.5">points</p>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Grid for other members -->
                    <div class="grid gap-3">
                        <?php foreach ($rest as $m): 
                            $tier_class = get_member_tier_class($m['points']);
                        ?>
                            <a href="#" class="member-modal-trigger group flex items-center gap-4 rounded-2xl p-4 reflective-card shadow-sm text-left <?php echo $tier_class; ?>"
                               data-id="<?php echo $m['id']; ?>"
                               data-name="<?php echo htmlspecialchars($m['name']); ?>"
                               data-role="<?php echo htmlspecialchars($m['role']); ?>"
                               data-team="<?php echo htmlspecialchars($m['team']); ?>"
                               data-level="<?php echo htmlspecialchars($m['level']); ?>"
                               data-campus="<?php echo htmlspecialchars($m['campus']); ?>"
                               data-points="<?php echo htmlspecialchars($m['points']); ?>"
                               data-responsibilities="<?php echo htmlspecialchars($m['responsibilities']); ?>"
                               data-img="<?php echo htmlspecialchars($m['image']); ?>"
                               data-rank="0">
                                
                                <img src="<?php echo $m['image'] ?: 'public/images/AWS-MembersPics/default.png'; ?>" alt="<?php echo $m['name']; ?>" class="h-10 w-10 sm:h-11 sm:w-11 rounded-xl object-cover border border-slate-200 dark:border-white/10">
                                <div class="min-w-0 flex-grow">
                                    <p class="truncate text-sm font-black text-slate-900 dark:text-white group-hover:text-purple-650 dark:group-hover:text-purple-400 transition-colors font-space"><?php echo $m['name']; ?></p>
                                    <p class="truncate text-xs text-slate-505 dark:text-zinc-400"><?php echo $m['role']; ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-black text-slate-900 dark:text-white"><?php echo number_format($m['points']); ?></p>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-purple-650 dark:text-purple-405">PTS</p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </section>
</div>

<?php
require_once 'includes/footer.php';
?>
