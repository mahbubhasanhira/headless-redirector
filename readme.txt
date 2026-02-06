=== Headless Redirector ===
Contributors: mahbubhasanhira
Tags: headless, redirect, jamstack, nextjs, gatsby
Requires at least: 6.3
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Plugin URI: https://github.com/mahbubhasanhira/headless-redirector

The essential gateway for headless WordPress. Redirects frontend traffic to your external site while white-listing Admin, Login, and API paths.

== Description ==

Headless Redirector is designed for WordPress sites that serve as a headless CMS. It intercepts frontend requests and redirects them to your specified headless frontend URL (e.g., a Next.js or Gatsby site), ensuring that visitors are seamlessly sent to the correct location.

Key features:
* **Global Redirect**: Send all traffic to a designated frontend URL.
* **Selective Exclusion**: Whitelist specific paths (e.g., `graphql`, `wp-json`) to ensure API access remains uninterrupted.
* **Admin Protection**: Automatically protects `wp-admin` and `wp-login.php` from redirection.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/headless-redirector` directory, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the 'Headless Redirector' menu to configure your settings.

== Frequently Asked Questions ==

= Does this redirect the admin panel? =
No, the plugin automatically protects `wp-admin` and `wp-login.php`.

= Can I exclude specific paths? =
Yes, you can add any path keywords to the exclusion list in the settings.

== Changelog ==

= 1.0.0 =
* Initial release.