<?php
/**
 * The file that defines the plugin blocks
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
class Hassle_Free_Date_List_Block {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_id    The ID of this plugin.
	 */
	private $plugin_id;

	/**
	 * Register block
	 *
	 * @param string $hassle_free_date_list The ID of this plugin
	 */
	public function __construct( $hassle_free_date_list ) {
		$this->plugin_id = $hassle_free_date_list;
	}

	/**
	 * Register block
	 *
	 * @return void
	 */
	public function hfdl_block_callback() {
		register_block_type(
			HASSLE_FREE_DATE_LIST_PATH . '/src/',
			array(
				'attributes'      => array(
					'sID'           => array( 'type' => 'string' ),
					'layout'        => array( 'type' => 'string' ),
					'timeLayout'    => array( 'type' => 'string' ),
					'align'         => array( 'type' => 'string' ),
					'largeNumber'   => array( 'type' => 'boolean' ),
					'boldDate'      => array( 'type' => 'boolean' ),
					'largeTime'     => array( 'type' => 'boolean' ),
					'dateColor'     => array( 'type' => 'string' ),
					'dateBgColor'   => array( 'type' => 'string' ),
					'timeColor'     => array( 'type' => 'string' ),
					'timeBgColor'   => array( 'type' => 'string' ),
					'closedColor'   => array( 'type' => 'string' ),
					'closedBgColor' => array( 'type' => 'string' ),
					'fullColor'     => array( 'type' => 'string' ),
					'fullBgColor'   => array( 'type' => 'string' ),
				),
				'render_callback' => function( $attr, $content = '' ) {
					if ( isset( $attr['largeNumber'] ) && 1 == $attr['largeNumber'] ) {
						$attr['largeNumber'] = 'on';
					} else {
						$attr['largeNumber'] = 'off';
					}
					if ( isset( $attr['boldDate'] ) && 1 == $attr['boldDate'] ) {
						$attr['boldDate'] = 'on';
					} else {
						$attr['boldDate'] = 'off';
					}
					if ( isset( $attr['largeTime'] ) && 1 == $attr['largeTime'] ) {
						$attr['largeTime'] = 'on';
					} else {
						$attr['largeTime'] = 'off';
					}

					$attr = wp_parse_args(
						$attr,
						array(
							'sID'           => '',
							'layout'        => 'row',
							'timeLayout'    => 'row',
							'align'         => 'left',
							'largeNumber'   => 'on',
							'boldDate'      => 'on',
							'largeTime'     => 'on',
							'dateColor'     => '',
							'dateBgColor'   => '',
							'timeColor'     => '',
							'timeBgColor'   => '',
							'closedColor'   => '',
							'closedBgColor' => '',
							'fullColor'     => '',
							'fullBgColor'   => '',
						)
					);

					$short_code = '[date_list ' . $this->parse_attr( $attr ) . ']';

					return do_shortcode( $short_code );
				},
			)
		);
	}

	/**
	 * 属性値をショートコード形式に変換
	 *
	 * @param array $attr 属性値の配列
	 * @return string
	 */
	public function parse_attr( $attr ) {
		$attr_text = '';
		foreach ( $attr as $key => $value ) {
			$attr_text .= ' ' . $key . '="' . $value . '"';
		}
		return $attr_text;
	}

	/**
	 * Enqueu schedule list as js variables for block editor
	 *
	 * @return void
	 */
	public function editor_enqueue_script() {
		wp_localize_script(
			'hassle-free-date-list-date-list-editor-script',
			'hfdlOpt',
			$this->schedule_list(),
		);
	}

	/**
	 * Return the list of hfdl_schedule post type
	 *
	 * @return array
	 */
	public static function schedule_list() {
		$args = array(
			'posts_per_page' => -1,
			'post_type'      => 'hfdl_schedule',
			'post_status'    => 'publish',
		);

		$hfdl_schedules = get_posts( $args );
		$schedul_data   = array();

		foreach ( $hfdl_schedules as $schedule ) {
			$schedul_data[] = array(
				'id'    => $schedule->ID,
				'title' => $schedule->post_title,
			);
		}
		return $schedul_data;
	}
}
