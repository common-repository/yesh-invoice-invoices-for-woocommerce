<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://yeshinvoice.co.il/contact
 * @since      1.0.2
 *
 * @package    Wc_Invoices_Yeshinvoice
 * @subpackage Wc_Invoices_Yeshinvoice/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.2
 * @package    Wc_Invoices_Yeshinvoice
 * @subpackage Wc_Invoices_Yeshinvoice/includes
 * @author     Yesh Invoice <support@yeshinvoice.co.il>
 */
class Wc_Invoices_Yeshinvoice_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.2
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wc-invoices-yeshinvoice',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
