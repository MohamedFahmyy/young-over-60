<?php
if (php_sapi_name() === 'cli' && !defined('DB_HOST')) {
    define('DB_HOST', '127.0.0.1');
}

define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';

$cssStyles = '<style>
/* CSS Reset / Base Overrides specifically for about page wrapper */
.about-page-wrapper {
  width: 100% !important;
  max-width: 100% !important;
  margin: 0 auto !important;
  padding: 0 !important;
  overflow-x: hidden;
  background-color: var(--background-color);
  color: var(--text-color);
  font-family: var(--body-font), system-ui, -apple-system, sans-serif;
}

.about-page-wrapper *, .about-page-wrapper *::before, .about-page-wrapper *::after {
  box-sizing: border-box;
}

/* Common Section Layout */
.about-section {
  position: relative;
  padding: 80px 24px;
  overflow: hidden;
}

.about-container-lg {
  max-width: 1200px;
  margin: 0 auto;
  position: relative;
  z-index: 10;
}

/* Gradients & Background Details */
.about-aura-left {
  position: absolute;
  top: 0;
  left: 0;
  width: 300px;
  height: 300px;
  background: radial-gradient(circle, rgba(212, 167, 92, 0.08) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
  filter: blur(40px);
}

.about-aura-right {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, rgba(15, 76, 129, 0.06) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
  filter: blur(40px);
}

.about-circle-outline {
  position: absolute;
  border: 1px solid rgba(212, 167, 92, 0.15);
  border-radius: 50%;
  pointer-events: none;
}

/* 1. HERO SECTION */
.about-hero-section {
  background-color: rgba(15, 76, 129, 0.02);
  padding: 100px 24px;
}

.about-hero-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 48px;
  align-items: center;
}

@media (min-width: 992px) {
  .about-hero-grid {
    grid-template-columns: 1.2fr 0.8fr;
  }
}

.about-hero-tagline {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  text-transform: uppercase;
  letter-spacing: 3px;
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--accent-color);
  margin-bottom: 24px;
}

.about-hero-tagline::after {
  content: "";
  width: 40px;
  height: 2px;
  background-color: var(--accent-color);
}

.about-hero-title {
  font-size: clamp(2.5rem, 5vw, 4rem);
  font-weight: 800;
  color: var(--secondary-color);
  line-height: 1.15;
  margin-top: 0;
  margin-bottom: 24px;
}

.about-hero-title span.italic-accent {
  font-style: italic;
  color: var(--accent-color);
  font-weight: 400;
}

.about-hero-quote {
  font-size: 1.15rem;
  font-style: italic;
  color: #555;
  border-left: 4px solid var(--accent-color);
  padding-left: 20px;
  margin: 24px 0;
  line-height: 1.6;
}

.about-hero-desc {
  font-size: 1.05rem;
  line-height: 1.75;
  color: var(--text-color);
  opacity: 0.85;
  margin-bottom: 40px;
  max-width: 600px;
}

.about-hero-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
}

.about-btn-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 16px 36px;
  background-color: var(--secondary-color);
  color: #fff;
  border-radius: 50px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 4px 14px rgba(30, 60, 90, 0.2);
}

.about-btn-primary:hover {
  background-color: var(--primary-color);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(30, 60, 90, 0.3);
}

.about-btn-secondary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 16px 36px;
  background-color: transparent;
  color: var(--text-color);
  border: 1px solid rgba(0,0,0,0.15);
  border-radius: 50px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
}

.about-btn-secondary:hover {
  background-color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.about-hero-image-container {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
}

.about-hero-img-frame {
  position: relative;
  border-radius: 40px;
  overflow: hidden;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  width: 100%;
  max-width: 460px;
  aspect-ratio: 4/5;
}

.about-hero-img-frame img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 2s ease;
}

.about-hero-image-container:hover .about-hero-img-frame img {
  transform: scale(1.08);
}

.about-hero-floating-card {
  position: absolute;
  bottom: -20px;
  left: -20px;
  background: #fff;
  border-radius: 24px;
  padding: 24px;
  box-shadow: 0 15px 30px rgba(0,0,0,0.1);
  z-index: 20;
  border: 1px solid rgba(0,0,0,0.03);
  transition: transform 0.4s ease;
}

.about-hero-image-container:hover .about-hero-floating-card {
  transform: translateY(-5px);
}

.about-hero-floating-card p {
  margin: 0;
  font-size: 0.85rem;
  color: #777;
}

.about-hero-floating-card h3 {
  margin: 4px 0 0 0;
  font-size: 2rem;
  font-weight: 800;
  color: var(--secondary-color);
}

/* 2. VISION SECTION */
.about-vision-section {
  background-color: #ffffff;
}

.about-vision-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 48px;
}

@media (min-width: 992px) {
  .about-vision-grid {
    grid-template-columns: 1.3fr 0.7fr;
  }
}

