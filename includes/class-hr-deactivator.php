<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://mahbubhasanhira.com/
 * @since      1.0.0
 *
 * @package    Headless_Redirector
 * @subpackage Headless_Redirector/includes
 */

class Headless_Redirector_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        // flush rewrite rules if necessary, or clean up temporary data.
        // We typically do NOT delete options on deactivation, only on uninstall.
	}

}
