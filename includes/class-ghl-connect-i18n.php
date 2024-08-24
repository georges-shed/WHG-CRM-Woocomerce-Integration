<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://https://www.ibsofts.com
 * @since      2.0.4
 *
 * @package    GHLCONNECT
 * @subpackage GHLCONNECT/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      2.0.4
 * @package    GHLCONNECT
 * @subpackage GHLCONNECT/includes
 * @author     iB Softs <ibsofts@gmail.com>
 */
class GHLCONNECT_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    2.0.4
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ghl-connect',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