.about-vision-left {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.about-vision-card-light {
  background-color: var(--background-color);
  border-radius: 32px;
  padding: 40px;
  border: 1px solid var(--border-color);
}

.about-vision-card-light h3 {
  font-size: 1.8rem;
  color: var(--secondary-color);
  margin-top: 0;
  margin-bottom: 16px;
}

.about-vision-card-light p {
  font-size: 1.05rem;
  line-height: 1.7;
  color: var(--text-color);
  opacity: 0.85;
  margin: 0 0 16px 0;
}

.about-vision-card-light p:last-child {
  margin-bottom: 0;
}

.about-vision-card-dark {
  background-color: var(--secondary-color);
  color: #fff;
  border-radius: 32px;
  padding: 40px;
  position: relative;
  overflow: hidden;
}

.about-vision-card-dark h3 {
  font-size: 1.8rem;
  color: #fff;
  margin-top: 0;
  margin-bottom: 16px;
  position: relative;
  z-index: 5;
}

.about-vision-card-dark p {
  font-size: 1.05rem;
  line-height: 1.7;
  color: rgba(255,255,255,0.85);
  margin: 0 0 16px 0;
  position: relative;
  z-index: 5;
}

.about-vision-card-dark p:last-child {
  margin-bottom: 0;
}

.about-vision-right {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.about-stat-card {
  border: 1px solid var(--border-color);
  border-radius: 24px;
  padding: 32px;
  background-color: #fff;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.about-stat-card:hover {
  transform: translateY(-5px);
  border-color: var(--accent-color);
  box-shadow: 0 12px 24px rgba(0,0,0,0.03);
}

.about-stat-num {
  font-size: 3.5rem;
  font-weight: 800;
  line-height: 1;
  margin: 0;
}

.about-stat-num.accent {
  color: var(--accent-color);
}

.about-stat-num.primary {
  color: var(--secondary-color);
}

.about-stat-label {
  margin: 12px 0 0 0;
  font-size: 1rem;
  color: var(--text-color);
  font-weight: 600;
  opacity: 0.8;
}

/* 3. OFFER SECTION */
.about-offer-section {
  background-color: var(--secondary-color);
  color: #fff;
}

.about-offer-header {
  text-align: center;
  max-width: 800px;
  margin: 0 auto 60px auto;
}

.about-offer-header .tagline {
  color: var(--accent-color);
  text-transform: uppercase;
  letter-spacing: 3px;
  font-size: 0.85rem;
  font-weight: 600;
  display: block;
  margin-bottom: 16px;
}

.about-offer-header h2 {
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 700;
  color: #fff;
  margin: 0 0 20px 0;
  line-height: 1.2;
}

.about-offer-header h2 span {
  color: var(--accent-color);
  font-style: italic;
  font-weight: 400;
}

.about-offer-header p {
  color: rgba(255,255,255,0.75);
  font-size: 1.1rem;
  line-height: 1.6;
}

/* Offers Grid for Desktop */
.about-offer-grid {
  display: none;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 30px;
}

@media (min-width: 768px) {
  .about-offer-grid {
    display: grid;
  }
}

.about-offer-card {
  background-color: var(--surface-color);
  border-radius: 28px;
  padding: 40px 30px;
  color: var(--text-color);
  border: 1px solid var(--border-color);
  transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
  display: flex;
  flex-direction: column;
  height: 100%;
}

.about-offer-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
  border-color: var(--accent-color);
}

.about-offer-icon {
  width: 64px;
  height: 64px;
  background-color: rgba(212, 167, 92, 0.1);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.75rem;
  color: var(--secondary-color);
  margin-bottom: 28px;
  transition: all 0.3s ease;
}

.about-offer-card:hover .about-offer-icon {
  transform: scale(1.1) rotate(3deg);
  background-color: var(--accent-color);
  color: #fff;
}

.about-offer-title {
  font-size: 1.35rem;
  font-weight: 700;
  color: var(--secondary-color);
  margin-top: 0;
  margin-bottom: 16px;
}

.about-offer-desc {
  font-size: 0.95rem;
  line-height: 1.6;
  color: var(--text-color);
  opacity: 0.8;
  margin: 0;
  flex-grow: 1;
}

/* Offers Slider for Mobile Only */
.about-offer-carousel-wrap {
  display: block;
  width: 100%;
  overflow-x: auto;
  padding-bottom: 20px;
  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
}

@media (min-width: 768px) {
  .about-offer-carousel-wrap {
    display: none;
  }
}

.about-offer-carousel {
  display: flex;
  gap: 20px;
  width: max-content;
  padding: 0 10px;
}

.about-offer-carousel .about-offer-card {
  width: 290px;
  scroll-snap-align: center;
}

/* 4. PHILOSOPHY SECTION */
.about-philosophy-section {
  background-color: var(--background-color);
  padding: 100px 24px;
}

.about-philosophy-bg-num {
  position: absolute;
  right: 5%;
  top: 50%;
  transform: translateY(-50%);
  font-size: 24rem;
  font-weight: 900;
  color: var(--secondary-color);
  opacity: 0.03;
  line-height: 1;
  pointer-events: none;
  user-select: none;
}

.about-philosophy-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 60px;
  align-items: center;
}

@media (min-width: 992px) {
  .about-philosophy-grid {
    grid-template-columns: 1fr 1fr;
  }
}

.about-philosophy-left {
  background-color: rgba(0,0,0,0.03);
  padding: 40px;
  border-radius: 28px;
  border: 1px solid rgba(0,0,0,0.03);
  transition: all 0.3s ease;
}

.about-philosophy-left:hover {
  border-color: rgba(212, 167, 92, 0.2);
  box-shadow: 0 10px 30px rgba(0,0,0,0.03);
}

.about-philosophy-left .tagline {
  color: var(--accent-color);
  text-transform: uppercase;
  letter-spacing: 3px;
  font-size: 0.85rem;
  font-weight: 600;
  display: block;
  margin-bottom: 16px;
}

.about-philosophy-left h2 {
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 700;
  color: var(--secondary-color);
  margin: 0 0 30px 0;
  line-height: 1.2;
}

.about-philosophy-left h2 span {
  color: var(--accent-color);
  font-style: italic;
  font-weight: 400;
}

