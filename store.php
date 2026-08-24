<?php
// store.php
require_once 'includes/header.php';

$success = '';
$error = '';

// Handle Swag Claim Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'claim_swag') {
    $member_id = intval($_POST['member_id']);
    $swag_id = intval($_POST['swag_id']);

    if (!$member_id || !$swag_id) {
        $error = "Please select a valid member profile and swag.";
    } else {
        // Find member and swag
        $target_member = null;
        foreach ($participants as $p) {
            if ($p['id'] == $member_id) {
                $target_member = $p;
                break;
            }
        }

        $target_swag = null;
        foreach ($swags as $s) {
            if ($s['id'] == $swag_id) {
                $target_swag = $s;
                break;
            }
        }

        if (!$target_member) {
            $error = "Selected member profile not found.";
        } elseif (!$target_swag) {
            $error = "Selected AWS Swag not found.";
        } elseif ($target_swag['stock'] <= 0) {
            $error = "Sorry, this swag item is currently out of stock!";
        } elseif (floatval($target_member['points']) < floatval($target_swag['points'])) {
            $error = "Insufficient points! " . htmlspecialchars($target_member['name']) . " has " . number_format($target_member['points'], 2) . " PTS, but this swag requires " . number_format($target_swag['points'], 2) . " PTS.";
        } else {
            try {
                // Check if a pending request already exists for this member and swag
                $checkStmt = $db->prepare("SELECT id FROM `swag_requests` WHERE `member_id` = ? AND `swag_id` = ? AND `status` = 'pending'");
                $checkStmt->execute([$member_id, $swag_id]);
                if ($checkStmt->rowCount() > 0) {
                    $error = "You already have a pending claim request for this swag item!";
                } else {
                    $stmt = $db->prepare("INSERT INTO `swag_requests` (`member_id`, `swag_id`, `points_spent`, `status`) VALUES (?, ?, ?, 'pending')");
                    $stmt->execute([$member_id, $swag_id, floatval($target_swag['points'])]);
                    $success = "Claim request for '" . htmlspecialchars($target_swag['name']) . "' submitted successfully! Admin will review and fulfill your request.";
                    
                    // Refresh data
                    require 'includes/db.php';
                }
            } catch (Exception $e) {
                $error = "Error submitting claim: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="mx-auto max-w-7xl px-4 sm:px-6 pb-24 pt-8 lg:px-8">
    <!-- Breadcrumbs -->
    <div class="mb-4 text-[10px] font-black uppercase tracking-[0.24em] text-slate-400 dark:text-zinc-400">
        <a href="index.php" class="hover:text-purple-500 transition-colors">AWS Student Builders</a>
        <span class="mx-2">/</span>
        <span class="text-purple-600 dark:text-purple-400">Swags Store</span>
    </div>

    <!-- Page Header -->
    <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.28em] text-purple-600 dark:text-purple-400">Official Rewards Catalog</p>
            <h1 class="mt-2 text-3xl sm:text-5xl font-black text-slate-900 dark:text-white leading-none font-space">
                AWS <span class="text-glow-gradient">Swags Store</span> 🎁
            </h1>
            <p class="mt-4 max-w-2xl text-xs sm:text-sm leading-relaxed text-slate-500 dark:text-zinc-400 font-medium">
                Redeem your hard-earned Builder PTS for exclusive AWS gear, apparel, water bottles, and accessories! Select a swag to request a claim.
            </p>
        </div>

        <!-- Store Stats Pills -->
        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.02] px-4 py-3 backdrop-blur-md">
                <span class="block text-[9px] font-black uppercase tracking-wider text-slate-400">Available Swags</span>
                <span class="text-lg font-black text-purple-600 dark:text-purple-400 font-space"><?php echo count($swags); ?> Items</span>
            </div>
            <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.02] px-4 py-3 backdrop-blur-md">
                <span class="block text-[9px] font-black uppercase tracking-wider text-slate-400">Community Builders</span>
                <span class="text-lg font-black text-amber-500 font-space"><?php echo count($participants); ?> Members</span>
            </div>
        </div>
    </div>

    <!-- Feedback Banners -->
    <?php if ($success): ?>
        <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4 mb-8 text-sm text-emerald-600 dark:text-emerald-400 font-bold">
            ✓ <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="rounded-2xl border border-red-500/20 bg-red-500/10 p-4 mb-8 text-sm text-red-600 dark:text-red-400 font-bold">
            ⚠️ <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Filter & Search Controls -->
    <div class="mb-8 flex flex-col sm:flex-row items-center justify-between gap-4 rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.02] p-4 shadow-sm">
        <div class="relative w-full sm:w-80">
            <input type="text" id="swag-search" placeholder="Search swags by name..." class="w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-4 py-2.5 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500">
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-500 dark:text-zinc-400">Sort by:</span>
            <select id="swag-sort" class="rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-[#0d0a15] px-3 py-2 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500 cursor-pointer">
                <option value="points-asc">Lowest PTS Required</option>
                <option value="points-desc">Highest PTS Required</option>
                <option value="name">Name (A-Z)</option>
            </select>
        </div>
    </div>

    <!-- SWAGS GRID -->
    <?php if (empty($swags)): ?>
        <div class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.02] p-12 text-center">
            <p class="text-base font-bold text-slate-600 dark:text-zinc-400">No swags currently available in store.</p>
            <p class="text-xs text-slate-400 mt-1">Check back soon for new AWS merchandise!</p>
        </div>
    <?php else: ?>
        <div id="swags-container" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($swags as $swag): ?>
                <?php 
                $stock = intval($swag['stock']);
                $pts = floatval($swag['points']);
                $img = $swag['image'] ?: 'public/images/cloud_architecture_illustration.png';
                ?>
                <div class="swag-card flex flex-col justify-between rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.02] p-6 shadow-md hover-glow-card transition-all duration-300 relative overflow-hidden" data-name="<?php echo htmlspecialchars(strtolower($swag['name'])); ?>" data-pts="<?php echo $pts; ?>">
                    <!-- Top Badge Container -->
                    <div>
                        <div class="relative w-full h-48 rounded-2xl bg-slate-100 dark:bg-white/5 overflow-hidden mb-5 border border-slate-200 dark:border-white/10 flex items-center justify-center p-4">
                            <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($swag['name']); ?>" class="max-h-full max-w-full object-contain transition-transform duration-300 hover:scale-105" onerror="this.src='public/images/cloud_architecture_illustration.png'">
                            
                            <!-- PTS Badge -->
                            <div class="absolute top-3 right-3 rounded-full bg-purple-600 px-3.5 py-1.5 text-[10px] font-black uppercase tracking-wider text-white shadow-lg shadow-purple-600/30">
                                ⚡ <?php echo number_format($pts, 2); ?> PTS
                            </div>

                            <!-- Stock Indicator -->
                            <div class="absolute bottom-3 left-3 rounded-full px-3 py-1 text-[9px] font-black uppercase tracking-widest backdrop-blur-md <?php echo $stock > 0 ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30' : 'bg-red-500/20 text-red-500 border border-red-500/30'; ?>">
                                <?php echo $stock > 0 ? "In Stock ($stock left)" : "Out of Stock"; ?>
                            </div>
                        </div>

                        <h3 class="text-xl font-black text-slate-900 dark:text-white font-space mb-2">
                            <?php echo htmlspecialchars($swag['name']); ?>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 leading-relaxed font-medium mb-6">
                            <?php echo htmlspecialchars($swag['description'] ?: 'Official AWS Student Builder Group merchandise.'); ?>
                        </p>
                    </div>

                    <!-- Action Button -->
                    <div class="pt-2 border-t border-slate-100 dark:border-white/5 flex items-center justify-between gap-3">
                        <div>
                            <span class="block text-[9px] font-black uppercase tracking-wider text-slate-400">Cost</span>
                            <span class="text-sm font-black text-purple-600 dark:text-purple-400 font-space"><?php echo number_format($pts, 2); ?> PTS</span>
                        </div>

                        <?php if ($stock > 0): ?>
                            <button type="button" 
                                    data-id="<?php echo $swag['id']; ?>" 
                                    data-name="<?php echo htmlspecialchars($swag['name'], ENT_QUOTES, 'UTF-8'); ?>" 
                                    data-pts="<?php echo $pts; ?>" 
                                    data-img="<?php echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>" 
                                    onclick="openClaimModal(this)" 
                                    class="rounded-full bg-purple-600 hover:bg-purple-500 px-5 py-2.5 text-xs font-black uppercase tracking-wider text-white transition-all shadow-md shadow-purple-600/20 cursor-pointer flex items-center gap-2">
                                <span>Cart / Claim</span>
                                <span>🛒</span>
                            </button>
                        <?php else: ?>
                            <button disabled class="rounded-full bg-slate-200 dark:bg-white/5 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500 cursor-not-allowed">
                                Out of Stock
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- CLAIM SWAG MODAL OVERLAY -->
<div id="claim-swag-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm transition-opacity duration-300">
    <div class="w-full max-w-lg rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-[#0c0817] p-6 sm:p-8 shadow-2xl relative text-left">
        <!-- Close button -->
        <button onclick="closeClaimModal()" class="absolute top-5 right-5 h-8 w-8 rounded-full border border-slate-200 dark:border-white/10 flex items-center justify-center text-slate-500 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors">
            ✕
        </button>

        <h3 class="text-xl font-black text-slate-900 dark:text-white font-space mb-1">🎁 Claim AWS Swag</h3>
        <p class="text-xs text-slate-500 dark:text-zinc-400 mb-6">Select your builder profile to submit a swag claim request.</p>

        <form action="store.php" method="POST" class="space-y-5">
            <input type="hidden" name="action" value="claim_swag">
            <input type="hidden" name="swag_id" id="modal-swag-id">

            <!-- Swag Summary Box -->
            <div class="flex items-center gap-4 rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 p-4">
                <img id="modal-swag-img" src="" class="h-16 w-16 object-contain rounded-xl bg-white/10 p-1">
                <div>
                    <h4 id="modal-swag-name" class="text-sm font-bold text-slate-900 dark:text-white font-space"></h4>
                    <p class="text-xs font-black uppercase text-purple-600 dark:text-purple-400 mt-0.5">Required: <span id="modal-swag-pts"></span> PTS</p>
                </div>
            </div>

            <!-- Member Select -->
            <div>
                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-500 mb-2">Select Your Builder Profile</label>
                <select name="member_id" id="modal-member-select" required onchange="updateMemberPointsPreview()" class="w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-[#0d0a15] px-4 py-3 text-xs text-slate-900 dark:text-white outline-none focus:border-purple-500 cursor-pointer">
                    <option value="" data-pts="0">-- Select Profile --</option>
                    <?php foreach ($participants as $p): ?>
                        <option value="<?php echo $p['id']; ?>" data-pts="<?php echo $p['points']; ?>">
                            <?php echo htmlspecialchars($p['name']); ?> (<?php echo htmlspecialchars($p['role']); ?> — <?php echo number_format($p['points'], 2); ?> PTS)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Points Status Alert Box -->
            <div id="points-status-box" class="rounded-2xl p-4 text-xs font-bold border hidden">
                <span id="points-status-text"></span>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" id="submit-claim-btn" class="flex-grow rounded-full bg-purple-600 hover:bg-purple-500 py-3 text-xs font-black uppercase tracking-wider text-white transition-all shadow-md shadow-purple-600/20 cursor-pointer">
                    Confirm & Submit Claim
                </button>
                <button type="button" onclick="closeClaimModal()" class="rounded-full border border-slate-200 dark:border-white/10 hover:bg-slate-100 dark:hover:bg-white/5 px-5 py-3 text-xs font-bold text-slate-600 dark:text-zinc-400 cursor-pointer">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentSwagRequiredPts = 0;

