<?php
// pages/podcasts.php
// Premium Podcasts Landing & Player View

$seoPageType = 'podcasts';

$podcastMgr = new PodcastManager();
$categories = $podcastMgr->getCategories();

$category = $_GET['category'] ?? null;
$search = $_GET['search'] ?? null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$podcastsResult = $podcastMgr->getPodcasts([
    'category' => $category,
    'search' => $search,
    'page' => $page,
    'limit' => 6
]);

$podcasts = $podcastsResult['data'];
$meta = $podcastsResult['meta'];

$featuredPodcasts = $podcastMgr->getFeaturedPodcasts();

require_once PATH_ROOT . '/includes/header.php';
require_once PATH_ROOT . '/includes/navbar.php';
?>

<main class="container dark-podcast-theme" style="padding: 3rem 2rem; border-radius: var(--border-radius-card); margin-top: 2rem; margin-bottom: 4rem; border: 1px solid var(--border-color); box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
    <!-- Breadcrumbs -->
    <?php renderBreadcrumbs(['Podcasts' => '']); ?>

    <!-- Search & Title Header -->
    <section class="content-list-section" style="padding-bottom: 2rem;">
        <div class="mb-16 text-center max-w-3xl mx-auto" data-scroll-reveal>
            <span class="section-label" style="color: var(--accent-color);">Audio Journeys</span>
            <h1 class="serif-title" style="margin-top: 1rem; color: #ffffff;"><?php echo e($settings['siteName'] ?? 'Young Over 60'); ?> Podcasts</h1>
            <p style="color: #b3b3b3; font-size: 1.1rem; font-weight: 300; margin-top: 1rem; margin-bottom: 3rem;">
                Listen to inspiring conversations, travel guides, and real accounts of travellers with disabilities navigating the globe.
            </p>
            
            <!-- Search bar -->
            <form action="<?php echo BASE_URL; ?>/podcasts" method="GET" style="max-width: 600px; margin: 0 auto; display: flex; gap: 0.5rem;">
                <?php if ($category): ?>
                    <input type="hidden" name="category" value="<?php echo e($category); ?>" />
                <?php endif; ?>
                <input 
                    type="text" 
                    name="search" 
                    value="<?php echo e($search); ?>" 
                    placeholder="Search episodes..." 
                    class="form-input" 
                    aria-label="Search podcasts query"
                    style="border-radius: 8px; flex-grow: 1; background-color: #2a2a2a; border-color: #3e3e3e; color: #ffffff;"
                />
                <button type="submit" class="btn-primary" style="padding: 0 2rem; border-radius: 8px; font-size: 0.75rem;">
                    Search
                </button>
            </form>
            <?php if ($search): ?>
                <p style="margin-top: 1rem; font-size: 0.85rem; color: #888;">
                    Showing results for "<strong><?php echo e($search); ?></strong>" &bull; <a href="<?php echo BASE_URL; ?>/podcasts<?php echo $category ? '?category='.urlencode($category) : ''; ?>" style="text-decoration: underline; color: var(--accent-color);">Clear search</a>
                </p>
            <?php endif; ?>
        </div>
    </section>

    <!-- 1. Featured Podcasts Carousel Section -->
    <?php if (!empty($featuredPodcasts) && !$category && !$search): ?>
        <section class="featured-podcasts-section" style="margin-bottom: 5rem;" data-scroll-reveal>
            <div class="intro-content" style="gap: 0.5rem; margin-bottom: 2rem;">
                <span class="section-label" style="color: var(--accent-color);">Editor's Picks</span>
                <h3 class="serif-title" style="margin:0; font-size: 1.75rem; color: #ffffff;">Featured Episodes</h3>
            </div>
            
            <div class="podcast-carousel-wrapper">
                <div class="podcast-carousel-inner">
                    <?php foreach ($featuredPodcasts as $pod): 
                        $cover = !empty($pod['cover_image']) ? $pod['cover_image'] : '/images/hero-bg.png';
                        ?>
                        <div class="podcast-featured-card">
                            <!-- Large tablet/desktop split view -->
                            <div class="podcast-featured-grid">
                                <div class="podcast-featured-media">
                                    <div class="progressive-image-placeholder"></div>
                                    <img src="<?php echo e(BASE_URL . $cover); ?>" alt="<?php echo e(t($pod, 'title')); ?>" class="post-card-img" onload="this.classList.add('loaded');" />
                                    <button class="podcast-play-overlay-btn" 
                                            data-audio="<?php echo e(BASE_URL . $pod['audio_file']); ?>" 
                                            data-title="<?php echo e(e(t($pod, 'title'))); ?>" 
                                            data-cover="<?php echo e(BASE_URL . $cover); ?>"
                                            data-id="<?php echo e($pod['id']); ?>"
                                            aria-label="Play <?php echo e(t($pod, 'title')); ?>">
                                        <svg style="width: 3rem; height: 3rem; fill: #ffffff;" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </button>
                                </div>
                                <div class="podcast-featured-content" style="padding: 2.5rem; display: flex; flex-direction: column; justify-content: center; gap: 1rem;">
                                    <span class="badge-text" style="align-self: flex-start; background: var(--primary-color); color: #fff;"><?php echo e(t($pod, 'category')); ?></span>
                                    <h4 class="serif-title" style="font-size: 1.6rem; margin: 0; color: #ffffff;"><?php echo e(t($pod, 'title')); ?></h4>
                                    <p style="color: #b3b3b3; font-size: 0.95rem; font-weight: 300; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;"><?php echo e(t($pod, 'description')); ?></p>
                                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: #888; font-weight: bold; margin-top: 1rem;">
                                        <span>Duration: <?php echo e($pod['duration']); ?></span>
                                        <span>Published: <?php echo formatDate($pod['created_at']); ?></span>
                                    </div>
                                    <button class="btn-primary play-trigger-btn" 
                                            data-audio="<?php echo e(BASE_URL . $pod['audio_file']); ?>" 
                                            data-title="<?php echo e(e(t($pod, 'title'))); ?>" 
                                            data-cover="<?php echo e(BASE_URL . $cover); ?>"
                                            data-id="<?php echo e($pod['id']); ?>"
                                            style="margin-top: 1.5rem; border-radius: 8px; font-size: 0.7rem; padding: 1rem 2rem;">
                                        LISTEN NOW
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- 2. Recently Played Section (Hydrated via JS) -->
    <section id="recently-played-section" style="display: none; margin-bottom: 5rem;" data-scroll-reveal>
        <div class="intro-content" style="gap: 0.5rem; margin-bottom: 2rem;">
            <span class="section-label" style="color: var(--accent-color);">Your History</span>
            <h3 class="serif-title" style="margin:0; font-size: 1.75rem; color: #ffffff;">Recently Played</h3>
        </div>
        <div id="recently-played-list" class="story-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
            <!-- Dynamically populated -->
        </div>
    </section>

    <!-- 3. Main Podcast Listing & Category Tabs -->
    <section class="filterable-section" style="padding-top: 0; border: none;">
        <div class="filterable-header-row" style="margin-bottom: 3rem;">
            <div class="intro-content" style="gap: 0.5rem;">
                <span class="section-label" style="color: var(--accent-color);">All Episodes</span>
                <h3 class="serif-title" style="margin:0; font-size: 1.75rem; color: #ffffff;">Browse Episodes</h3>
            </div>
            
            <!-- Category Tabs -->
            <div class="tabs-container" role="tablist" aria-label="Podcast categories">
                <a href="<?php echo BASE_URL; ?>/podcasts" class="tab-btn <?php echo !$category ? 'active' : ''; ?>" role="tab" style="background-color: #1c1c1c; border-color: #2e2e2e; color: #b3b3b3;">ALL</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="<?php echo BASE_URL; ?>/podcasts?category=<?php echo urlencode($cat); ?>" class="tab-btn <?php echo $category === $cat ? 'active' : ''; ?>" role="tab" style="background-color: #1c1c1c; border-color: #2e2e2e; color: #b3b3b3;">
                        <?php echo e(strtoupper($cat)); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Episodes Grid -->
        <?php if (!empty($podcasts)): ?>
            <div class="story-grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));">
                <?php foreach ($podcasts as $pod): 
                    $cover = !empty($pod['cover_image']) ? $pod['cover_image'] : '/images/hero-bg.png';
                    ?>
                    <article class="post-card group" data-scroll-reveal style="border-radius: 12px; overflow: hidden; background: var(--surface-color); border-color: var(--border-color);">
                        <div class="post-card-media" style="aspect-ratio: 16/10; position: relative;">
                            <div class="progressive-image-placeholder"></div>
                            <img src="<?php echo e(BASE_URL . $cover); ?>" alt="<?php echo e(t($pod, 'title')); ?>" loading="lazy" class="post-card-img" onload="this.classList.add('loaded');" />
                            <div class="post-card-badge">
                                <span class="badge-text" style="background: var(--primary-color); color: #fff;"><?php echo e(t($pod, 'category')); ?></span>
                            </div>
                            <!-- Play button overlay -->
                            <button class="podcast-card-play-btn" 
                                    data-audio="<?php echo e(BASE_URL . $pod['audio_file']); ?>" 
                                    data-title="<?php echo e(e(t($pod, 'title'))); ?>" 
                                    data-cover="<?php echo e(BASE_URL . $cover); ?>"
                                    data-id="<?php echo e($pod['id']); ?>"
                                    aria-label="Play <?php echo e(t($pod, 'title')); ?>">
                                <svg style="width: 1.5rem; height: 1.5rem; fill: #ffffff;" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </button>
                        </div>
                        
                        <div class="post-card-body" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 0.75rem;">
                            <div class="post-card-meta" style="margin-bottom: 0;">
                                <time datetime="<?php echo e($pod['created_at']); ?>" style="color: #b3b3b3;"><?php echo formatDate($pod['created_at']); ?></time>
                                <span class="meta-dot"></span>
                                <span style="color: #b3b3b3;">Duration: <?php echo e($pod['duration']); ?></span>
                            </div>
                            
                            <h3 class="post-card-title" style="font-size: 1.25rem; margin-bottom: 0; color: #ffffff;"><?php echo e(t($pod, 'title')); ?></h3>
                            
                            <p class="post-card-excerpt" style="font-size: 0.85rem; line-height: 1.5; color: #b3b3b3; -webkit-line-clamp: 2; flex-grow: 1; margin-bottom: 0;">
                                <?php echo e(t($pod, 'description')); ?>
                            </p>
                            
                            <div class="post-card-cta" style="margin-top: 0.5rem;">
                                <button class="cta-text btn-reset play-trigger-btn" 
                                        data-audio="<?php echo e(BASE_URL . $pod['audio_file']); ?>" 
                                        data-title="<?php echo e(e(t($pod, 'title'))); ?>" 
                                        data-cover="<?php echo e(BASE_URL . $cover); ?>"
                                        data-id="<?php echo e($pod['id']); ?>"
                                        style="font-size: 0.65rem; border: none; cursor: pointer; background: transparent; font-weight: 900; letter-spacing: 0.1em; color: var(--accent-color);">
                                    PLAY NOW <span class="cta-arrow" aria-hidden="true" style="margin-left: 0.25rem; color: var(--accent-color);">&rarr;</span>
                                </button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination Row -->
            <?php if ($meta['totalPages'] > 1): ?>
                <div class="pagination-row" data-scroll-reveal>
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo $category ? '&category='.urlencode($category) : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="page-link" style="background: var(--surface-color); border-color: var(--border-color); color: #ffffff;">&larr;</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $meta['totalPages']; $i++): ?>
                        <a href="?page=<?php echo $i; ?><?php echo $category ? '&category='.urlencode($category) : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="page-link <?php echo $i === $page ? 'active' : ''; ?>" style="background: var(--surface-color); border-color: var(--border-color); color: #ffffff;">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $meta['totalPages']): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $category ? '&category='.urlencode($category) : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="page-link" style="background: var(--surface-color); border-color: var(--border-color); color: #ffffff;">&rarr;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div style="text-align: center; padding: 6rem 0; border: 1px dashed var(--border-color); background-color: var(--surface-color); border-radius: 12px;" data-scroll-reveal>
                <div style="font-size: 3rem; margin-bottom: 1.5rem;">🎙️</div>
                <h3 class="serif-title" style="font-size: 1.5rem; margin-bottom: 0.5rem; color: #ffffff;">No Episodes Found</h3>
                <p style="color: #b3b3b3; font-weight: 300;">We couldn't find any podcast episodes matching your queries.</p>
            </div>
        <?php endif; ?>
    </section>
