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

        // Avoid redirecting Login (Critical Safety)
        if ( stripos( $_SERVER['REQUEST_URI'], 'wp-login.php' ) !== false ) {
            return;
        }

        // Avoid redirecting REST API (Critical Safety)
        if ( stripos( $_SERVER['REQUEST_URI'], 'wp-json' ) !== false || stripos( $_SERVER['REQUEST_URI'], 'graphql' ) !== false ) {
            return;
        }

        // Avoid redirecting Assets (Critical Safety)
        if ( stripos( $_SERVER['REQUEST_URI'], 'wp-content' ) !== false || stripos( $_SERVER['REQUEST_URI'], 'wp-includes' ) !== false ) {
            return;
        }

        // Avoid redirecting WP Cron (Critical Safety)
        if ( stripos( $_SERVER['REQUEST_URI'], 'wp-cron.php' ) !== false ) {
            return;
        }

        $target_url = get_option( 'hr_target_url' );
        $request_uri = $_SERVER['REQUEST_URI'];
        $mappings = get_option( 'hr_url_mappings', array() );
        $current_id = get_queried_object_id();
        
        // 1. Check Individual Mappings (Highest Priority) - Works even if Global Target is empty? 
        // User said: "if those path which have single redirect path it will redirect to single path."
        // We should allow individual redirects even if global target is missing? 
        // Historically we returned if target_url is empty. But now we have mappings.
        // Let's keep checking mappings first.
        if ( $current_id && ! empty( $mappings[$current_id] ) ) {
            wp_redirect( $mappings[$current_id], 301 );
            exit;
        }

        // If no global target is set, we can't do global/full redirect.
        if ( empty( $target_url ) ) {
            return;
        }

        // 2. Check Exclusions (Unless Full Redirect Mode is ON)
        $full_redirect = get_option( 'hr_full_redirect_mode' ) === '1';

        if ( ! $full_redirect ) {
            $exclusions = array_filter( array_map( 'trim', explode( "\n", get_option( 'hr_excluded_paths' ) ) ) );
            $exclusions[] = 'wp-admin'; 
            $exclusions[] = 'wp-login';
            
            foreach ( $exclusions as $path ) {
                if ( ! empty( $path ) && stripos( $request_uri, $path ) !== false ) {
                    return;
                }
            }
        } else {
            // In Full Redirect Mode, we still MUST exclude wp-admin/login to prevent lockout
            if ( stripos( $request_uri, 'wp-admin' ) !== false || stripos( $request_uri, 'wp-login' ) !== false ) {
                return;
            }
        }

        // 3. Fallback: Global Action
        // If "Full Redirect" is ON, we are here (exclusions skipped).
        // If "Full Redirect" is OFF, we are here (exclusions passed).
        
        $strategy = get_option( 'hr_redirect_strategy', 'redirect' );

        if ( $strategy === 'block' ) {
            // Headless Mode: Block Access
            wp_die( 
                '<h1>Access Denied</h1><p>This site is in headless mode. Please visit the <a href="' . esc_url( $target_url ) . '">frontend application</a>.</p>', 
                'Headless Mode', 
                array( 'response' => 403 ) 
            );
            exit;
        } else {
            // Standard Mode: Redirect
            wp_redirect( $target_url, 301 );
            exit;
        }
	}

}
