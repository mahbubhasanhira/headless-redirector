# Headless Redirector

> **The essential gateway for headless WordPress.**  
> Redirects frontend traffic to your external site while white-listing Admin, Login, and API paths.

**Headless Redirector** takes the headache out of managing traffic for decoupled WordPress sites. Whether you are using Next.js, Gatsby, Nuxt, or any other frontend framework, this plugin ensures your visitors and search engines always land on your headless application, not your backend WordPress install.

Unlike generic redirect plugins, Headless Redirector is architected specifically for headless setups. It understands that while you want to hide your frontend themes, you absolutely must keep your **REST API**, **GraphQL**, and **Admin Dashboard** accessible.

## 🚀 Key Features

- **Global Redirection**  
  Automatically sends all standard frontend traffic to your configured target URL (e.g., `https://my-headless-site.com`).

- **Headless Mode (Block Access)**  
  Option to return a **403 Forbidden** status instead of redirecting. Perfect for private backends where you don't want any public frontend access.

- **Smart Exclusions**  
  Whitelist specific paths that should stay on the WordPress domain.
  - **Wildcard Support:** Use `*` for pattern matching (e.g., `/landing-page/*` matches everything under that path).
  - **Exact Matching:** Precise control for specific files or pages.

- **Critical Path Protection**  
  Intelligent failsafes prevent you from ever blocking `wp-admin`, `wp-login.php`, `wp-json`, or `wp-cron.php`.

- **Individual URL Mapping**  
  Need a specific WordPress post to redirect to a different URL than the global default? Map it easily in the dashboard.

- **Full Site Redirect**  
  A powerful option to force redirects on _almost_ everything (respecting your critical paths) when you need stricter enforcement.

## 💡 Why Use This?

When running WordPress headlessly, the default frontend pages (like `/hello-world/`) still exist. This causes:

1.  **SEO Issues:** Duplicate content between your WP backend and Headless frontend.
2.  **User Confusion:** Visitors might accidentally land on the unstyled WordPress theme.
3.  **Security Obscurity:** Exposing your backend structure unnecessarily.

**Headless Redirector** solves these by intercepting requests at the server level (using `template_redirect`) and routing them intelligently based on your rules.

## 🔧 Installation & Configuration

1.  **Install:** Upload to `/wp-content/plugins/headless-redirector` or install via WordPress plugins screen.
2.  **Activate:** Enable the plugin.
3.  **Configure:** Go to `> Headless Redirector` (or the dedicated menu item).
    - **Target URL:** Enter your frontend application's URL.
    - **Strategy:** Choose "Redirect" or "Block".
    - **Exclusions:** Add any special paths to keep on the WP backend.

## 🤝 Contribution Roadmap

We would love your help to make **Headless Redirector** even better! follow this roadmap to contribute:

### 1. 🛠️ **Setup Your Environment**

- **Fork the Repository:** Click the "Fork" button on GitHub.
- **Clone Locally:** `git clone https://github.com/mahbubhasanhira/headless-redirector.git`
- **Install Dependencies:** Ensure you have a standard WordPress development environment (LocalWP, Docker, etc.).

### 2. 🌿 **Branching Strategy**

- Create a new branch for your specific task.
- **Naming Convention:**
  - Features: `feature/my-new-feature`
  - Fixes: `fix/bug-description`
  - Docs: `docs/update-readme`

### 3. 💻 **Development Standards**

- **Code Style:** Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/).
- **Sanitization:** Always sanitize inputs (`sanitize_text_field`, `esc_url`) and escape outputs.
- **Comments:** Document complex logic, especially in `class-hr-redirect.php`.

### 4. 🧪 **Testing**

- **Manual Testing:** Verify your changes in all 3 modes (Redirect, Block, Full Site Redirect).
- **Edge Cases:** Check exclusions and critical paths with and without wildcards.

### 5. 🚀 **Submit Pull Request**

- Push your branch: `git push origin feature/my-new-feature`.
- Open a Pull Request against the `main` branch.
- Provide a clear description of **what** changed and **why**.

## 📄 License

GPLv2 or later.
