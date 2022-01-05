<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Hassle-Free Date List
 * Plugin URI:        https://github.com/free-leaf/hassle-free-date-list
 * Description:       This plugin adds a block, a shortcode, and a contactform 7 form tag that displays a list of dates. Dates that are due will automatically be hidden or labeled. This will prevent you from forgetting to delete the dates of your courses, workshop etc. Dates can be managed centrally from the admin panel.
 * Requires at least: 5.6
 * Requires PHP:      7.4
 * Version:           1.0.0
 * Author:            Makoto Nakao
 * Author URI:        https://free-leaf.org/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       hassle-free-date-list
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'HASSLE_FREE_DATE_LIST_VERSION', '1.0.0' );
define( 'HASSLE_FREE_DATE_LIST_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'HASSLE_FREE_DATE_LIST_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * The core plugin class that is used to define internationalization,
 */
require HASSLE_FREE_DATE_LIST_PATH . '/includes/class-hassle-free-date-list.php';

/**
 * Begins execution of the plugin.
 *
 * @since 1.0.0
 */
function run_hassle_free_date_list() {
	$plugin = new Hassle_Free_Date_List();
	$plugin->run();

}
run_hassle_free_date_list();
