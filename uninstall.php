<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @since      1.0.0
 *
 * @package    Hassle_Free_Date_List
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Delete plugins's post type and metadata.
 *
 * @return void
 */
function hfdl_delete_plugin() {
	global $wpdb;

	delete_option( 'wpcf7' );

	$posts = get_posts(
		array(
			'numberposts' => -1,
			'post_type'   => 'hfdl_schedule',
			'post_status' => 'any',
		)
	);

	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}

}

if ( ! defined( 'HASSLE_FREE_DATE_LIST_VERSION' ) ) {
	hfdl_delete_plugin();
}
