# Rebuilt Travel Without Limits (Pure PHP & MySQL)

This project is a high-fidelity clone of the "Travel Without Limits" luxury magazine-style travel platform, completely rewritten from scratch in **pure PHP and MySQL** without any frameworks, composer dependencies, or build systems. It is production-ready and optimized for standard shared cPanel hosting.

---

## Features
- **MVC Architecture**: Custom-built Front-Controller routing (`index.php` and `.htaccess`) with OOP-designed service managers (`classes/`).
- **Vanilla CSS Design System**: Responsive grid structures, custom HSL branding colors, dynamic visual typography, dark overlays, polaroid card collages, and category loaders.
- **Relational Integrity**: Complete MySQL schema mapping proper foreign keys, indices, and soft deletion support.
- **Performance Cache**: File-based posts & settings cache layers to optimize Lighthouse speeds.
- **WAI-ARIA Accessibility**: WCAG-compliant attributes, focus controls, and a fully interactive client accessibility overlay widget toggling contrast, link underlines, readable font modes, and font size scaling.
- **Administrative Panel**: Manage stories, category lists, subscribers list, contact submissions, and site-wide visual presets.
- **Secure Controls**: Encrypted blowfish passwords (`PASSWORD_BCRYPT`), AJAX uploader inputs, and CSRF token protection on form entries.
- **Dynamic SEO Utilities**: Dynamic metadata injection, dynamically generated `sitemap.xml`, and a structured `robots.txt`.

---

## Setup & Local Installation

### 1. Database Import
1. Create a MySQL database (e.g. `travel-final` in phpMyAdmin or Laragon database manager).
2. Import the `schema.sql` file located in the root of the project:
   ```bash
   mysql -u root -p travel-final < schema.sql
   ```
   *Note: This imports all table schemas, primary/foreign constraints, lookup indices, and pre-seeded high-fidelity content.*

### 2. Configuration Coordinates
Open the file `includes/config.php` and verify/adjust your database coordinates:
```php
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'travel-final');
define('DB_USER', 'root');
define('DB_PASS', '');
```
*Note: The project calculates the `BASE_URL` dynamically, allowing it to run smoothly out of any local folder (e.g. `http://localhost/clone-project/`) or root domains without path adjustments.*

### 3. Folder Permissions
Ensure the following directories are writable on your server (they are created automatically if missing):
- `/uploads/` (for media image files)
- `/cache/` (for speed performance JSON logs)

---

## Administration Credentials

To access the backend admin panel:
1. Navigate to: `http://<your-domain>/admin/login` (or `http://localhost/clone-project/admin/login` locally).
2. Enter the pre-seeded administrator coordinates:
   - **Email Address**: `admin@youngover60.com`
   - **Password**: `Password123`

---

## Deployment (cPanel & Production)

Because the project is written in pure PHP and contains zero composer or node package dependencies:
1. Upload the entire contents of the `/clone-project/` folder directly to your host's `public_html` directory (or directory of your choice).
2. No building, packaging, or command line executions are required. It runs instantly out-of-the-box.
