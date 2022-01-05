<?php
/**
 * Add custom post type
 *
 * @since      1.0.0
 *
 * @package    Hassle_Free_Date_List
 * @subpackage Hassle_Free_Date_List/includes
 */

/**
 * Plugin shortcode
 *
 * @since      1.0.0
 * @package    Hassle_Free_Date_List
 * @subpackage Hassle_Free_Date_List/includes
 * @author     Makoto Nakao
 */
class Hassle_Free_Date_List_Post_Type {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_id    The ID of this plugin.
	 */
	private $plugin_id;

	/**
	 * Register　custom post type
	 *
	 * @return void
	 */
	public function init_post_type() {
		register_post_type(
			'hfdl_schedule',
			array(
				'labels'                => array(
					'name'               => __( 'Dates', 'hassle-free-date-list' ),
					'singular_name'      => __( 'Dates', 'hassle-free-date-list' ),
					'all_items'          => __( 'Dates', 'hassle-free-date-list' ),
					'new_item'           => __( 'New Dates', 'hassle-free-date-list' ),
					'add_new'            => __( 'Add New', 'hassle-free-date-list' ),
					'add_new_item'       => __( 'Add New', 'hassle-free-date-list' ),
					'edit_item'          => __( 'Edit Dates', 'hassle-free-date-list' ),
					'view_item'          => __( 'View Dates', 'hassle-free-date-list' ),
					'search_items'       => __( 'Search Dates', 'hassle-free-date-list' ),
					'not_found'          => __( 'Dates not found', 'hassle-free-date-list' ),
					'not_found_in_trash' => __( 'Dates not found in trash', 'hassle-free-date-list' ),
					'menu_name'          => __( 'Dates', 'hassle-free-date-list' ),
				),
				'public'                => false,
				'hierarchical'          => false,
				'show_ui'               => true,
				'supports'              => array( 'title' ),
				'has_archive'           => false,
				'rewrite'               => true,
				'menu_position'         => 40,
				'menu_icon'             => 'dashicons-calendar-alt',
				'show_in_rest'          => true,
				'rest_base'             => 'hfdl_schedule',
				'rest_controller_class' => 'WP_REST_Posts_Controller',
			)
		);

	}

	/**
	 * Messages of upadted posts
	 *
	 * @param string $messages アップデートメッセージ.
	 * @return string $messages アップデートメッセージ
	 */
	public function updated_messages( $messages ) {
		global $post;

		$messages['hfdl_schedule'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Updated Dates', 'hassle-free-date-list' ),
			2  => __( 'Updated Dates custom fields', 'hassle-free-date-list' ),
			3  => __( 'Deleted Dates custom fiilds', 'hassle-free-date-list' ),
			4  => __( 'Update Dates', 'hassle-free-date-list' ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Recoved from revision %s.' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Published', 'hassle-free-date-list' ),
			7  => __( 'Saved', 'hassle-free-date-list' ),
			8  => __( 'Published ', 'hassle-free-date-list' ),
			9  => sprintf( __( 'Publish date: <strong>%1$s</strong>. ' ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) ),
			10 => __( 'Update draft', 'hassle-free-date-list' ),
		);

		return $messages;
	}
}
