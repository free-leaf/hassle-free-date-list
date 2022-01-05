<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 *
 * @package    Hassle_Free_Date_List
 * @subpackage Hassle_Free_Date_List/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Hassle_Free_Date_List
 * @subpackage Hassle_Free_Date_List/includes
 * @author     Makoto Nakao
 */
class Hassle_Free_Date_List {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Hassle_Free_Date_List_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $hassle_free_date_list    The string used to uniquely identify this plugin.
	 */
	protected $hassle_free_date_list;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'HASSLE_FREE_DATE_LIST_VERSION' ) ) {
			$this->version = HASSLE_FREE_DATE_LIST_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->hassle_free_date_list = 'hassle-free-date-list';

		$this->load_dependencies();
		$this->set_locale();
		$this->register_custom_post_type();
		$this->define_metabox_hooks();
		$this->define_short_cords();
		$this->register_block();
		$this->define_contactform7_form_tag();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once HASSLE_FREE_DATE_LIST_PATH . '/includes/class-hassle-free-date-list-loader.php';
		require_once HASSLE_FREE_DATE_LIST_PATH . '/includes/class-hassle-free-date-list-i18n.php';
		require_once HASSLE_FREE_DATE_LIST_PATH . '/includes/class-hassle-free-date-list-shortcode.php';
		require_once HASSLE_FREE_DATE_LIST_PATH . '/includes/class-hassle-free-date-list-block.php';
		require_once HASSLE_FREE_DATE_LIST_PATH . '/includes/class-hassle-free-date-list-post-type.php';
		require_once HASSLE_FREE_DATE_LIST_PATH . '/includes/class-hassle-free-date-list-meta-box.php';
		require_once HASSLE_FREE_DATE_LIST_PATH . '/includes/class-hassle-free-date-list-cf7-form-tag.php';

		$this->loader = new Hassle_Free_Date_List_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Hassle_Free_Date_List_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_metabox_hooks() {

		$plugin_admin = new Hassle_Free_Date_List_Meta_Box( $this->get_hassle_free_date_list(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles_and_scripts' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_options' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_meta_box' );
		$this->loader->add_action( 'edit_form_after_title', $plugin_admin, 'after_title_content' );
	}

	/**
	 * Register short cords
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_short_cords() {

		$plugin_short_cords = new Hassle_Free_Date_List_Shortcode( $this->get_hassle_free_date_list() );
	}

	/**
	 * Register form tag for contact form 7
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_contactform7_form_tag() {

			$plugin_public = new Hassle_Free_Date_List_Cf7_Form_Tag( $this->get_hassle_free_date_list() );

			$this->loader->add_action( 'wpcf7_init', $plugin_public, 'wpcf7_add_form_tag_datelist' );
			$this->loader->add_action( 'wpcf7_admin_init', $plugin_public, 'wpcf7_add_tag_generator_datelist' );
			$this->loader->add_filter( 'wpcf7_validate_schedule', $plugin_public, 'wpcf7_datelist_validation_filter', 10, 2 );
			$this->loader->add_filter( 'wpcf7_validate_schedule*', $plugin_public, 'wpcf7_datelist_validation_filter', 10, 2 );

	}

	/**
	 * Register custom post type
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function register_custom_post_type() {

		$plugin_public = new Hassle_Free_Date_List_Post_Type( $this->get_hassle_free_date_list() );

		$this->loader->add_action( 'init', $plugin_public, 'init_post_type' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_public, 'updated_messages', 10, 2 );

	}

	/**
	 * Register block
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function register_block() {

		$plugin_public = new Hassle_Free_Date_List_Block( $this->get_hassle_free_date_list() );

		$this->loader->add_action( 'init', $plugin_public, 'hfdl_block_callback' );
		$this->loader->add_action( 'enqueue_block_editor_assets', $plugin_public, 'editor_enqueue_script' );
	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_hassle_free_date_list() {
		return $this->hassle_free_date_list;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Hassle_Free_Date_List_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
