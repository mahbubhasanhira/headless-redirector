<?php
/**
 * Plugin Name:       Headless Redirector
 * Plugin URI:        https://github.com/mahbubhasanhira/headless-redirector
 * Description:       The essential gateway for headless WordPress. Redirects frontend traffic to your external site while white-listing Admin, Login, and API paths.
 * Version:           1.0.0
 * Requires PHP:      7.4
 * Author:            Mahbub Hasan Hira
 * Author URI:        https://mahbubhasanhira.com/
 * License:           GPL-2.0+
 * Text Domain:       headless-redirector
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 */
function activate_headless_redirector() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hr-activator.php';
	Headless_Redirector_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_headless_redirector() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hr-deactivator.php';
	Headless_Redirector_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_headless_redirector' );
register_deactivation_hook( __FILE__, 'deactivate_headless_redirector' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-hr-loader.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-hr-redirect.php';
require plugin_dir_path( __FILE__ ) . 'admin/class-hr-admin.php';

/**
 * Begs execution of the plugin.
 */
function run_headless_redirector() {

    $plugin_name = 'headless-redirector';
    $version = '1.0.0';

	$loader = new Headless_Redirector_Loader();
    $admin = new Headless_Redirector_Admin( $plugin_name, $version );
    $redirect = new Headless_Redirector_Redirect( $plugin_name, $version );

    // Admin Hooks
    $loader->add_action( 'admin_menu', $admin, 'add_plugin_admin_menu' );
    $loader->add_action( 'admin_init', $admin, 'register_settings' );
    $loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
    $loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_scripts' );

    // Public Hooks
    $loader->add_action( 'template_redirect', $redirect, 'execute_redirect' );

    $loader->run();
}

run_headless_redirector();