.about-philosophy-quote {
  font-size: clamp(1.35rem, 3.5vw, 1.85rem);
  font-weight: 300;
  font-style: italic;
  color: var(--secondary-color);
  line-height: 1.5;
  margin: 0;
}

.about-philosophy-right {
  background: #fff;
  border-radius: 36px;
  padding: 40px;
  border: 1px solid var(--border-color);
  box-shadow: 0 15px 35px rgba(0,0,0,0.02);
  transition: all 0.4s ease;
}

.about-philosophy-right:hover {
  transform: translateY(-4px);
  box-shadow: 0 25px 50px rgba(0,0,0,0.06);
  border-color: rgba(212, 167, 92, 0.15);
}

.about-philosophy-right p {
  font-size: 1.05rem;
  line-height: 1.75;
  color: var(--text-color);
  opacity: 0.85;
  margin-top: 0;
  margin-bottom: 24px;
}

.about-philosophy-right p:last-of-type {
  margin-bottom: 30px;
}

.about-philosophy-link {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  color: var(--accent-color);
  font-weight: 700;
  text-decoration: none;
  font-size: 1.05rem;
  transition: all 0.3s ease;
}

.about-philosophy-link::before {
  content: "";
  width: 30px;
  height: 2px;
  background-color: var(--accent-color);
  transition: width 0.3s ease;
}

.about-philosophy-link:hover::before {
  width: 45px;
}

.about-philosophy-link:hover {
  color: var(--secondary-color);
}

/* 5. CONTACT / FOUNDER SECTION */
.about-contact-section {
  background-color: #ffffff;
  padding: 80px 24px;
}

.about-contact-header {
  text-align: center;
  max-width: 700px;
  margin: 0 auto 60px auto;
}

.about-contact-header .tagline {
  color: var(--accent-color);
  text-transform: uppercase;
  letter-spacing: 3px;
  font-size: 0.85rem;
  font-weight: 600;
  display: block;
  margin-bottom: 16px;
}

.about-contact-header h2 {
  font-size: clamp(2rem, 3.5vw, 2.75rem);
  font-weight: 700;
  color: var(--secondary-color);
  margin: 0 0 16px 0;
}

.about-contact-header h2 span {
  color: var(--accent-color);
  font-style: italic;
  font-weight: 400;
}

.about-contact-header p {
  font-size: 1.05rem;
  color: var(--text-color);
  opacity: 0.8;
  margin: 0;
}

.about-contact-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 40px;
}

@media (min-width: 992px) {
  .about-contact-grid {
    grid-template-columns: 1fr 1fr;
  }
}

.about-founder-card {
  background-color: #fff;
  border-radius: 32px;
  padding: 40px;
  border: 1px solid var(--border-color);
  box-shadow: 0 15px 35px rgba(0,0,0,0.03);
  text-decoration: none;
  color: inherit;
  display: flex;
  flex-direction: column;
  transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.about-founder-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 25px 50px rgba(0,0,0,0.08);
  border-color: var(--accent-color);
}

.about-founder-profile {
  display: flex;
  align-items: center;
  gap: 24px;
  margin-bottom: 30px;
}

.about-founder-img-wrap {
  width: 90px;
  height: 90px;
  border-radius: 20px;
  overflow: hidden;
  position: relative;
  box-shadow: 0 8px 16px rgba(0,0,0,0.08);
}

.about-founder-img-wrap img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.about-founder-img-wrap::after {
  content: "";
  position: absolute;
  inset: 0;
  border-radius: 20px;
  border: 2px solid rgba(212, 167, 92, 0.2);
  transition: all 0.3s ease;
}

.about-founder-card:hover .about-founder-img-wrap::after {
  border-color: var(--accent-color);
}

.about-founder-name {
  font-size: 1.5rem;
  font-weight: 800;
  color: var(--secondary-color);
  margin: 0 0 6px 0;
  transition: color 0.3s ease;
}

.about-founder-card:hover .about-founder-name {
  color: var(--accent-color);
}

.about-founder-role {
  margin: 0;
  font-size: 0.9rem;
  color: #777;
  font-weight: 500;
}

.about-founder-linkedin-link {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 12px;
  color: var(--secondary-color);
  font-weight: 700;
  font-size: 0.9rem;
}

.about-founder-card:hover .about-founder-linkedin-link {
  color: var(--accent-color);
}

.about-founder-quote {
  font-size: 1.05rem;
  line-height: 1.7;
  font-style: italic;
  color: var(--text-color);
  opacity: 0.85;
  margin: 0;
  border-top: 1px solid var(--border-color);
  padding-top: 24px;
}

.about-contact-form-panel {
  background-color: #fff;
  border-radius: 32px;
  padding: 40px;
  border: 1px solid var(--border-color);
  box-shadow: 0 15px 35px rgba(0,0,0,0.03);
}

.about-contact-form-panel h3 {
  font-size: 1.5rem;
  font-weight: 800;
  color: var(--secondary-color);
  margin-top: 0;
  margin-bottom: 24px;
}

.about-form-group {
  margin-bottom: 20px;
}

.about-form-input {
  width: 100%;
  padding: 16px 20px;
  border-radius: 14px;
  border: 1px solid rgba(0,0,0,0.1);
  font-family: inherit;
  font-size: 0.95rem;
  color: var(--text-color);
  background-color: #fafafa;
  transition: all 0.3s ease;
}

.about-form-input:focus {
  outline: none;
  border-color: var(--accent-color);
  background-color: #fff;
  box-shadow: 0 4px 12px rgba(214, 167, 92, 0.05);
}