function openClaimModal(btn) {
    const swagId = btn.getAttribute('data-id');
    const swagName = btn.getAttribute('data-name');
    const swagPts = parseFloat(btn.getAttribute('data-pts'));
    const swagImg = btn.getAttribute('data-img');

    currentSwagRequiredPts = swagPts;

    document.getElementById('modal-swag-id').value = swagId;
    document.getElementById('modal-swag-name').innerText = swagName;
    document.getElementById('modal-swag-pts').innerText = swagPts.toFixed(2);
    document.getElementById('modal-swag-img').src = swagImg;

    // Reset select
    const select = document.getElementById('modal-member-select');
    select.selectedIndex = 0;
    updateMemberPointsPreview();

    document.getElementById('claim-swag-modal').classList.remove('hidden');
}

function closeClaimModal() {
    document.getElementById('claim-swag-modal').classList.add('hidden');
}

function updateMemberPointsPreview() {
    const select = document.getElementById('modal-member-select');
    const selectedOption = select.options[select.selectedIndex];
    const memberPts = parseFloat(selectedOption.getAttribute('data-pts') || 0);

    const statusBox = document.getElementById('points-status-box');
    const statusText = document.getElementById('points-status-text');
    const submitBtn = document.getElementById('submit-claim-btn');

    if (!select.value) {
        statusBox.classList.add('hidden');
        submitBtn.disabled = false;
        return;
    }

    statusBox.classList.remove('hidden', 'bg-emerald-500/10', 'border-emerald-500/30', 'text-emerald-400', 'bg-red-500/10', 'border-red-500/30', 'text-red-400');

    if (memberPts >= currentSwagRequiredPts) {
        statusBox.classList.add('bg-emerald-500/10', 'border-emerald-500/30', 'text-emerald-400');
        statusText.innerHTML = `✓ Eligible! Your balance: <strong>${memberPts.toFixed(2)} PTS</strong>. Remaining after claim: <strong>${(memberPts - currentSwagRequiredPts).toFixed(2)} PTS</strong>.`;
        submitBtn.disabled = false;
    } else {
        statusBox.classList.add('bg-red-500/10', 'border-red-500/30', 'text-red-400');
        statusText.innerHTML = `⚠️ Insufficient points! Your balance: <strong>${memberPts.toFixed(2)} PTS</strong>. You need <strong>${(currentSwagRequiredPts - memberPts).toFixed(2)} PTS</strong> more.`;
        submitBtn.disabled = true;
    }
}

