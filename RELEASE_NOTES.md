# Release Notes - v1.0.0

This release marks a significant milestone for the Secure Flat-File CMS, introducing enterprise-grade features and professional UI options.

## 🌟 New Features

### Security & Stability
*   **Two-Factor Authentication (2FA):** Native TOTP implementation (RFC 6238). QR codes are generated locally in the browser for maximum security (zero-knowledge).
*   **File Locking (flock):** All file write operations now use exclusive locks to prevent race conditions and data corruption.
*   **Encrypted Indexing:** Taxonomies (`tags`, `category`) are now indexed for faster lookups.

### Performance
*   **Smart Caching:** Public pages are cached as static HTML, bypassing decryption and Markdown parsing on subsequent visits.
*   **Optimized Router:** New `public/router.php` for seamless local development.

### Developer Experience
*   **Headless API:** New endpoint `GET /api/content/{slug}` returns clean JSON for decoupling the backend.
*   **Plugin Documentation:** The Extensions manager now renders `README.md` files from plugins in a modal.

### Design & Content
*   **Professional Themes:** Added 5 new high-quality themes:
    *   **Nexus:** Corporate / SaaS (Inter font).
    *   **Zenith:** Editorial / Minimalist (Merriweather font).
    *   **Vanguard:** Magazine / News.
    *   **Lumina:** Dark Mode / Portfolio.
    *   **Essence:** Lifestyle / eCommerce.
*   **Shortcode UI Plugin:** A comprehensive set of components for Markdown:
    *   Grids, Alerts, Buttons, Cards, Badges.
    *   YouTube embeds, Hero banners, Accordions.
    *   Pricing Tables, Stats, User Testimonials.

## 🔧 Upgrading
No database migration is required. Just pull the changes and run `composer install` if dependencies changed (none added in this release).

**Author:** Eduardo Nacimiento-García <enacimie@ull.edu.es>