textarea.about-form-input {
  resize: vertical;
}

.about-form-submit {
  width: 100%;
  padding: 18px;
  background-color: var(--secondary-color);
  color: #fff;
  border: none;
  border-radius: 14px;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(30, 60, 90, 0.15);
}

.about-form-submit:hover {
  background-color: var(--primary-color);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(30, 60, 90, 0.25);
}

.about-form-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* RTL OVERRIDES */
html[dir="rtl"] .about-page-wrapper {
  text-align: right;
}

html[dir="rtl"] .about-hero-tagline::after {
  display: none;
}

html[dir="rtl"] .about-hero-tagline::before {
  content: "";
  width: 40px;
  height: 2px;
  background-color: var(--accent-color);
}

html[dir="rtl"] .about-hero-tagline {
  gap: 12px;
}

html[dir="rtl"] .about-hero-quote {
  border-left: none;
  border-right: 4px solid var(--accent-color);
  padding-left: 0;
  padding-right: 20px;
}

html[dir="rtl"] .about-hero-floating-card {
  left: auto;
  right: -20px;
}

html[dir="rtl"] .about-philosophy-left {
  border-left: 1px solid rgba(0,0,0,0.03);
}

html[dir="rtl"] .about-philosophy-link::before {
  display: none;
}

html[dir="rtl"] .about-philosophy-link::after {
  content: "";
  width: 30px;
  height: 2px;
  background-color: var(--accent-color);
  transition: width 0.3s ease;
  margin-right: 12px;
}

html[dir="rtl"] .about-philosophy-link:hover::after {
  width: 45px;
}

html[dir="rtl"] .about-philosophy-bg-num {
  right: auto;
  left: 5%;
}
</style>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("aboutContactForm");
    const statusDiv = document.getElementById("aboutFormStatus");
    
    if (form) {
        form.addEventListener("submit", function(e) {
            e.preventDefault();
            
            statusDiv.style.display = "none";
            statusDiv.className = "form-status-alert";
            statusDiv.textContent = "";
            
            // Get CSRF Token from DOM (newsletter form)
            const csrfInput = document.querySelector(\'input[name="csrf_token"]\');
            if (!csrfInput) {
                statusDiv.style.display = "block";
                statusDiv.style.backgroundColor = "#ffebee";
                statusDiv.style.color = "#c62828";
                statusDiv.textContent = window.CURRENT_LANG === "ar" ? "حدث خطأ في التحقق من الأمان." : "Security validation token missing.";
                return;
            }
            
            const formData = new FormData(form);
            formData.append("csrf_token", csrfInput.value);
            
            const submitBtn = form.querySelector(\'button[type="submit"]\');
            const originalBtnText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = window.CURRENT_LANG === "ar" ? "جاري الإرسال..." : "Sending...";
            
            fetch(window.BASE_URL + "/api/contact", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
                
                statusDiv.style.display = "block";
                if (data.success) {
                    statusDiv.style.backgroundColor = "#e8f5e9";
                    statusDiv.style.color = "#2e7d32";
                    statusDiv.textContent = window.CURRENT_LANG === "ar" ? "تم إرسال رسالتك بنجاح. سنرد عليك قريبًا." : "Your message has been sent successfully. We will get back to you shortly.";
                    form.reset();
                } else {
                    statusDiv.style.backgroundColor = "#ffebee";
                    statusDiv.style.color = "#c62828";
                    statusDiv.textContent = data.error || (window.CURRENT_LANG === "ar" ? "فشل إرسال الرسالة. يرجى المحاولة مرة أخرى." : "Failed to send message. Please try again.");
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
                statusDiv.style.display = "block";
                statusDiv.style.backgroundColor = "#ffebee";
                statusDiv.style.color = "#c62828";
                statusDiv.textContent = window.CURRENT_LANG === "ar" ? "خطأ في الاتصال بالخادم. يرجى المحاولة لاحقًا." : "Server connection error. Please try again later.";
                console.error("Error:", error);
            });
        });
    }
});
</script>';

