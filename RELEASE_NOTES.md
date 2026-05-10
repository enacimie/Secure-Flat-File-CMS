# Release Notes - v1.3.0 (Excellence Pack)

This release finalizes the Core features, focusing on Security, SEO, and Developer Experience.

## 🌟 New Features

### 🛡️ Security & Enterprise
*   **Dynamic Form Builder:** Create complex forms (Contact, Surveys) directly in Markdown using Shortcodes.
*   **Anti-Spam (Honeypot):** Invisible protection against bots in all forms.
*   **Installer Self-Destruct:** The installation script automatically deletes itself after success.
*   **CLI Tool (`bin/cms`):** Command-line interface for emergency tasks (reset password, clear cache).

### 🚀 SEO & Performance
*   **Dynamic Sitemap & Robots:** Auto-generated `sitemap.xml` (with images) and `robots.txt`.
*   **HTML Minification:** Output is automatically minified to reduce bandwidth usage.

### 🧪 Quality Assurance
*   **PHPUnit Suite:** Full unit testing infrastructure included.
*   **CI/CD:** GitHub Actions workflow configured for automated testing.

## 📦 Upgrading
Run `composer update` to install development dependencies (PHPUnit).

**Author:** Eduardo Nacimiento-García <enacimie@ull.edu.es>