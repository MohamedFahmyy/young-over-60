// assets/js/app.js
// Client-side interactions, AJAX forms, sliders, and animation handlers

document.addEventListener("DOMContentLoaded", function() {
    
    // ==========================================
    // 1. Theme and Color Initializer (Admin Layout)
    // ==========================================
    // Updates accessibility widget dots on load
    updateAccessibilityWidgetUI();
    window.addEventListener('twl_accessibility_change', updateAccessibilityWidgetUI);

    function updateAccessibilityWidgetUI() {
        const state = window.Accessibility ? window.Accessibility.getState() : null;
        if (!state) return;

        // Update Font Size Dots
        const dots = document.querySelectorAll('.size-dot');
        dots.forEach((dot, idx) => {
            if (idx < state.fontSize) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });

        // Update size buttons disabled state
        const minusBtn = document.querySelector('.size-btn-minus');
        const plusBtn = document.querySelector('.size-btn-plus');
        if (minusBtn) minusBtn.disabled = (state.fontSize <= 1);
        if (plusBtn) plusBtn.disabled = (state.fontSize >= 5);

        // Update Toggle Buttons active status
        const toggles = {
            'highContrast': document.querySelector('[data-toggle="highContrast"]'),
            'readableFont': document.querySelector('[data-toggle="readableFont"]'),
            'underlineLinks': document.querySelector('[data-toggle="underlineLinks"]'),
            'highlightLinks': document.querySelector('[data-toggle="highlightLinks"]')
        };

        for (const [key, btn] of Object.entries(toggles)) {
            if (btn) {
                if (state[key]) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            }
        }
    }

    // Bind accessibility widget triggers
    const widgetTriggers = document.querySelectorAll('[data-accessibility-action]');
    widgetTriggers.forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.getAttribute('data-accessibility-action');
            if (action === 'reset') {
                window.Accessibility.reset();
            } else if (action === 'decrease-font') {
                const s = window.Accessibility.getState().fontSize;
                window.Accessibility.setFontSize(s - 1);
            } else if (action === 'increase-font') {
                const s = window.Accessibility.getState().fontSize;
                window.Accessibility.setFontSize(s + 1);
            }
        });
    });

    const toggleBtns = document.querySelectorAll('[data-toggle]');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const prop = this.getAttribute('data-toggle');
            if (prop === 'highContrast') window.Accessibility.toggleHighContrast();
            if (prop === 'readableFont') window.Accessibility.toggleReadableFont();
            if (prop === 'underlineLinks') window.Accessibility.toggleUnderlineLinks();
            if (prop === 'highlightLinks') window.Accessibility.toggleHighlightLinks();
        });
    });


    // ==========================================
    // 2. Modals and Sidebars Toggles
    // ==========================================
    const backdrop = document.querySelector('.overlay-backdrop');
    const searchModal = document.querySelector('.search-modal');
    const accWidget = document.querySelector('.accessibility-widget');
    const mobileDrawer = document.querySelector('.mobile-menu-drawer');

    // Helper to open/close
    function closeAllOverlays() {
        if (backdrop) backdrop.classList.remove('active');
        if (searchModal) searchModal.classList.remove('active');
        if (accWidget) accWidget.classList.remove('active');
        if (mobileDrawer) mobileDrawer.classList.remove('active');
        document.body.classList.remove('no-scroll');
        
        const toggleBtn = document.querySelector('.mobile-toggle');
        if (toggleBtn) toggleBtn.classList.remove('open');
    }

    if (backdrop) {
        backdrop.addEventListener('click', closeAllOverlays);
    }

    // Search trigger
    const searchOpenTriggers = document.querySelectorAll('[data-open-search]');
    searchOpenTriggers.forEach(btn => {
        btn.addEventListener('click', function() {
            closeAllOverlays();
            if (backdrop) backdrop.classList.add('active');
            if (searchModal) {
                searchModal.classList.add('active');
                const inp = searchModal.querySelector('input');
                if (inp) setTimeout(() => inp.focus(), 100);
            }
        });
    });

    const searchCloseBtn = document.querySelector('[data-close-search]');
    if (searchCloseBtn) {
        searchCloseBtn.addEventListener('click', closeAllOverlays);
    }

    // Accessibility sidebar trigger
    const accOpenTriggers = document.querySelectorAll('[data-open-accessibility]');
    accOpenTriggers.forEach(btn => {
        btn.addEventListener('click', function() {
            closeAllOverlays();
            if (backdrop) backdrop.classList.add('active');
            if (accWidget) accWidget.classList.add('active');
        });
    });

    const accCloseBtn = document.querySelector('[data-close-accessibility]');
    if (accCloseBtn) {
        accCloseBtn.addEventListener('click', closeAllOverlays);
    }

    // Mobile menu trigger
    const mobileMenuTrigger = document.querySelector('.mobile-toggle');
    if (mobileMenuTrigger) {
        mobileMenuTrigger.addEventListener('click', function() {
            if (mobileDrawer) {
                const isOpen = mobileDrawer.classList.contains('active');
                closeAllOverlays();
                if (!isOpen) {
                    mobileDrawer.classList.add('active');
                    if (backdrop) backdrop.classList.add('active');
                    document.body.classList.add('no-scroll');
                    this.classList.add('open');
                }
            }
        });
    }

    // Mobile menu accordions
    const mobileNavBtns = document.querySelectorAll('.mobile-nav-btn');
    mobileNavBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const sub = document.getElementById(targetId);
            if (sub) {
                sub.classList.toggle('active');
                const arrow = this.querySelector('svg');
                if (arrow) {
                    arrow.style.transform = sub.classList.contains('active') ? 'rotate(180deg)' : 'rotate(0)';
                }
            }
        });
    });


    // ==========================================
    // 3. Slider System
    // ==========================================
    const sliderContainer = document.querySelector('.slider-inner');
    const sliderLeftBtn = document.querySelector('.slider-btn-left');
    const sliderRightBtn = document.querySelector('.slider-btn-right');

    if (sliderContainer) {
        if (sliderLeftBtn) {
            sliderLeftBtn.addEventListener('click', () => {
                sliderContainer.scrollBy({ left: -380, behavior: 'smooth' });
            });
        }
        if (sliderRightBtn) {
            sliderRightBtn.addEventListener('click', () => {
                sliderContainer.scrollBy({ left: 380, behavior: 'smooth' });
            });
        }
    }

    // ==========================================
    // 3a. Hero Slider System (Dynamic Home Slider)
    // ==========================================
    const heroSlider = document.querySelector('.hero-slider-section');
    if (heroSlider) {
        const slides = heroSlider.querySelectorAll('.hero-slide');
        const prevBtn = heroSlider.querySelector('.slider-arrow.prev');
        const nextBtn = heroSlider.querySelector('.slider-arrow.next');
        const dots = heroSlider.querySelectorAll('.slider-dot');
        let currentIndex = 0;
        let autoplayTimer = null;
        const intervalTime = 7000; // 7 seconds

        function showSlide(index) {
            if (index === currentIndex) return;
            
            // Boundary checks
            if (index >= slides.length) index = 0;
            if (index < 0) index = slides.length - 1;

            const prevSlide = slides[currentIndex];
            const nextSlide = slides[index];

            // Lazy load the image if not loaded
            const nextImg = nextSlide.querySelector('.hero-slide-img');
            if (nextImg && nextImg.getAttribute('data-src')) {
                nextImg.src = nextImg.getAttribute('data-src');
                nextImg.removeAttribute('data-src');
            }

            // Transition classes
            prevSlide.classList.remove('active');
            nextSlide.classList.add('active');

            // Update dots
            dots.forEach((dot, idx) => {
                if (idx === index) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });

            currentIndex = index;
        }

        function nextSlide() {
            showSlide(currentIndex + 1);
        }

        function prevSlide() {
            showSlide(currentIndex - 1);
        }

        // Event listeners
        if (nextBtn) nextBtn.addEventListener('click', () => {
            nextSlide();
            resetAutoplay();
        });
        if (prevBtn) prevBtn.addEventListener('click', () => {
            prevSlide();
            resetAutoplay();
        });

        dots.forEach(dot => {
            dot.addEventListener('click', function() {
                const targetIdx = parseInt(this.getAttribute('data-slide-to'), 10);
                showSlide(targetIdx);
                resetAutoplay();
            });
        });

        // Autoplay
        function startAutoplay() {
            autoplayTimer = setInterval(nextSlide, intervalTime);
        }

        function stopAutoplay() {
            clearInterval(autoplayTimer);
        }

        function resetAutoplay() {
            stopAutoplay();
            startAutoplay();
        }

        // Hover pause
        heroSlider.addEventListener('mouseenter', stopAutoplay);
        heroSlider.addEventListener('mouseleave', startAutoplay);

        // Touch Swipe support
        let startX = 0;
        let endX = 0;

        heroSlider.addEventListener('touchstart', (e) => {
            startX = e.changedTouches[0].screenX;
        }, { passive: true });

        heroSlider.addEventListener('touchend', (e) => {
            endX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });

        function handleSwipe() {
            const threshold = 50; // min distance for swipe
            if (startX - endX > threshold) {
                // Swiped Left -> Next Slide
                nextSlide();
                resetAutoplay();
            } else if (endX - startX > threshold) {
                // Swiped Right -> Prev Slide
                prevSlide();
                resetAutoplay();
            }
        }

        // Initialize
        startAutoplay();
        
        // Pre-load second slide image immediately so transition is seamless
        if (slides.length > 1) {
            const secondImg = slides[1].querySelector('.hero-slide-img');
            if (secondImg && secondImg.getAttribute('data-src')) {
                secondImg.src = secondImg.getAttribute('data-src');
                secondImg.removeAttribute('data-src');
            }
        }
    }


    // ==========================================
    // 4. Tab posts loaders with skeleton loaders
    // ==========================================
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const gridId = this.getAttribute('data-grid');
            const catId = this.getAttribute('data-category');
            const targetGrid = document.getElementById(gridId);

            if (!targetGrid) return;

            // Set active button
            const section = this.closest('.filterable-section');
            const sibs = section.querySelectorAll('.tab-btn');
            sibs.forEach(s => s.classList.remove('active'));
            this.classList.add('active');

            // Render skeleton loading cells
            targetGrid.innerHTML = `
                <div class="skeleton-loader">
                    <div class="skeleton-card">
                        <div class="skeleton-img"></div>
                        <div class="skeleton-text title"></div>
                        <div class="skeleton-text excerpt"></div>
                    </div>
                    <div class="skeleton-card">
                        <div class="skeleton-img"></div>
                        <div class="skeleton-text title"></div>
                        <div class="skeleton-text excerpt"></div>
                    </div>
                    <div class="skeleton-card">
                        <div class="skeleton-img"></div>
                        <div class="skeleton-text title"></div>
                        <div class="skeleton-text excerpt"></div>
                    </div>
                </div>
            `;

            // AJAX call to retrieve post data
            const url = `${window.BASE_URL}/api/posts?categoryId=${catId}&limit=3`;
            fetch(url)
                .then(res => res.json())
                .then(result => {
                    if (result.success && result.data.data.length > 0) {
                        targetGrid.innerHTML = '';
                        result.data.data.forEach(post => {
                            targetGrid.appendChild(createPostCardDom(post));
                        });
                        
                        // Re-trigger scroll reveals
                        initScrollReveal();
                    } else {
                        targetGrid.innerHTML = `<div style="grid-column: 1/-1; text-align:center; color:#999; padding: 4rem 0; font-style:italic;">No stories found in this category yet.</div>`;
                    }
                })
                .catch(err => {
                    console.error("AJAX Error loading posts", err);
                    targetGrid.innerHTML = `<div style="grid-column: 1/-1; text-align:center; color:#e00; padding: 4rem 0; font-style:italic;">Failed to load stories. Please try again.</div>`;
                });
        });
    });

    // Client-side mapping of returned post to post card markup
    function createPostCardDom(post) {
        const wrapper = document.createElement('div');
        const pubDate = new Date(post.publishedAt);
        const formattedDate = pubDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
        
        wrapper.className = 'post-card group';
        wrapper.setAttribute('data-scroll-reveal', '');
        
        wrapper.innerHTML = `
            <a href="${window.BASE_URL}/posts/${post.slug}" class="post-card-link" aria-label="Read ${post.title}">
                <div class="post-card-media">
                    <img src="${post.coverImage || '/images/hero-bg.png'}" alt="${post.title}" loading="lazy" class="post-card-img" />
                    <div class="post-card-badge">
                        <span class="badge-text">${post.categoryName}</span>
                    </div>
                </div>
                
                <div class="post-card-body">
                    <div class="post-card-meta">
                        <time datetime="${post.publishedAt}">${formattedDate}</time>
                        <span class="meta-dot" aria-hidden="true"></span>
                        <span>${post.authorName || 'Site Admin'}</span>
                    </div>
                    
                    <h3 class="post-card-title">${post.title}</h3>
                    
                    <p class="post-card-excerpt">
                        ${post.excerpt || ''}
                    </p>
                    
                    <div class="post-card-cta">
                        <span class="cta-text">Read Story <span class="cta-arrow" aria-hidden="true">→</span></span>
                    </div>
                </div>
            </a>
        `;
        return wrapper;
    }


    // ==========================================
    // 5. Dynamic Scroll Reveal
    // ==========================================
    let revealObserver;
    function initScrollReveal() {
        const revealElements = document.querySelectorAll('[data-scroll-reveal]');
        
        if ('IntersectionObserver' in window) {
            if (revealObserver) {
                revealObserver.disconnect();
            }

            revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            revealElements.forEach(el => revealObserver.observe(el));
        } else {
            // Fallback for older browsers
            revealElements.forEach(el => el.classList.add('revealed'));
        }
    }
    
    initScrollReveal();





    // ==========================================
    // 7. Interactive AJAX Search Input
    // ==========================================
    const searchInpField = document.querySelector('.search-input');
    const searchResultsBox = document.querySelector('.search-results');
    
    if (searchInpField && searchResultsBox) {
        let debounceTimer;
        searchInpField.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();

            if (query.length < 2) {
                searchResultsBox.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                const fetchUrl = `${window.BASE_URL}/api/posts?search=${encodeURIComponent(query)}&limit=5`;
                fetch(fetchUrl)
                    .then(res => res.json())
                    .then(result => {
                        if (result.success && result.data.data.length > 0) {
                            searchResultsBox.innerHTML = '';
                            result.data.data.forEach(post => {
                                const item = document.createElement('a');
                                item.href = `${window.BASE_URL}/posts/${post.slug}`;
                                item.className = 'search-result-item';
                                item.innerHTML = `
                                    <img src="${post.coverImage || '/images/hero-bg.png'}" alt="" />
                                    <div>
                                        <h4 class="search-result-title">${post.title}</h4>
                                        <span style="font-size: 0.65rem; text-transform: uppercase; color: #999;">${post.categoryName}</span>
                                    </div>
                                `;
                                searchResultsBox.appendChild(item);
                            });
                        } else {
                            searchResultsBox.innerHTML = `<div style="text-align: center; color: #999; padding: 2rem 0; font-style: italic;">No matching stories found.</div>`;
                        }
                    })
                    .catch(err => {
                        console.error("Failed to query search results", err);
                    });
            }, 300);
        });
    }


    // ==========================================
    // 8. Contact Form Submissions
    // ==========================================
    const contactForm = document.getElementById('contactForm');
    const contactStatusBox = document.getElementById('contactStatus');

    if (contactForm && contactStatusBox) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const origBtnText = submitBtn ? submitBtn.innerHTML : 'Send Message';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Sending...';
            }

            contactStatusBox.style.display = 'none';
            contactStatusBox.className = 'form-status-alert';

            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    contactStatusBox.classList.add('success');
                    contactStatusBox.innerHTML = `<svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Message Sent! We will contact you soon.`;
                    contactStatusBox.style.display = 'flex';
                    contactForm.reset();
                } else {
                    contactStatusBox.classList.add('error');
                    contactStatusBox.innerHTML = `<svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> ${result.error || 'Something went wrong.'}`;
                    contactStatusBox.style.display = 'flex';
                }
            })
            .catch(err => {
                contactStatusBox.classList.add('error');
                contactStatusBox.innerHTML = 'Server connection error. Please try again.';
                contactStatusBox.style.display = 'flex';
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = origBtnText;
                }
            });
        });
    }


    // ==========================================
    // 9. Newsletter Form Submissions
    // ==========================================
    const newsletterForm = document.getElementById('newsletterForm');
    const newsletterStatusBox = document.getElementById('newsletterStatus');

    if (newsletterForm && newsletterStatusBox) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const origBtnText = submitBtn ? submitBtn.innerHTML : 'Subscribe';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Subscribing...';
            }

            newsletterStatusBox.style.display = 'none';
            newsletterStatusBox.className = 'form-status-alert';

            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    newsletterStatusBox.classList.add('success');
                    newsletterStatusBox.innerHTML = `<svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Thank you! Subscribed successfully.`;
                    newsletterStatusBox.style.display = 'flex';
                    newsletterForm.reset();
                } else {
                    newsletterStatusBox.classList.add('error');
                    newsletterStatusBox.innerHTML = `<svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> ${result.error || 'Failed to subscribe.'}`;
                    newsletterStatusBox.style.display = 'flex';
                }
            })
            .catch(err => {
                newsletterStatusBox.classList.add('error');
                newsletterStatusBox.innerHTML = 'Server connection error.';
                newsletterStatusBox.style.display = 'flex';
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = origBtnText;
                }
            });
        });
    }

    // ==========================================
    // 10. Featured News Slider
    // ==========================================
    const newsSlider = document.querySelector('.featured-news-section');
    if (newsSlider) {
        const slides = newsSlider.querySelectorAll('.news-slide');
        const prevBtn = newsSlider.querySelector('.news-arrow.prev-news');
        const nextBtn = newsSlider.querySelector('.news-arrow.next-news');
        const dots = newsSlider.querySelectorAll('.news-dot');
        let currentIndex = 0;
        let autoplayTimer = null;
        const autoplayInterval = 8000; // 8 seconds

        function showSlide(index) {
            if (index === currentIndex) return;

            // Handle boundaries
            if (index >= slides.length) index = 0;
            if (index < 0) index = slides.length - 1;

            const prevSlide = slides[currentIndex];
            const nextSlide = slides[index];

            // Toggle active classes
            prevSlide.classList.remove('active');
            nextSlide.classList.add('active');

            // Update dots
            dots.forEach((dot, idx) => {
                if (idx === index) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });

            currentIndex = index;
        }

        function nextSlide() {
            showSlide(currentIndex + 1);
        }

        function prevSlide() {
            showSlide(currentIndex - 1);
        }

        if (nextBtn) nextBtn.addEventListener('click', () => {
            nextSlide();
            resetAutoplay();
        });

        if (prevBtn) prevBtn.addEventListener('click', () => {
            prevSlide();
            resetAutoplay();
        });

        dots.forEach(dot => {
            dot.addEventListener('click', function() {
                const targetIdx = parseInt(this.getAttribute('data-slide-to'), 10);
                showSlide(targetIdx);
                resetAutoplay();
            });
        });

        function startAutoplay() {
            if (slides.length > 1) {
                autoplayTimer = setInterval(nextSlide, autoplayInterval);
            }
        }

        function resetAutoplay() {
            if (autoplayTimer) {
                clearInterval(autoplayTimer);
                startAutoplay();
            }
        }

        // Initialize autoplay
        startAutoplay();
    }

});
