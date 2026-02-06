<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://mahbubhasanhira.com/
 * @since      1.0.0
 *
 * @package    Headless_Redirector
 * @subpackage Headless_Redirector/admin
 */

class Headless_Redirector_Admin {

	private $plugin_name;
	private $version;
    private $option_group = 'hr_settings_group';

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function enqueue_styles() {
		// wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/headless-redirector-admin.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/headless-redirector-admin.js', array( 'jquery' ), $this->version, false );
	}

    public function add_plugin_admin_menu() {
        add_menu_page(
            __( 'Headless Redirector', 'headless-redirector' ),
            __( 'Headless Redirector', 'headless-redirector' ),
            'manage_options',
            'headless-redirector',
            array( $this, 'display_plugin_settings_page' ),
            'dashicons-randomize',
            99
        );
    }

    public function register_settings() {
        // General Group
        register_setting( 'hr_general_settings', 'hr_enabled' );
        register_setting( 'hr_general_settings', 'hr_redirect_strategy' ); // New setting
        register_setting( 'hr_general_settings', 'hr_target_url', 'esc_url_raw' );
        register_setting( 'hr_general_settings', 'hr_excluded_paths', 'sanitize_textarea_field' );
        
        // Advanced Group
        register_setting( 'hr_advanced_settings', 'hr_full_redirect_mode' );
        register_setting( 'hr_advanced_settings', 'hr_url_mappings', array( $this, 'sanitize_mappings' ) );
    }

    public function sanitize_mappings( $input ) {
        // Basic sanitization for mappings array
        // Expected format: array of [post_id => url]
        return $input; 
    }

    public function display_plugin_settings_page() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/hr-admin-display.php';
    }

}
