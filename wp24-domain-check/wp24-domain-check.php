<?php
/**
 * Plugin Name: WP24 Domain Check
 * Plugin URI: https://wp24.org/plugins/domain-check
 * Description: Check (whois) domain names for availability. Easy integration via shortcode or widget.
 * Version: 1.11.3
 * Author: WP24
 * Author URI: https://wp24.org
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp24-domain-check
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! defined( 'WP24_DOMAIN_CHECK_VERSION' ) )
	define( 'WP24_DOMAIN_CHECK_VERSION', '1.11.3' );

if ( ! defined( 'WP24_DOMAIN_CHECK_DATABASE_VERSION' ) )
	define( 'WP24_DOMAIN_CHECK_DATABASE_VERSION', '1.3.0' );

if ( ! class_exists( 'WP24_Domain_Check' ) )
	require_once( plugin_dir_path( __FILE__ ) . '/includes/class-wp24-domaincheck.php' );
if ( ! class_exists( 'WP24_Domain_Check_Options' ) )
	require_once( plugin_dir_path( __FILE__ ) . '/includes/class-wp24-options.php' );
if ( ! class_exists( 'WP24_Domain_Check_Widget' ) )
	require_once( plugin_dir_path( __FILE__ ) . '/includes/class-wp24-widget.php' );

if ( is_admin() ) {
	if ( ! class_exists( 'WP24_Domain_Check_Setup' ) )
		require_once( plugin_dir_path( __FILE__ ) . '/includes/class-wp24-setup.php' );

	add_action( 'plugins_loaded', function() {
		WP24_Domain_Check_Setup::update_database();
	} );

	register_uninstall_hook( __FILE__, array( 'WP24_Domain_Check_Setup', 'uninstall' ) );
}

add_action( 'init',  function() {
	// create and init domain check
	$wp24_domain_check = new WP24_Domain_Check();
	$wp24_domain_check->init();

	if ( is_admin() ) {
		if ( ! class_exists( 'WP24_Domain_Check_Settings' ) )
			require_once( plugin_dir_path( __FILE__ ) . '/includes/class-wp24-settings.php' );

		// create and init settings
		$wp24_domain_check_settings = new WP24_Domain_Check_Settings();
		$wp24_domain_check_settings->init();
	}
} );
