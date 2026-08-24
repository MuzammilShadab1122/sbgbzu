<?php
// blog.php
require_once 'includes/header.php';

// Unique categories
$categories = ['All'];
foreach ($posts as $post) {
    if (!in_array($post['category'], $categories)) {
        $categories[] = $post['category'];
    }
}
?>

<div class="mx-auto max-w-7xl px-4 sm:px-6 pb-24 pt-8 lg:px-8">
    <!-- Breadcrumbs -->
    <div class="mb-4 text-[9px] font-black uppercase tracking-[0.24em] text-slate-400 dark:text-zinc-400">
        <span>AWS Student Builders</span>
        <span class="mx-2">/</span>
        <span class="text-purple-650 dark:text-purple-400">Blog Updates</span>
    </div>

    <!-- Page Header -->
    <div class="mb-10">
        <p class="text-[10px] font-black uppercase tracking-[0.28em] text-purple-605 dark:text-purple-400">Announcements & Notes</p>
        <h2 class="mt-2 text-3xl sm:text-5xl font-black text-slate-900 dark:text-white leading-none font-space">Program <span class="text-glow-gradient">Updates & Orientation</span></h2>
        <p class="mt-4 max-w-2xl text-xs sm:text-sm leading-relaxed text-slate-500 dark:text-zinc-400 font-medium font-medium">Read official orientations, rules, team points updates, and community newsletters direct from BZU leads.</p>
    </div>

    <!-- Filters Panel -->
    <div class="flex flex-col gap-4 border-b border-slate-200 dark:border-white/10 pb-6 sm:flex-row sm:items-center sm:justify-between">
        <input
            id="blog-search"
            type="text"
            placeholder="Search blog articles…"
            class="w-full rounded-full border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 px-5 py-3 text-sm text-slate-900 dark:text-white outline-none focus:border-purple-500 focus:shadow-[0_0_15px_rgba(168,85,247,0.25)] placeholder:text-slate-400 dark:placeholder:text-zinc-550 transition-all sm:max-w-sm"
        >

        <select
            id="blog-category"
            class="rounded-full border border-slate-200 dark:border-white/10 bg-white dark:bg-[#0d0a15] px-5 py-3 text-sm text-slate-900 dark:text-white outline-none focus:border-purple-500 focus:shadow-[0_0_15px_rgba(168,85,247,0.25)] transition-all"
        >
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat; ?>" class="text-black"><?php echo $cat; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Blog Articles Grid -->
    <div class="mt-8 grid gap-6 md:grid-cols-2">
        <?php foreach ($posts as $post): 
            $search_data = $post['title'] . ' ' . $post['excerpt'] . ' ' . $post['category'] . ' ' . $post['date'];
        ?>
            <article class="blog-article rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.02] p-6 shadow-sm flex flex-col justify-between hover-glow-card"
                     data-category="<?php echo htmlspecialchars($post['category']); ?>"
                     data-search="<?php echo htmlspecialchars($search_data); ?>">
                
                <div>
                    <span class="text-[9px] font-black uppercase tracking-widest text-purple-650 dark:text-purple-400">
                        <?php echo $post['category']; ?> / <?php echo $post['date']; ?>
                    </span>
                    <h3 class="mt-3 text-xl sm:text-2xl font-black text-slate-900 dark:text-white leading-tight font-space mb-2"><?php echo $post['title']; ?></h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-zinc-400 leading-relaxed font-medium"><?php echo $post['excerpt']; ?></p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
