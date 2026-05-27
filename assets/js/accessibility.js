// assets/js/accessibility.js
// Client-side Accessibility State and DOM Controller

(function() {
    const DEFAULT_STATE = {
        fontSize: 1,
        highContrast: false,
        readableFont: false,
        underlineLinks: false,
        highlightLinks: false
    };

    let state = Object.assign({}, DEFAULT_STATE);

    // Initialize accessibility state from local storage
    function init() {
        const saved = localStorage.getItem("twl_accessibility");
        if (saved) {
            try {
                state = Object.assign({}, DEFAULT_STATE, JSON.parse(saved));
            } catch (e) {
                console.error("Failed to parse accessibility state", e);
            }
        }
        applyState();
    }

    // Apply active settings directly to the documentElement
    function applyState() {
        const html = document.documentElement;
        
        // 1. Font Size Scaling
        html.setAttribute("data-font-size", state.fontSize.toString());
        
        // 2. High Contrast
        if (state.highContrast) {
            html.classList.add("acc-high-contrast");
        } else {
            html.classList.remove("acc-high-contrast");
        }
        
        // 3. Readable Font
        if (state.readableFont) {
            html.classList.add("acc-readable-font");
        } else {
            html.classList.remove("acc-readable-font");
        }
        
        // 4. Underline Links
        if (state.underlineLinks) {
            html.classList.add("acc-underline-links");
        } else {
            html.classList.remove("acc-underline-links");
        }
        
        // 5. Highlight Links
        if (state.highlightLinks) {
            html.classList.add("acc-highlight-links");
        } else {
            html.classList.remove("acc-highlight-links");
        }

        // Save
        localStorage.setItem("twl_accessibility", JSON.stringify(state));

        // Dispatch customized event for components to listen if needed
        window.dispatchEvent(new CustomEvent('twl_accessibility_change', { detail: state }));
    }

    // Expose Global Object for widget interfaces
    window.Accessibility = {
        getState: function() {
            return state;
        },
        setFontSize: function(size) {
            state.fontSize = Math.max(1, Math.min(5, parseInt(size)));
            applyState();
        },
        toggleHighContrast: function() {
            state.highContrast = !state.highContrast;
            applyState();
        },
        toggleReadableFont: function() {
            state.readableFont = !state.readableFont;
            applyState();
        },
        toggleUnderlineLinks: function() {
            state.underlineLinks = !state.underlineLinks;
            applyState();
        },
        toggleHighlightLinks: function() {
            state.highlightLinks = !state.highlightLinks;
            applyState();
        },
        reset: function() {
            state = Object.assign({}, DEFAULT_STATE);
            applyState();
        }
    };

    // Run automatically on include
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