$contentEn = $cssStyles . "\n" . '<div class="about-page-wrapper">
  <!-- 1. Hero Section -->
  <section class="about-section about-hero-section">
    <div class="about-aura-left"></div>
    <div class="about-container-lg about-hero-grid">
      <div>
        <span class="about-hero-tagline">Our Story</span>
        <h1 class="about-hero-title">
          Beyond 60,<br>
          <span class="italic-accent">life begins</span> again.
        </h1>
        <div class="about-hero-quote">
          "We believe that travel is a fundamental right for everyone. Life begins after 60, when experience deepens, passion grows, and the desire to explore matures without limits."
        </div>
        <p class="about-hero-desc">
          We are not just another travel news website. We are a vibrant platform, designed especially for those who see age as just a number—one that does not define spirit or ambition. We truly believe that life begins after 60, when experience deepens, passion grows, and the desire to explore matures without limits.
        </p>
        <div class="about-hero-buttons">
          <a href="#about-vision" class="about-btn-primary">Our Vision</a>
          <a href="#about-contact" class="about-btn-secondary">Get in Touch</a>
        </div>
      </div>
      <div class="about-hero-image-container">
        <div class="about-hero-img-frame">
          <img src="/assets/images/about-header.jpeg" alt="About Us" />
        </div>
        <div class="about-hero-floating-card">
          <p>Stories Shared</p>
          <h3>500+</h3>
        </div>
      </div>
    </div>
  </section>

  <!-- 2. Vision Section -->
  <section id="about-vision" class="about-section about-vision-section">
    <div class="about-container-lg about-vision-grid">
      <div class="about-vision-left">
        <div class="about-vision-card-light">
          <h3>Breaking Stereotypes</h3>
          <p>
            We work to break the stereotypes that traveling is difficult or unsuitable after a certain age—or for people with disabilities. On the contrary, we see in you an enduring youthful energy, and we want to be your trusted guide to exploring the world safely and with dignity.
          </p>
          <p>
            Our goal is to build a community where stories of active travel inspire others to step out of their comfort zones.
          </p>
        </div>
        <div class="about-vision-card-dark">
          <div class="about-aura-right" style="background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);"></div>
          <h3>Empowering Travelers</h3>
          <p>
            By providing accurate accessibility reports and sharing real-life journeys, we empower you to plan with confidence and discover the world\'s most beautiful destinations.
          </p>
          <p>
            We collaborate with local experts and international hospitality leaders to advocate for higher accessibility standards across all travel sectors.
          </p>
        </div>
      </div>
      <div class="about-vision-right">
        <div class="about-stat-card">
          <h4 class="about-stat-num accent">60+</h4>
          <p class="about-stat-label">Active Seniors Community</p>
        </div>
        <div class="about-stat-card">
          <h4 class="about-stat-num primary">∞</h4>
          <p class="about-stat-label">Limitless Adventures</p>
        </div>
        <div class="about-stat-card">
          <h4 class="about-stat-num accent">100%</h4>
          <p class="about-stat-label">Accessibility Mapping</p>
        </div>
      </div>
    </div>
  </section>

  <!-- 3. What We Offer Section -->
  <section class="about-section about-offer-section">
    <div class="about-container-lg">
      <div class="about-offer-header">
        <span class="tagline">What We Offer</span>
        <h2>Tailored Services for Your <span>Comfort</span></h2>
        <p>We offer specialized news, in-depth travel reports, real stories, and practical guides designed to make your journey seamless and inspiring.</p>
      </div>

      <!-- Desktop Grid -->
      <div class="about-offer-grid">
        <div class="about-offer-card">
          <div class="about-offer-icon">📰</div>
          <h3 class="about-offer-title">Specialized News</h3>
          <p class="about-offer-desc">The latest trends in senior and accessible travel—from airline policies to hotels equipped with the highest standards.</p>
        </div>
        <div class="about-offer-card">
          <div class="about-offer-icon">🗺️</div>
          <h3 class="about-offer-title">In-Depth Reports</h3>
          <p class="about-offer-desc">Destinations rated by accessibility, ease of movement, and availability of suitable medical and recreational services.</p>
        </div>
        <div class="about-offer-card">
          <div class="about-offer-icon">✨</div>
          <h3 class="about-offer-title">Stories & Inspiration</h3>
          <p class="about-offer-desc">Real-life experiences of people over 60 or with special needs who prove that adventure knows no age or obstacle.</p>
        </div>
        <div class="about-offer-card">
          <div class="about-offer-icon">📘</div>
          <h3 class="about-offer-title">Practical Guides</h3>
          <p class="about-offer-desc">From planning your trip and handling health insurance to the best means of transport and accommodation designed for your comfort.</p>
        </div>
      </div>

      <!-- Mobile Carousel Slider -->
      <div class="about-offer-carousel-wrap">
        <div class="about-offer-carousel">
          <div class="about-offer-card">
            <div class="about-offer-icon">📰</div>
            <h3 class="about-offer-title">Specialized News</h3>
            <p class="about-offer-desc">The latest trends in senior and accessible travel—from airline policies to hotels equipped with the highest standards.</p>
          </div>
          <div class="about-offer-card">
            <div class="about-offer-icon">🗺️</div>
            <h3 class="about-offer-title">In-Depth Reports</h3>
            <p class="about-offer-desc">Destinations rated by accessibility, ease of movement, and availability of suitable medical and recreational services.</p>
          </div>
          <div class="about-offer-card">
            <div class="about-offer-icon">✨</div>
            <h3 class="about-offer-title">Stories & Inspiration</h3>
            <p class="about-offer-desc">Real-life experiences of people over 60 or with special needs who prove that adventure knows no age or obstacle.</p>
          </div>
          <div class="about-offer-card">
            <div class="about-offer-icon">📘</div>
            <h3 class="about-offer-title">Practical Guides</h3>
            <p class="about-offer-desc">From planning your trip and handling health insurance to the best means of transport and accommodation designed for your comfort.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 4. Philosophy Section -->
  <section class="about-section about-philosophy-section">
    <div class="about-philosophy-bg-num">60</div>
    <div class="about-container-lg about-philosophy-grid">
      <div class="about-philosophy-left">
        <span class="tagline">Our Philosophy</span>
        <h2>Life Begins After <span>Sixty</span></h2>
        <blockquote class="about-philosophy-quote">
          "We reject the idea of reducing a person to the number on their passport. Sixty is not the end of the road—it is a new starting point toward a life full of discovery."
        </blockquote>
      </div>
      <div class="about-philosophy-right">
        <p>
          True youth lies in a fresh spirit and a curious mind, and these never grow old. At Young over 60, we see every traveler as a story worth telling, and every journey as a chance to renew life.
        </p>
        <p>
          We believe that experience is the ultimate asset, and senior years are the perfect time to explore the world with a deeper understanding and appreciation.
        </p>
        <p>
          Join us, because the world is too big to be seen only from a window, and because your right to travel never expires.
        </p>
        <a href="/news" class="about-philosophy-link">Read traveler stories</a>
      </div>
    </div>
  </section>

  <!-- 5. Contact / Founder Section -->
  <section id="about-contact" class="about-section about-contact-section">
    <div class="about-container-lg">
      <div class="about-contact-header">
        <span class="tagline">Get in Touch</span>
        <h2>We\'d Love to Hear From <span>You</span></h2>
        <p>Have questions about accessibility features at our destinations? Want to contribute a story? Reach out below.</p>
      </div>
      
      <div class="about-contact-grid">
        <!-- Founder Card -->
        <a href="https://www.linkedin.com/in/zakaria-dawoud-26902b180?utm_source=share_via&utm_content=profile&utm_medium=member_android" target="_blank" class="about-founder-card">
          <div class="about-founder-profile">
            <div class="about-founder-img-wrap">
              <img src="/assets/images/founder.jpeg" alt="Zakaria Dawoud" />
            </div>
            <div>
              <h3 class="about-founder-name">Zakaria Dawoud</h3>
              <p class="about-founder-role">Founder of Young Over 60</p>
              <div class="about-founder-linkedin-link">
                <span>Connect on LinkedIn</span>
                <span>→</span>
              </div>
            </div>
          </div>
          <p class="about-founder-quote">
            "My dream is to create a world where every senior can travel safely, comfortably, and without limits. Let\'s make this journey together."
          </p>
        </a>

        <!-- Contact Form -->
        <div class="about-contact-form-panel">
          <h3>Send a Message</h3>
          <div id="aboutFormStatus" class="form-status-alert" style="display: none; padding: 12px; margin-bottom: 20px; border-radius: 8px;"></div>
          <form id="aboutContactForm" class="about-form">
            <div class="about-form-group">
              <input type="text" name="name" class="about-form-input" placeholder="Your Name" required />
            </div>
            <div class="about-form-group">
              <input type="email" name="email" class="about-form-input" placeholder="Your Email" required />
            </div>
            <div class="about-form-group">
              <input type="text" name="phone" class="about-form-input" placeholder="Phone Number" required />
            </div>
            <div class="about-form-group">
              <textarea name="message" class="about-form-input" rows="5" placeholder="Your Message" required></textarea>
            </div>
            <button type="submit" class="about-form-submit">Send Message</button>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>';

