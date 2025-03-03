<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://yeshinvoice.co.il/contact
 * @since             1.0.2
 * @package           Wc_Invoices_Yeshinvoice
 *
 * @wordpress-plugin
 * Plugin Name:       Yesh Invoice Invoices for WooCommerce
 * Plugin URI:        https://yeshinvoice.co.il
 * Description:       The best system for generating invoices and receipts for WooCommerce.
 * Version:           1.0.4
 * Author:            Yesh Invoice
 * Author URI:        https://yeshinvoice.co.il/contact
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-invoices-yeshinvoice
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.2 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WC_INVOICES_YESHINVOICE_VERSION', '1.0.3' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wc-invoices-yeshinvoice.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.2
 */
function run_wc_invoices_yeshinvoice() {

	$plugin = new Wc_Invoices_Yeshinvoice();
	$plugin->run();

}
run_wc_invoices_yeshinvoice();
