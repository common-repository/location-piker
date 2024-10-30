<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              plugin/uzzal.me
 * @since             1.0.0
 * @package           Location_Piker
 *
 * @wordpress-plugin
 * Plugin Name:       Google Map With Fancybox
 * Description:       Location Piker plugin is awesome google map maker , easy to use for google map that’s able to display google map with fancybox popup.
 * Version:           2.1.0
 * Author:            uzzal mondal
 * Author URI:        https://profiles.wordpress.org/mondal
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       location-piker
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-location-piker-activator.php
 */
function activate_location_piker() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-location-piker-activator.php';
	Location_Piker_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-location-piker-deactivator.php
 */
function deactivate_location_piker() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-location-piker-deactivator.php';
	Location_Piker_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_location_piker' );
register_deactivation_hook( __FILE__, 'deactivate_location_piker' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-location-piker.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_location_piker() {

	$plugin = new Location_Piker();
	$plugin->run();

}
run_location_piker();
