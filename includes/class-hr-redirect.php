<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://mahbubhasanhira.com/
 * @since      1.0.0
 *
 * @package    Headless_Redirector
 * @subpackage Headless_Redirector/includes
 */

class Headless_Redirector_Redirect {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Execute the redirect logic.
	 */
	public function execute_redirect() {
        if ( get_option( 'hr_enabled' ) !== '1' || is_admin() || is_network_admin() ) {
            return;
        }

        // Validate Request
        if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
            return;
        }

        $request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );

        // Avoid redirecting Login (Critical Safety)
        if ( stripos( $request_uri, 'wp-login.php' ) !== false ) {
            return;
        }

        // Avoid redirecting REST API (Critical Safety)
        if ( stripos( $request_uri, 'wp-json' ) !== false || stripos( $request_uri, 'graphql' ) !== false ) {
            return;
        }

        // Avoid redirecting Assets (Critical Safety)
        if ( stripos( $request_uri, 'wp-content' ) !== false || stripos( $request_uri, 'wp-includes' ) !== false ) {
            return;
        }

        // Avoid redirecting WP Cron (Critical Safety)
        if ( stripos( $request_uri, 'wp-cron.php' ) !== false ) {
            return;
        }

        $strategy = get_option( 'hr_redirect_strategy', 'redirect' );
        $target_url = get_option( 'hr_target_url' );
        $mappings = get_option( 'hr_url_mappings', array() );
        $current_id = get_queried_object_id();
        $full_redirect = get_option( 'hr_full_redirect_mode' ) === '1';
        
        // ============================================================
        // SIMPLIFIED LOGIC:
        // 1. Check critical paths FIRST (always accessible, even in Full Redirect)
        // 2. Check exclusions (only in normal mode, not Full Redirect)
        // 3. Block Access mode = blocks everything (except critical paths & exclusions)
        // 4. Redirect mode = individual mappings first, then global (respects exclusions)
        // 5. Full Site Redirect = forces redirect, ignores exclusions (but respects critical paths)
        // ============================================================
        
        // STEP 1: Check Critical Paths FIRST (always accessible in ALL modes)
        $critical_paths = array_filter( array_map( 'trim', explode( "\n", get_option( 'hr_critical_paths', '' ) ) ) );
        
        foreach ( $critical_paths as $path ) {
            if ( ! empty( $path ) ) {
                // Check if wildcard is present
                if ( strpos( $path, '*' ) !== false ) {
                    // Wildcard matching: /blog/* matches /blog/anything
                    $path_prefix = rtrim( str_replace( '*', '', $path ), '/' );
                    $path_normalized = '/' . ltrim( $path_prefix, '/' );
                    if ( strpos( $request_uri, $path_normalized ) === 0 ) {
                        return;
                    }
                } else {
                    // Exact matching: /about matches ONLY /about (with or without trailing slash)
                    $path_normalized = '/' . ltrim( $path, '/' );
                    // Strip query string from request URI for comparison
                    $request_path = strtok( $request_uri, '?' );
                    // Compare with and without trailing slash
                    if ( rtrim( $request_path, '/' ) === rtrim( $path_normalized, '/' ) || 
                         rtrim( $request_path, '/' ) === rtrim( $path, '/' ) ) {
                        return;
                    }
                }
            }
        }
        
        // STEP 2: Check Exclusions (only if NOT in Full Redirect Mode)
        if ( ! $full_redirect ) {
            // Exclusions work in Block and Redirect modes
            $exclusions = array_filter( array_map( 'trim', explode( "\n", get_option( 'hr_excluded_paths', '' ) ) ) );
            
            foreach ( $exclusions as $path ) {
                if ( ! empty( $path ) ) {
                    // Check if wildcard is present
                    if ( strpos( $path, '*' ) !== false ) {
                        // Wildcard matching: /blog/* matches /blog/anything
                        $path_prefix = rtrim( str_replace( '*', '', $path ), '/' );
                        $path_normalized = '/' . ltrim( $path_prefix, '/' );
                        if ( strpos( $request_uri, $path_normalized ) === 0 ) {
                            return;
                        }
                    } else {
                        // Exact matching: /about matches ONLY /about (with or without trailing slash)
                        $path_normalized = '/' . ltrim( $path, '/' );
                        // Strip query string from request URI for comparison
                        $request_path = strtok( $request_uri, '?' );
                        // Compare with and without trailing slash
                        if ( rtrim( $request_path, '/' ) === rtrim( $path_normalized, '/' ) || 
                             rtrim( $request_path, '/' ) === rtrim( $path, '/' ) ) {
                            return;
                        }
                    }
                }
            }
        }
        
        // STEP 3: Block Access Mode
        if ( $strategy === 'block' ) {
            // Block access to all frontend pages (after checking exclusions)
            $message = '<h1>Access Denied</h1><p>This site is in headless mode.';
            if ( ! empty( $target_url ) ) {
                $message .= ' Please visit the <a href="' . esc_url( $target_url ) . '">frontend application</a>.';
            }
            $message .= '</p>';
            
            wp_die( 
                wp_kses_post( $message ), 
                'Headless Mode', 
                array( 'response' => 403 ) 
            );
            exit;
        }
        
        // STEP 4: Check Individual URL Mappings (Highest Priority in Redirect Mode)
        // Individual redirects work first, even before global redirect
        if ( $current_id && ! empty( $mappings[$current_id] ) ) {
            // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- External redirect is the intended feature.
            wp_redirect( $mappings[$current_id], 301 );
            exit;
        }
        
        // STEP 5: Check if we can do global redirect
        // If no global target URL is set, we can't redirect
        if ( empty( $target_url ) ) {
            return;
        }
        
        // STEP 6: Execute Global Redirect
        // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- External redirect is the intended feature.
        wp_redirect( $target_url, 301 );
        exit;
	}

}
