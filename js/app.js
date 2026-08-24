// js/app.js

document.addEventListener('DOMContentLoaded', () => {
    // ----------------------------------------------------
    // 1. Dynamic Clock & Greet Banner
    // ----------------------------------------------------
    const greetEl = document.getElementById('local-greet');
    const timeEl = document.getElementById('local-time');

    function updateGreeting() {
        const now = new Date();
        const hrs = now.getHours();
        const userName = (greetEl && greetEl.getAttribute('data-user')) ? greetEl.getAttribute('data-user') : "Builder";
        
        // Formulate greeting string
        let greetText = `Welcome to AWS SBG, ${userName}!`;
        if (hrs >= 5 && hrs < 12) {
            greetText = `Good morning, ${userName}! ☀️`;
        } else if (hrs >= 12 && hrs < 17) {
            greetText = `Good afternoon, ${userName}! ⚡`;
        } else if (hrs >= 17 && hrs < 22) {
            greetText = `Good evening, ${userName}! 🌌`;
        } else {
            greetText = `Working late? Keep grinding, ${userName}! 🛸`;
        }

        if (greetEl) {
            greetEl.innerText = greetText;
        }

        // Formulate time string (12-hour format)
        let mins = now.getMinutes();
        let formattedMins = mins < 10 ? '0' + mins : mins;
        let ampm = hrs >= 12 ? 'PM' : 'AM';
        let hrs12 = hrs % 12;
        hrs12 = hrs12 ? hrs12 : 12; // 0 should be 12
        let formattedHrs = hrs12 < 10 ? '0' + hrs12 : hrs12;

        if (timeEl) {
            timeEl.innerText = `${formattedHrs}:${formattedMins} ${ampm}`;
        }
    }

    updateGreeting();
    setInterval(updateGreeting, 60000); // Update time indicator every 60s

    // ----------------------------------------------------
    // 2. Theme Switcher (Dark / Light Mode)
    // ----------------------------------------------------
    const themeToggle = document.getElementById('theme-toggle');
    const rootEl = document.documentElement;

    // Load initial theme
    const savedTheme = localStorage.getItem('theme') || 'dark';
    if (savedTheme === 'light') {
        rootEl.classList.remove('dark');
        rootEl.classList.add('light');
        if (themeToggle) themeToggle.innerHTML = '🌙 Dark';
    } else {
        rootEl.classList.remove('light');
        rootEl.classList.add('dark');
        if (themeToggle) themeToggle.innerHTML = '☀️ Light';
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            if (rootEl.classList.contains('dark')) {
                rootEl.classList.remove('dark');
                rootEl.classList.add('light');
                localStorage.setItem('theme', 'light');
                themeToggle.innerHTML = '🌙 Dark';
            } else {
                rootEl.classList.remove('light');
                rootEl.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                themeToggle.innerHTML = '☀️ Light';
            }
        });
    }

    // ----------------------------------------------------
    // 3. Leaderboard Dynamic Search
    // ----------------------------------------------------
    const searchInput = document.getElementById('leaderboard-search');
    const leaderboardRows = document.querySelectorAll('.leaderboard-row');

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();

            leaderboardRows.forEach(row => {
                const searchContent = row.getAttribute('data-search').toLowerCase();
                if (searchContent.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // ----------------------------------------------------
    // 4. Blog Category Filtering
    // ----------------------------------------------------
    const blogCategory = document.getElementById('blog-category');
    const blogSearch = document.getElementById('blog-search');
    const blogArticles = document.querySelectorAll('.blog-article');

    function filterBlog() {
        const cat = blogCategory ? blogCategory.value : 'All';
        const q = blogSearch ? blogSearch.value.toLowerCase().trim() : '';

        blogArticles.forEach(art => {
            const articleCat = art.getAttribute('data-category');
            const searchContent = art.getAttribute('data-search').toLowerCase();

            const matchCat = (cat === 'All' || articleCat === cat);
            const matchQ = (!q || searchContent.includes(q));

            if (matchCat && matchQ) {
                art.style.display = '';
            } else {
                art.style.display = 'none';
            }
        });
    }

    if (blogCategory) blogCategory.addEventListener('change', filterBlog);
    if (blogSearch) blogSearch.addEventListener('input', filterBlog);

    // ----------------------------------------------------
    // 5. Interactive Member Modal
    // ----------------------------------------------------
    const modal = document.getElementById('member-modal');
    const modalClose = document.getElementById('modal-close');
    const modalOverlay = document.getElementById('modal-overlay');

    const modalImg = document.getElementById('modal-img');
    const modalName = document.getElementById('modal-name');
    const modalRole = document.getElementById('modal-role');
    const modalTeam = document.getElementById('modal-team');
    const modalLevel = document.getElementById('modal-level');
    const modalCampus = document.getElementById('modal-campus');
    const modalPoints = document.getElementById('modal-points');
    const modalResponsibilities = document.getElementById('modal-responsibilities');
    const modalRankBadge = document.getElementById('modal-rank-badge');

    const memberCards = document.querySelectorAll('.member-modal-trigger');

    function openModal(card) {
        const img = card.getAttribute('data-img');
        const name = card.getAttribute('data-name');
        const role = card.getAttribute('data-role');
        const team = card.getAttribute('data-team');
        const level = card.getAttribute('data-level');
        const campus = card.getAttribute('data-campus');
        const points = card.getAttribute('data-points');
        const responsibilities = card.getAttribute('data-responsibilities');
        const rank = card.getAttribute('data-rank');

        if (modalImg) modalImg.src = img || 'public/images/AWS-MembersPics/default.png';
        if (modalName) modalName.innerText = name;
        if (modalRole) modalRole.innerText = role;
        if (modalTeam) modalTeam.innerText = team;
        if (modalLevel) modalLevel.innerText = level;
        if (modalCampus) modalCampus.innerText = campus;
        if (modalPoints) modalPoints.innerText = parseFloat(points).toLocaleString();
        if (modalResponsibilities) modalResponsibilities.innerText = responsibilities || 'No responsibilities provided.';

        if (modalRankBadge) {
            if (rank && rank !== '0') {
                modalRankBadge.style.display = '';
                modalRankBadge.innerText = `Rank #${rank}`;
            } else {
                modalRankBadge.style.display = 'none';
            }
        }

        // Gamified Tier Progression calculation
        const pts = parseFloat(points) || 0;
        let tierLabel = "Bronze Initiate";
        let progressPercent = 0;
        let progressText = "";
        let tierClass = "tier-bronze";
        let progressBarColor = "from-orange-500 via-amber-600 to-red-500";

        if (level === 'Lead' || level === 'Core Team' || level === 'Directorate') {
            tierLabel = `Gold Master (${level})`;
            progressPercent = 100;
            progressText = "Milestone Achieved";
            tierClass = "tier-gold";
            progressBarColor = "from-amber-500 via-yellow-400 to-amber-600";
        } else if (pts >= 60) {
            tierLabel = "Gold Master";
            progressPercent = 100;
            progressText = "Milestone Achieved";
            tierClass = "tier-gold";
            progressBarColor = "from-amber-500 via-yellow-400 to-amber-600";
        } else if (pts >= 40) {
            tierLabel = "Silver Builder";
            progressPercent = Math.round(((pts - 40) / 20) * 100);
            progressText = `Progress to Gold: ${progressPercent}%`;
            tierClass = "tier-silver";
            progressBarColor = "from-slate-400 via-zinc-350 to-slate-500";
        } else {
            tierLabel = "Bronze Initiate";
            progressPercent = Math.round((pts / 40) * 100);
            progressText = `Progress to Silver: ${progressPercent}%`;
            tierClass = "tier-bronze";
            progressBarColor = "from-purple-500 via-pink-500 to-indigo-500";
        }

        // Apply tier border styling to the modal card element
        const modalContentCard = document.querySelector('#member-modal > div:nth-child(2)');
        if (modalContentCard) {
            modalContentCard.classList.remove('tier-gold', 'tier-silver', 'tier-bronze');
            modalContentCard.classList.add(tierClass);
        }

        // Update progress indicators
        const tierBadgeEl = document.getElementById('modal-tier-badge');
        const progressTextEl = document.getElementById('modal-tier-progress-text');
        const progressBarEl = document.getElementById('modal-tier-progress-bar');

        if (tierBadgeEl) tierBadgeEl.innerText = tierLabel;
        if (progressTextEl) progressTextEl.innerText = progressText;
        if (progressBarEl) {
            progressBarEl.style.width = `${progressPercent}%`;
            progressBarEl.className = `h-full rounded-full bg-gradient-to-r ${progressBarColor} progress-bar-shine`;
        }

        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                modal.classList.add('opacity-100');
            }, 10);
        }
    }

    function closeModal() {
        if (modal) {
            modal.classList.remove('opacity-100');
            document.body.style.overflow = '';
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    }

    memberCards.forEach(card => {
        card.addEventListener('click', (e) => {
            e.preventDefault();
            openModal(card);
        });
    });

    if (modalClose) modalClose.addEventListener('click', closeModal);
    if (modalOverlay) modalOverlay.addEventListener('click', closeModal);

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });

    // ----------------------------------------------------
    // 6. Past Event Gallery Slider
    // ----------------------------------------------------
    const sliders = document.querySelectorAll('.gallery-slider');
    sliders.forEach(slider => {
        const slides = slider.querySelectorAll('.slider-slide');
        const btnPrev = slider.querySelector('.slider-prev');
        const btnNext = slider.querySelector('.slider-next');
        let activeIdx = 0;

        function updateSlider() {
            slides.forEach((slide, idx) => {
                if (idx === activeIdx) {
                    slide.classList.remove('opacity-0', 'pointer-events-none');
                    slide.classList.add('opacity-100');
                } else {
                    slide.classList.add('opacity-0', 'pointer-events-none');
                    slide.classList.remove('opacity-100');
                }
            });
        }

        if (btnPrev && btnNext && slides.length > 0) {
            btnPrev.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                activeIdx = (activeIdx - 1 + slides.length) % slides.length;
                updateSlider();
            });

            btnNext.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                activeIdx = (activeIdx + 1) % slides.length;
                updateSlider();
            });
        }
    });
});