// Live Search & Sort
const searchInput = document.getElementById('swag-search');
const sortSelect = document.getElementById('swag-sort');

if (searchInput) {
    searchInput.addEventListener('input', filterAndSortSwags);
}
if (sortSelect) {
    sortSelect.addEventListener('change', filterAndSortSwags);
}

function filterAndSortSwags() {
    const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
    const sortBy = sortSelect ? sortSelect.value : 'points-asc';
    const container = document.getElementById('swags-container');
    const cards = Array.from(document.querySelectorAll('.swag-card'));

    cards.forEach(card => {
        const name = card.getAttribute('data-name');
        if (name.includes(query)) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });

    cards.sort((a, b) => {
        const ptsA = parseFloat(a.getAttribute('data-pts'));
        const ptsB = parseFloat(b.getAttribute('data-pts'));
        const nameA = a.getAttribute('data-name');
        const nameB = b.getAttribute('data-name');

        if (sortBy === 'points-asc') return ptsA - ptsB;
        if (sortBy === 'points-desc') return ptsB - ptsA;
        if (sortBy === 'name') return nameA.localeCompare(nameB);
        return 0;
    });

    if (container) {
        cards.forEach(card => container.appendChild(card));
    }
}
</script>

<?php
require_once 'includes/footer.php';
?>