</main>

<!-- Floating Audio Player Component -->
<div id="floating-audio-player" class="floating-player">
    <div class="floating-player-container container">
        <audio id="main-audio-element" src=""></audio>
        
        <!-- Album Cover & Info -->
        <div class="player-track-info">
            <img id="player-track-cover" src="/images/hero-bg.png" alt="Cover" />
            <div class="player-track-text">
                <h5 id="player-track-title">Episode Title</h5>
                <span id="player-track-status">Stopped</span>
            </div>
        </div>

        <!-- Controls -->
        <div class="player-controls-wrapper">
            <div class="player-action-buttons">
                <!-- Backward 10s -->
                <button class="player-control-btn" id="player-btn-back" aria-label="Rewind 10 seconds">
                    <svg viewBox="0 0 24 24"><path d="M12 5V1L7 6l5 5V7c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8zM10.8 15h.8v-3.5h-.8v.7h-.6v.7h.6v2.1zm2 .2h.8v-3.7h-.8v3.7z"/></svg>
                </button>
                <!-- Play/Pause -->
                <button class="player-control-btn player-btn-main" id="player-btn-play" aria-label="Play/Pause">
                    <svg id="play-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    <svg id="pause-svg" viewBox="0 0 24 24" style="display: none;"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                </button>
                <!-- Forward 30s -->
                <button class="player-control-btn" id="player-btn-forward" aria-label="Fast forward 30 seconds">
                    <svg viewBox="0 0 24 24"><path d="M12 5V1l5 5-5 5V7c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6h2c0 4.42-3.58 8-8 8s-8-3.58-8-8 3.58-8 8-8zm2 10.2h.8v-3.7h-.8v3.7zm-3.2-.2h.8v-3.5h-.8v.7h-.6v.7h.6v2.1z"/></svg>
                </button>
            </div>
            
            <!-- Progress Bar -->
            <div class="player-progress-container">
                <span id="player-time-current" class="player-time">0:00</span>
                <div class="player-progress-bar-outer" id="player-progress-outer">
                    <div class="player-progress-bar-inner" id="player-progress-inner"></div>
                </div>
                <span id="player-time-duration" class="player-time">0:00</span>
            </div>
        </div>

        <!-- Waveform visualizer & Volume -->
        <div class="player-right-controls">
            <!-- Waveform Animation -->
            <div class="waveform-animation" id="waveform-visualizer">
                <div class="waveform-bar bar-1"></div>
                <div class="waveform-bar bar-2"></div>
                <div class="waveform-bar bar-3"></div>
                <div class="waveform-bar bar-4"></div>
                <div class="waveform-bar bar-5"></div>
            </div>
            
            <!-- Volume Slider -->
            <div class="player-volume-container">
                <svg style="width: 1.2rem; height: 1.2rem;" fill="currentColor" viewBox="0 0 24 24"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>
                <input type="range" id="player-volume-slider" min="0" max="1" step="0.05" value="0.8" aria-label="Volume slider" />
            </div>

            <!-- Close player button -->
            <button class="player-close-btn" id="player-btn-close" aria-label="Close audio player">
                <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const audio = document.getElementById("main-audio-element");
    const player = document.getElementById("floating-audio-player");
    const playBtn = document.getElementById("player-btn-play");
    const playSvg = document.getElementById("play-svg");
    const pauseSvg = document.getElementById("pause-svg");
    const trackCover = document.getElementById("player-track-cover");
    const trackTitle = document.getElementById("player-track-title");
    const trackStatus = document.getElementById("player-track-status");
    const progressOuter = document.getElementById("player-progress-outer");
    const progressInner = document.getElementById("player-progress-inner");
    const timeCurrent = document.getElementById("player-time-current");
    const timeDuration = document.getElementById("player-time-duration");
    const volumeSlider = document.getElementById("player-volume-slider");
    const waveform = document.getElementById("waveform-visualizer");
    const closeBtn = document.getElementById("player-btn-close");
    const backBtn = document.getElementById("player-btn-back");
    const forwardBtn = document.getElementById("player-btn-forward");

    let currentTrackId = null;

    // 1. Setup Recently Played Hydration
    hydrateRecentlyPlayed();

    // 2. Play Actions from page triggers
    const triggers = document.querySelectorAll(".podcast-card-play-btn, .podcast-play-overlay-btn, .play-trigger-btn");
    triggers.forEach(btn => {
        btn.addEventListener("click", function(e) {
            e.preventDefault();
            const audioSrc = this.getAttribute("data-audio");
            const title = this.getAttribute("data-title");
            const cover = this.getAttribute("data-cover");
            const id = this.getAttribute("data-id");

            playTrack(id, title, cover, audioSrc);
        });
    });

    function playTrack(id, title, cover, src) {
        currentTrackId = id;
        trackTitle.textContent = title;
        trackCover.src = cover;
        audio.src = src;
        audio.load();
        
        // Add active classes to trigger styles
        player.classList.add("active");
        
        audio.play()
            .then(() => {
                updatePlayState(true);
                addToHistory(id, title, cover, src);
            })
            .catch(err => console.error("Playback failed: ", err));
    }

    function updatePlayState(isPlaying) {
        if (isPlaying) {
            playSvg.style.display = "none";
            pauseSvg.style.display = "block";
            trackStatus.textContent = "Now Playing";
            waveform.classList.add("playing");
        } else {
            playSvg.style.display = "block";
            pauseSvg.style.display = "none";
            trackStatus.textContent = "Paused";
            waveform.classList.remove("playing");
        }
    }

    // Toggle Play/Pause on main player button
    playBtn.addEventListener("click", function() {
        if (audio.paused) {
            audio.play().then(() => updatePlayState(true));
        } else {
            audio.pause();
            updatePlayState(false);
        }
    });

    // Seek Controls
    audio.addEventListener("timeupdate", function() {
        if (audio.duration) {
            const pct = (audio.currentTime / audio.duration) * 100;
            progressInner.style.width = pct + "%";
            timeCurrent.textContent = formatTime(audio.currentTime);
            timeDuration.textContent = formatTime(audio.duration);
        }
    });

    progressOuter.addEventListener("click", function(e) {
        const rect = this.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        const pct = clickX / rect.width;
        if (audio.duration) {
            audio.currentTime = pct * audio.duration;
        }
    });

    // Skip Back 10s & Forward 30s
    backBtn.addEventListener("click", function() {
        audio.currentTime = Math.max(0, audio.currentTime - 10);
    });

    forwardBtn.addEventListener("click", function() {
        if (audio.duration) {
            audio.currentTime = Math.min(audio.duration, audio.currentTime + 30);
        }
    });

    // Volume Control
    volumeSlider.addEventListener("input", function() {
        audio.volume = this.value;
    });

    // Close Player
    closeBtn.addEventListener("click", function() {
        audio.pause();
        updatePlayState(false);
        player.classList.remove("active");
    });

    // Helper: format seconds to M:SS
    function formatTime(secs) {
        if (isNaN(secs)) return "0:00";
        const m = Math.floor(secs / 60);
        const s = Math.floor(secs % 60);
        return m + ":" + (s < 10 ? "0" : "") + s;
    }

    // 3. LocalStorage History Management
    function addToHistory(id, title, cover, src) {
        let history = JSON.parse(localStorage.getItem("twl_podcast_history")) || [];
        // Remove duplicate if exists
        history = history.filter(item => item.id !== id);
        // Add to top of list
        history.unshift({ id, title, cover, src, time: Date.now() });
        // Cap at 4 items
        if (history.length > 4) history.pop();
        localStorage.setItem("twl_podcast_history", JSON.stringify(history));
        hydrateRecentlyPlayed();
    }

    function hydrateRecentlyPlayed() {
        const history = JSON.parse(localStorage.getItem("twl_podcast_history")) || [];
        const box = document.getElementById("recently-played-section");
        const list = document.getElementById("recently-played-list");

        if (history.length === 0) {
            box.style.display = "none";
            return;
        }

        box.style.display = "block";
        list.innerHTML = "";

        history.forEach(item => {
            const card = document.createElement("div");
            card.className = "post-card";
            card.style.borderRadius = "8px";
            card.style.overflow = "hidden";
            card.style.background = "#fafafa";
            card.style.display = "flex";
            card.style.alignItems = "center";
            card.style.gap = "1rem";
            card.style.padding = "1rem";

            card.innerHTML = `
                <img src="${item.cover}" alt="" style="width:60px; height:60px; object-fit:cover; border-radius:6px;" />
                <div style="flex-grow:1; display:flex; flex-direction:column; gap:0.25rem;">
                    <h5 class="serif-title" style="font-size:0.95rem; margin:0; line-height:1.2; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">${item.title}</h5>
                    <button class="btn-reset play-rec-btn" style="align-self:flex-start; font-size:0.6rem; font-weight:bold; color:var(--primary-color); border:none; background:none; cursor:pointer; padding:0;">REPLAY &rarr;</button>
                </div>
            `;

            card.querySelector(".play-rec-btn").addEventListener("click", () => {
                playTrack(item.id, item.title, item.cover, item.src);
            });

            list.appendChild(card);
        });
    }
});
</script>

<?php 
require_once PATH_ROOT . '/includes/footer.php'; 
?>
