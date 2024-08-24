<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.ibsofts.com
 * @since             2.0.4
 * @package           GHLCONNECT
 *
 * @wordpress-plugin
 * Plugin Name:       GHL Connect for WooCommerce
 * Plugin URI:        https://www.ibsofts.com/plugins/ghl-connect
 * Description:       This plugin will connect the popular CRM Go High Level(GHL) to the most popular content management software WordPress.
 * Version:           2.0.4
 * Author:            iB Softs
 * Author URI:        https://www.ibsofts.com/
 * License:           GPL-2.0.4+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.4.txt
 * Text Domain:       ghl-connect
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 2.0.4 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'GHLCONNECT_VERSION', '2.0.4' );
define( 'GHLCONNECT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'GHLCONNECT_LOCATION_CONNECTED', false );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ghl-connect-activator.php
 */
if ( ! function_exists( 'ghlconnect_activate' ) ) {
	function ghlconnect_activate() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-ghl-connect-activator.php';
		GHLCONNECT_Activator::activate();
	}
	register_activation_hook( __FILE__, 'ghlconnect_activate' );
}
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ghl-connect-deactivator.php
 */
if ( ! function_exists( 'ghlconnect_deactivate' ) ) {
	function ghlconnect_deactivate() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-ghl-connect-deactivator.php';
		GHLCONNECT_Deactivator::deactivate();
	}
	register_deactivation_hook( __FILE__, 'ghlconnect_deactivate' );
}







/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ghl-connect.php';
/**
 * Inclusion of definitions.php
 */
require_once plugin_dir_path( __FILE__ ) . 'definitions.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.4
 */
if ( ! function_exists( 'ghlconnect_run' ) ) {
	function ghlconnect_run() {

		$plugin = new GHLCONNECT();
		$plugin->run();

	}
	ghlconnect_run();
}