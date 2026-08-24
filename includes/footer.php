<?php
// includes/footer.php
?>
    </main>

    <!-- FOOTER -->
    <footer class="relative overflow-hidden border-t border-slate-200 dark:border-white/10 bg-white/70 dark:bg-black/40 backdrop-blur-md mt-24">
        <!-- Floating Stickers in background -->
        <div class="pointer-events-none absolute inset-0 z-0 hidden lg:block overflow-hidden">
            <div class="absolute -right-6 -top-3 h-20 w-20 opacity-10 animate-float" style="animation-delay: 1s;">
                <img src="public/images/stickers/aws.png" alt="" class="h-full w-full object-contain">
            </div>
            <div class="absolute -left-6 bottom-4 h-20 w-20 opacity-10 animate-float" style="animation-delay: 3s;">
                <img src="public/images/stickers/cloud.png" alt="" class="h-full w-full object-contain">
            </div>
        </div>

        <div class="relative z-10 mx-auto max-w-7xl px-6 py-12 lg:px-8">
            <div class="grid grid-cols-1 gap-10 md:grid-cols-4">
                
                <!-- Brand Profile -->
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3">
                        <img src="public/images/sbg-logo.png" alt="AWS SBG BZU logo" class="h-10 w-10 rounded-full object-contain">
                        <div>
                            <p class="text-base font-black tracking-tight text-slate-900 dark:text-white">
                                AWS Student Builder Group BZU
                            </p>
                            <p class="mt-1 text-xs font-bold uppercase tracking-[0.22em] text-purple-600 dark:text-purple-400">
                                Empowering innovation through AWS
                            </p>
                        </div>
                    </div>
                    <p class="mt-4 max-w-xl text-sm leading-7 text-slate-600 dark:text-zinc-400">
                        We are a student-led, peer-to-peer cloud community at Bahauddin Zakariya University. Our mission is to bridge the gap between classroom theory and real-world production-grade cloud environments using Amazon Web Services.
                    </p>
                </div>

                <!-- Navigation Quicklinks -->
                <div>
                    <h3 class="mb-4 text-xs font-black uppercase tracking-[0.24em] text-slate-900 dark:text-white">
                        Explore
                    </h3>
                    <ul class="space-y-2.5 text-sm text-slate-600 dark:text-zinc-450">
                        <li><a href="index.php" class="hover:text-purple-500 transition-colors">Home</a></li>
                        <li><a href="team.php" class="hover:text-purple-500 transition-colors">Teams</a></li>
                        <li><a href="leaderboard.php" class="hover:text-purple-500 transition-colors">Leaderboard</a></li>
                        <li><a href="events.php" class="hover:text-purple-500 transition-colors">Events</a></li>
                        <li><a href="blog.php" class="hover:text-purple-500 transition-colors">Blog & Updates</a></li>
                    </ul>
                </div>

                <!-- Connect Socials -->
                <div>
                    <h3 class="mb-4 text-xs font-black uppercase tracking-[0.24em] text-slate-900 dark:text-white">
                        Connect With Us
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        <!-- GitHub (Reactivated!) -->
                        <a href="https://github.com/Aleenakhan-p/AWS-BZU-Portfolio" target="_blank" rel="noopener noreferrer" class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-white hover:bg-purple-600 hover:text-white dark:hover:bg-purple-600 hover:-translate-y-1 transition-all duration-300" title="GitHub Repository">
                            <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                        </a>
                        <!-- LinkedIn -->
                        <a href="https://www.linkedin.com/company/aws-cloud-club-bzu-multan/" target="_blank" rel="noopener noreferrer" class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-white hover:bg-[#0A66C2] hover:text-white hover:-translate-y-1 transition-all duration-300" title="LinkedIn">
                            <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.779-1.75-1.75s.784-1.75 1.75-1.75 1.75.779 1.75 1.75-.784 1.75-1.75 1.75zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        </a>
                        <!-- Instagram -->
                        <a href="https://www.instagram.com/aws_sbg_bzu" target="_blank" rel="noopener noreferrer" class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-white hover:bg-pink-500 hover:text-white hover:-translate-y-1 transition-all duration-300" title="Instagram">
                            <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <!-- Meetup -->
                        <a href="https://www.meetup.com/aws-sbg-at-bahauddinzakariya-univ-multan" target="_blank" rel="noopener noreferrer" class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-white hover:bg-[#E51937] hover:text-white hover:-translate-y-1 transition-all duration-300" title="Meetup">
                            <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24"><path d="M12.012 3.1a9.23 9.23 0 00-7.26 3.51c-.34.45-.63.93-.86 1.43a9.42 9.42 0 00.99 10.51c1.88 2.21 4.67 3.45 7.49 3.45a9.24 9.24 0 007.26-3.51c.34-.45.63-.93.86-1.43a9.42 9.42 0 00-.99-10.51c-1.88-2.21-4.67-3.45-7.49-3.45zm.77 5.76a2.82 2.82 0 11-5.64 0 2.82 2.82 0 015.64 0zm6.13 5.41a3.02 3.02 0 11-6.04 0 3.02 3.02 0 016.04 0zm-7.66 4.3a2.02 2.02 0 11-4.04 0 2.02 2.02 0 014.04 0z"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bottom Credits -->
            <div class="mt-12 pt-8 border-t border-slate-200 dark:border-white/5 flex flex-col gap-4 md:flex-row md:items-center md:justify-between text-xs text-slate-500">
                <p>© 2026 AWS Student Builder Group, BZU Multan. All rights reserved.</p>
                <p>Designed & Developed with ❤️ by <a href="team.php" class="text-purple-600 dark:text-purple-400 hover:underline">Technical Team</a>.</p>
            </div>
        </div>
    </footer>

    <!-- REUSABLE MEMBER DETAILS MODAL -->
    <div id="member-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 md:p-6 hidden opacity-0 transition-opacity duration-300">
        <!-- Overlay backdrop -->
        <div id="modal-overlay" class="absolute inset-0 bg-black/80 backdrop-blur-md cursor-pointer"></div>
        
        <!-- Modal Card -->
        <div class="relative w-full max-w-2xl overflow-hidden rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-[#0d091a] text-slate-950 dark:text-white p-6 md:p-8 shadow-2xl transition-all duration-300">
            <!-- Background Radial Glow -->
            <div class="pointer-events-none absolute -inset-32 opacity-35 bg-[radial-gradient(circle_at_center,rgba(139,92,246,0.2),transparent_65%)]"></div>

            <!-- Close button -->
            <button id="modal-close" class="absolute right-4 top-4 text-slate-400 hover:text-slate-900 dark:text-zinc-400 dark:hover:text-white bg-slate-100 dark:bg-white/5 rounded-full p-2 border border-slate-200 dark:border-white/10 cursor-pointer">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Modal Content Grid -->
            <div class="relative z-10 flex flex-col md:flex-row gap-6 items-center md:items-start text-center md:text-left">
                <!-- Avatar block -->
                <div class="relative group">
                    <img id="modal-img" src="" alt="" class="h-32 w-32 md:h-40 md:w-40 rounded-3xl object-cover border border-slate-200 dark:border-white/10 shadow-lg">
                    <span id="modal-rank-badge" class="absolute -bottom-3 left-1/2 -translate-x-1/2 rounded-full bg-amber-500 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-black shadow-md whitespace-nowrap">
                        Rank #--
                    </span>
                </div>

                <!-- Info detail block -->
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 mb-2">
                        <span id="modal-team" class="rounded-full bg-purple-500/10 border border-purple-500/20 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-purple-600 dark:text-purple-300">
                            Team
                        </span>
                        <span id="modal-level" class="rounded-full bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-slate-700 dark:text-white/80">
                            Level
                        </span>
                        <span id="modal-campus" class="rounded-full bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-slate-700 dark:text-white/80">
                            Campus
                        </span>
                    </div>

                    <h3 id="modal-name" class="text-3xl font-black tracking-tight mb-1 text-slate-900 dark:text-white">
                        Member Name
                    </h3>
                    <p id="modal-role" class="text-purple-600 dark:text-purple-300 text-sm font-semibold mb-5">
                        Role
                    </p>

                    <!-- Stat columns -->
                    <div class="grid grid-cols-2 gap-4 border-y border-slate-200 dark:border-white/5 py-4 mb-5">
                        <div>
                            <span class="block text-[9px] font-black uppercase tracking-widest text-slate-500">Total Contribution</span>
                            <span class="text-2xl font-black text-slate-900 dark:text-white"><span id="modal-points">0.0</span> <span class="text-xs text-purple-600 dark:text-purple-400">PTS</span></span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-black uppercase tracking-widest text-slate-500">Activity Status</span>
                            <span class="text-base font-bold text-emerald-600 dark:text-emerald-450 flex items-center justify-center md:justify-start gap-1.5 mt-1">
                                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Active Builder
                            </span>
                        </div>
                    </div>

                    <!-- Progression Bar to Next Tier -->
                    <div id="modal-progression-wrapper" class="mb-5">
                        <div class="flex justify-between items-center mb-1.5 text-[9px] font-black uppercase tracking-widest text-slate-400">
                            <span id="modal-tier-badge" class="font-extrabold text-purple-600 dark:text-purple-300">Bronze Initiate</span>
                            <span id="modal-tier-progress-text" class="font-bold">Progress to Next Tier: --%</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-slate-100 dark:bg-white/10 overflow-hidden">
                            <div id="modal-tier-progress-bar" class="h-full rounded-full bg-gradient-to-r from-purple-500 via-pink-500 to-purple-600 progress-bar-shine" style="width: 0%;"></div>
                        </div>
                    </div>

                    <!-- Responsibilities block -->
                    <div>
                        <span class="block text-[9px] font-black uppercase tracking-widest text-slate-500 mb-2">Key Responsibilities</span>
                        <p id="modal-responsibilities" class="text-sm leading-relaxed text-slate-605 dark:text-zinc-400">
                            Loading responsibilities...
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Scripts -->
    <script src="js/app.js"></script>
</body>
</html>