$contentAr = $cssStyles . "\n" . '<div class="about-page-wrapper">
  <!-- 1. Hero Section -->
  <section class="about-section about-hero-section">
    <div class="about-aura-left"></div>
    <div class="about-container-lg about-hero-grid">
      <div>
        <span class="about-hero-tagline">قصتنا</span>
        <h1 class="about-hero-title">
          بعد الستين،<br>
          <span class="italic-accent">تبدأ الحياة</span> من جديد.
        </h1>
        <div class="about-hero-quote">
          "نؤمن بأن السياحة حق أساسي للجميع. تبدأ الحياة الحقيقية بعد الستين، حيث تتسع الخبرة، ويزداد الشغف، وتنضج الرغبة في الاستكشاف دون قيود."
        </div>
        <p class="about-hero-desc">
          نحن لسنا مجرد موقع سياحي إخباري؛ نحن منصة نابضة بالحياة، صممت خصيصًا لمن يرى أن العمر مجرد رقم لا يحدد الروح ولا الطموح. نؤمن بأن الحياة الحقيقية تبدأ بعد الستين، حيث تتسع الخبرة، ويزداد الشغف، وتنضج الرغبة في الاستكشاف دون قيود.
        </p>
        <div class="about-hero-buttons">
          <a href="#about-vision" class="about-btn-primary">رؤيتنا</a>
          <a href="#about-contact" class="about-btn-secondary">تواصل معنا</a>
        </div>
      </div>
      <div class="about-hero-image-container">
        <div class="about-hero-img-frame">
          <img src="/assets/images/about-header.jpeg" alt="من نحن" />
        </div>
        <div class="about-hero-floating-card">
          <p>تجارب مشتركة</p>
          <h3>+500</h3>
        </div>
      </div>
    </div>
  </section>

  <!-- 2. Vision Section -->
  <section id="about-vision" class="about-section about-vision-section">
    <div class="about-container-lg about-vision-grid">
      <div class="about-vision-left">
        <div class="about-vision-card-light">
          <h3>كسر الصور النمطية</h3>
          <p>
            نعمل على كسر الصور النمطية التي تروّج أن السفر صعب أو غير مناسب بعد عمر معين، أو لأصحاب الهمم. بالعكس، نحن نرى فيكم طاقة الشباب المستمر، ونريد أن نكون دليلكم الموثوق لاستكشاف العالم بأمان ورقي.
          </p>
          <p>
            هدفنا هو بناء مجتمع تلهم فيه قصص السفر النشط الآخرين للخروج من مناطق الراحة الخاصة بهم.
          </p>
        </div>
        <div class="about-vision-card-dark">
          <div class="about-aura-right" style="background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);"></div>
          <h3>تمكين المسافرين</h3>
          <p>
            من خلال تقديم تقارير دقيقة حول إمكانية الوصول ومشاركة تجارب السفر الواقعية، نمكنكم من التخطيط بثقة واكتشاف أجمل وجهات العالم.
          </p>
          <p>
            نتعاون مع الخبراء المحليين ورواد الضيافة الدوليين للدعوة إلى مستويات أعلى لإمكانية الوصول في جميع قطاعات السفر.
          </p>
        </div>
      </div>
      <div class="about-vision-right">
        <div class="about-stat-card">
          <h4 class="about-stat-num accent">+60</h4>
          <p class="about-stat-label">مجتمع كبار السن النشطين</p>
        </div>
        <div class="about-stat-card">
          <h4 class="about-stat-num primary">∞</h4>
          <p class="about-stat-label">مغامرات بلا حدود</p>
        </div>
        <div class="about-stat-card">
          <h4 class="about-stat-num accent">100%</h4>
          <p class="about-stat-label">خرائط سهولة الوصول</p>
        </div>
      </div>
    </div>
  </section>

  <!-- 3. What We Offer Section -->
  <section class="about-section about-offer-section">
    <div class="about-container-lg">
      <div class="about-offer-header">
        <span class="tagline">ماذا نقدم</span>
        <h2>خدمات مخصصة لضمان <span>راحتكم</span></h2>
        <p>نقدم أخباراً متخصصة، وتقارير سفر معمقة، وقصصاً حقيقية، وأدلة عملية مصممة لجعل رحلتكم سلسة وملهمة.</p>
      </div>

      <!-- Desktop Grid -->
      <div class="about-offer-grid">
        <div class="about-offer-card">
          <div class="about-offer-icon">📰</div>
          <h3 class="about-offer-title">أخبار متخصصة</h3>
          <p class="about-offer-desc">أحدث الاتجاهات في سياحة كبار السن وذوي الاحتياجات الخاصة، من سياسات الطيران المريحة إلى الفنادق المجهزة بأعلى المعايير.</p>
        </div>
        <div class="about-offer-card">
          <div class="about-offer-icon">🗺️</div>
          <h3 class="about-offer-title">تقارير معمقة</h3>
          <p class="about-offer-desc">وجهات مصنفة وفق معايير إمكانية الوصول، وسهولة الحركة، وتوافر الخدمات الطبية والترفيهية المناسبة.</p>
        </div>
        <div class="about-offer-card">
          <div class="about-offer-icon">✨</div>
          <h3 class="about-offer-title">قصص وإلهام</h3>
          <p class="about-offer-desc">تجارب حقيقية لأشخاص تجاوزوا الستين أو من ذوي الاحتياجات الخاصة، وأثبتوا أن المغامرة لا تعرف عمرًا ولا عائقًا.</p>
        </div>
        <div class="about-offer-card">
          <div class="about-offer-icon">📘</div>
          <h3 class="about-offer-title">أدلة عملية</h3>
          <p class="about-offer-desc">من كيفية التخطيط للرحلة، والتعامل مع التأمين الصحي، إلى أفضل وسائل النقل والإقامة المصممة لراحتكم.</p>
        </div>
      </div>

      <!-- Mobile Carousel Slider -->
      <div class="about-offer-carousel-wrap">
        <div class="about-offer-carousel">
          <div class="about-offer-card">
            <div class="about-offer-icon">📰</div>
            <h3 class="about-offer-title">أخبار متخصصة</h3>
            <p class="about-offer-desc">أحدث الاتجاهات في سياحة كبار السن وذوي الاحتياجات الخاصة، من سياسات الطيران المريحة إلى الفنادق المجهزة بأعلى المعايير.</p>
          </div>
          <div class="about-offer-card">
            <div class="about-offer-icon">🗺️</div>
            <h3 class="about-offer-title">تقارير معمقة</h3>
            <p class="about-offer-desc">وجهات مصنفة وفق معايير إمكانية الوصول، وسهولة الحركة، وتوافر الخدمات الطبية والترفيهية المناسبة.</p>
          </div>
          <div class="about-offer-card">
            <div class="about-offer-icon">✨</div>
            <h3 class="about-offer-title">قصص وإلهام</h3>
            <p class="about-offer-desc">تجارب حقيقية لأشخاص تجاوزوا الستين أو من ذوي الاحتياجات الخاصة، وأثبتوا أن المغامرة لا تعرف عمرًا ولا عائقًا.</p>
          </div>
          <div class="about-offer-card">
            <div class="about-offer-icon">📘</div>
            <h3 class="about-offer-title">أدلة عملية</h3>
            <p class="about-offer-desc">من كيفية التخطيط للرحلة، والتعامل مع التأمين الصحي، إلى أفضل وسائل النقل والإقامة المصممة لراحتكم.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 4. Philosophy Section -->
  <section class="about-section about-philosophy-section">
    <div class="about-philosophy-bg-num">60</div>
    <div class="about-container-lg about-philosophy-grid">
      <div class="about-philosophy-left">
        <span class="tagline">فلسفتنا</span>
        <h2>الحياة تبدأ بعد <span>الستين</span></h2>
        <blockquote class="about-philosophy-quote">
          "نحن نرفض أن يُختزل الإنسان في رقم في جواز سفره. الستون ليست نهاية الطريق، بل هي محطة انطلاق جديدة نحو حياة مليئة بالاكتشافات."
        </blockquote>
      </div>
      <div class="about-philosophy-right">
        <p>
          فالشباب الحقيقي هو نضارة الروح وفضول العقل، وهذان لا يشيخان أبدًا. في شباب فوق الستين، نرى في كل مسافر قصة تستحق أن تُروى، وفي كل رحلة فرصة لتجديد الحياة.
        </p>
        <p>
          نحن نؤمن بأن الخبرة هي الأصل الأهم، وأن سنوات النضج هي الوقت الأمثل لاستكشاف العالم بفهم وتقدير أعمق.
        </p>
        <p>
          انضم إلينا، لأن العالم أكبر من أن يُرى من النافذة فقط، ولأن حقك في السياحة لا يسقط بالتقادم.
        </p>
        <a href="/news" class="about-philosophy-link">اقرأ قصص المسافرين</a>
      </div>
    </div>
  </section>

  <!-- 5. Contact / Founder Section -->
  <section id="about-contact" class="about-section about-contact-section">
    <div class="about-container-lg">
      <div class="about-contact-header">
        <span class="tagline">تواصل معنا</span>
        <h2>يسعدنا دائماً السماع <span>منكم</span></h2>
        <p>هل لديك أسئلة حول ميزات إمكانية الوصول في وجهاتنا؟ هل ترغب في المساهمة بقصة؟ تواصل معنا أدناه.</p>
      </div>
      
      <div class="about-contact-grid">
        <!-- Founder Card -->
        <a href="https://www.linkedin.com/in/zakaria-dawoud-26902b180?utm_source=share_via&utm_content=profile&utm_medium=member_android" target="_blank" class="about-founder-card">
          <div class="about-founder-profile">
            <div class="about-founder-img-wrap">
              <img src="/assets/images/founder.jpeg" alt="زكريا داود" />
            </div>
            <div>
              <h3 class="about-founder-name">زكريا داود</h3>
              <p class="about-founder-role">مؤسس شباب فوق الستين</p>
              <div class="about-founder-linkedin-link">
                <span>تواصل عبر لينكد إن</span>
                <span>←</span>
              </div>
            </div>
          </div>
          <p class="about-founder-quote">
            "حلمي هو خلق عالم يمكن فيه لكل مسافر متقدم في السن السفر بأمان وراحة ودون حدود. دعونا نخوض هذه الرحلة معاً."
          </p>
        </a>

        <!-- Contact Form -->
        <div class="about-contact-form-panel">
          <h3>أرسل رسالة</h3>
          <div id="aboutFormStatus" class="form-status-alert" style="display: none; padding: 12px; margin-bottom: 20px; border-radius: 8px;"></div>
          <form id="aboutContactForm" class="about-form">
            <div class="about-form-group">
              <input type="text" name="name" class="about-form-input" placeholder="الاسم الكامل" required />
            </div>
            <div class="about-form-group">
              <input type="email" name="email" class="about-form-input" placeholder="البريد الإلكتروني" required />
            </div>
            <div class="about-form-group">
              <input type="text" name="phone" class="about-form-input" placeholder="رقم الهاتف" required />
            </div>
            <div class="about-form-group">
              <textarea name="message" class="about-form-input" rows="5" placeholder="نص الرسالة" required></textarea>
            </div>
            <button type="submit" class="about-form-submit">إرسال الرسالة</button>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>';

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if about page exists
    $stmt = $db->prepare("SELECT id FROM custom_pages WHERE id = 'page-about' OR slug_en = 'about-us' LIMIT 1");
    $stmt->execute();
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        $pageId = $existing['id'];
        echo "Updating existing About Us page (ID: {$pageId})...\n";
        
        $updateStmt = $db->prepare("
            UPDATE custom_pages SET
                title_en = 'About Us',
                title_ar = 'من نحن',
                slug_en = 'about-us',
                slug_ar = 'من-نحن',
                content_en = :content_en,
                content_ar = :content_ar,
                template_type = 'about',
                hero_title_en = '',
                hero_title_ar = '',
                hero_subtitle_en = '',
                hero_subtitle_ar = '',
                hero_image = '',
                featured_image = '',
                is_published = 1,
                show_in_menu = 1,
                menu_title_en = 'About Us',
                menu_title_ar = 'من نحن',
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        $updateStmt->execute([
            ':content_en' => $contentEn,
            ':content_ar' => $contentAr,
            ':id' => $pageId
        ]);
        echo "✓ About Us page updated successfully.\n";
    } else {
        echo "About Us page not found, creating new page...\n";
        $pageId = 'page-about';
        
        $insertStmt = $db->prepare("
            INSERT INTO custom_pages (
                id, slug_en, slug_ar, title_en, title_ar, content_en, content_ar, 
                template_type, sort_order, show_in_menu, menu_title_en, menu_title_ar,
                hero_title_en, hero_title_ar, hero_subtitle_en, hero_subtitle_ar, hero_image,
                meta_title_en, meta_title_ar, meta_description_en, meta_description_ar, is_published,
                created_at, updated_at
            ) VALUES (
                :id, 'about-us', 'من-نحن', 'About Us', 'من نحن', :content_en, :content_ar, 
                'about', 1, 1, 'About Us', 'من نحن',
                '', '', '', '', '',
                'About Us | Travel Without Boundaries', 'من نحن | شباب فوق الستين', 
                'Beyond 60, life begins again. Learn more about Young Over 60, our mission, vision, and philosophy.',
                'بعد الستين، تبدأ الحياة من جديد. تعرف على رسالتنا ورؤيتنا وفلسفتنا في شباب فوق الستين.',
                1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
            )
        ");
        $insertStmt->execute([
            ':id' => $pageId,
            ':content_en' => $contentEn,
            ':content_ar' => $contentAr
        ]);
        echo "✓ About Us page created successfully.\n";
    }

    // Clear page cache so changes take effect immediately
    $cacheDir = PATH_ROOT . '/cache';
    $cacheFiles = glob($cacheDir . '/page_*.json');
    if ($cacheFiles) {
        foreach ($cacheFiles as $f) { @unlink($f); }
        echo "✓ Page cache cleared (" . count($cacheFiles) . " files).\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
