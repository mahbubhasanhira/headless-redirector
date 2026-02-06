<?php

/**
 * Fired during plugin activation
 *
 * @link       https://mahbubhasanhira.com/
 * @since      1.0.0
 *
 * @package    Headless_Redirector
 * @subpackage Headless_Redirector/includes
 */

class Headless_Redirector_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        // Add default options if they don't exist
        if ( false === get_option( 'hr_excluded_paths' ) ) {
            update_option( 'hr_excluded_paths', "graphql\nwp-content\nwp-login\nwp-json" );
        }
        
        // Set default critical paths (always accessible, even in Full Site Redirect mode)
        if ( false === get_option( 'hr_critical_paths' ) ) {
            update_option( 'hr_critical_paths', "wp-admin/*\nwp-login.php\nwp-json/*\nwp-content/*\nwp-includes/*\nwp-cron.php" );
        }
	}

}
