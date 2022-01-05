<?php
/**
 * The Schedule post type meta box
 *
 * @since      1.0.0
 *
 * @package    Hassle_Free_Date_List
 * @subpackage Hassle_Free_Date_List/admin
 */

/**
 * The Schedule post type meta box.
 *
 * @package    Hassle_Free_Date_List
 * @subpackage Hassle_Free_Date_List/admin
 * @author     Makoto Nakao
 */
class Hassle_Free_Date_List_Meta_Box {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_id    The ID of this plugin.
	 */
	private $plugin_id;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $hassle_free_date_list       The name of this plugin.
	 */
	public function __construct( $hassle_free_date_list, $version ) {

		$this->plugin_id = $hassle_free_date_list;
		$this->version   = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles_and_scripts( $hook_suffix ) {
		global $post;
		switch ( $hook_suffix ) {
			case 'post.php':
			case 'post-new.php':
				if ( 'hfdl_schedule' === $post->post_type ) {
					wp_enqueue_style(
						$this->plugin_id,
						HASSLE_FREE_DATE_LIST_URL . '/asset/css/hfdl-admin.css',
						array(),
						$this->version,
						'all'
					);
					wp_enqueue_script(
						$this->plugin_id,
						HASSLE_FREE_DATE_LIST_URL . '/asset/js/hfdl-admin.js',
						array( 'jquery' ),
						$this->version,
						false
					);
				}
				break;
			default:
				break;
		}

	}

	/**
	 * Add meta box
	 *
	 * @return void
	 */
	public function add_meta_box() {
		$screens = array( 'hfdl_schedule' );

		foreach ( $screens as $screen ) {
			add_meta_box(
				'hfdl_schedule_options',
				__( 'Date List Setting', $this->plugin_id ),
				array( $this, 'meta_box_callback' ),
				$screen,
				'normal',
				'high'
			);
		}
	}

	/**
	 * メタボックスを表示する関数
	 *
	 * @return void
	 */
	public function meta_box_callback() {
		global $post;

		$post_type = $post->post_type;

		wp_nonce_field( 'hfdl_metabox_nonce', 'hfdl_metabox_nonce' );

		// カスタムフィールドの保存されているデータの取得＆初期化.
		// スケジュールデータ.
		$item_data = get_post_meta( $post->ID, '_item_data', true );

		// スケジュールの最大値（ループのカウントなどに使う）.
		$item_max = intval( get_post_meta( $post->ID, '_item_max', true ) );

		// 各順位のスケジュールデータに固有に数値を割り当てる.
		$item_count = intval( get_post_meta( $post->ID, '_item_count', true ) );

		// 比較する基準
		$base_date = get_post_meta( $post->ID, '_base_date', true );
		$base_date = empty( $base_date ) ? 'relative' : $base_date;

		$base_date_abs = get_post_meta( $post->ID, '_base_date_abs', true );

		$base_date_rel = get_post_meta( $post->ID, '_base_date_rel', true );
		$base_date_rel = '' == $base_date_rel ? 0 : $base_date_rel;

		// 基準日以前の日付を表示する場合
		$closed = get_post_meta( $post->ID, '_closed', true );
		$closed = ! empty( $closed ) ? $closed : 'hide';

		$closed_design = get_post_meta( $post->ID, '_closed_design', true );
		$closed_design = ! empty( $closed_design ) ? $closed_design : 'strikethrough';

		$closed_text = get_post_meta( $post->ID, '_closed_text', true );
		$closed_text = '' !== $closed_text ? $closed_text : __( 'Closed', 'hassle-free-date-list' );

		$closed_content       = get_post_meta( $post->ID, '_closed_content', true );
		$closed_dropdown_text = get_post_meta( $post->ID, '_closed_dropdown_text', true );

		// 予約された場合
		$full = get_post_meta( $post->ID, '_full', true );
		$full = ! empty( $full ) ? $full : 'hide';

		$full_design = get_post_meta( $post->ID, '_full_design', true );
		$full_design = ! empty( $full_design ) ? $full_design : 'strikethrough';

		$full_text = get_post_meta( $post->ID, '_full_text', true );
		$full_text = '' !== $full_text ? $full_text : __( 'Full', 'hassle-free-date-list' );

		// 日付のフォーマット
		$date_format = get_post_meta( $post->ID, '_date_format', true );

		// 曜日のフォーマット
		$custom_day_format = get_post_meta( $post->ID, '_custom_day_format', true );
		$custom_day_format = ! empty( $custom_day_format ) ? $custom_day_format : 'off';

		$default_days = array(
			__( 'Sunday', 'hassle-free-date-list' )    => __( '(sun)', 'hassle-free-date-list' ),
			__( 'Monday', 'hassle-free-date-list' )    => __( '(mon)', 'hassle-free-date-list' ),
			__( 'Tuesday', 'hassle-free-date-list' )   => __( '(tue)', 'hassle-free-date-list' ),
			__( 'Wednesday', 'hassle-free-date-list' ) => __( '(wed)', 'hassle-free-date-list' ),
			__( 'Thursday', 'hassle-free-date-list' )  => __( '(thu)', 'hassle-free-date-list' ),
			__( 'Friday', 'hassle-free-date-list' )    => __( '(fri)', 'hassle-free-date-list' ),
			__( 'Satday', 'hassle-free-date-list' )    => __( '(sat)', 'hassle-free-date-list' ),
		);

		$days = array();
		$i    = 0;
		foreach ( $default_days as $day => $value ) {
			$save_value   = get_post_meta( $post->ID, '_day-' . $i, true );
			$days[ $day ] = ! empty( $save_value ) ? $save_value : $value;
			$i++;
		}

		// Initialize max item number and item sirial number
		if ( ! isset( $item_max ) || 0 >= $item_max ) {
			$item_max = 1;
		}
		if ( ! isset( $item_count ) || 0 >= $item_count ) {
			$item_count = 1;
		}

		// Selected tab
		$tabs = get_post_meta( $post->ID, '_cp_tab', true );
		if ( empty( $tabs ) ) {
			$tabs = 1;
		}

		$tab_names = array(
			__( 'General', 'hassle-free-date-list' ),
			__( 'Register Dates', 'hassle-free-date-list' ),
		);

		?>
<div id="ui_tab" class="cp_tab">
		<?php $this->get_admin_tab_src( $tab_names, 'setting_tab', $tabs ); ?>
	<div class="cp_tabpanels">
		<div id="tab_setting" class="cp_tabpanel">
			<table class="form-table">
				<tbody>
					<tr>
						<th><?php esc_html_e( 'Deadline', 'hassle-free-date-list' ); ?></th>
						<td>
						<fieldset>
							<label><input type="radio" name="base_date" value="absolute" <?php checked( $base_date, 'absolute' ); ?>> <?php esc_html_e( 'Specify the date', 'hassle-free-date-list' ); ?>:</label>
							<input type="date" class="date" name="base_date_abs" value="<?php echo esc_attr( $base_date_abs ); ?>">
							<p class="hfdl-note"><?php esc_html_e( 'If blank, it is based on the day.', 'hassle-free-date-list' ); ?></p>
							<label><input type="radio" name="base_date" value="relative" <?php checked( $base_date, 'relative' ); ?>> <?php esc_html_e( 'Specify number of days', 'hassle-free-date-list' ); ?>:</label>
							<input type="number" class="small-text" name="base_date_rel" min="0" step="1" value="<?php echo esc_attr( $base_date_rel ); ?>"> <?php esc_html_e( 'days prior', 'hassle-free-date-list' ); ?>
							<p class="hfdl-note"><?php esc_html_e( 'Enter a number greater than or equal to 0', 'hassle-free-date-list' ); ?></p>
						</fieldset>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Overdue Dates', 'hassle-free-date-list' ); ?></th>
						<td>
							<fieldset>
								<label>
									<input type="hidden" name="closed" value="hide">
									<input type="checkbox" name="closed" value="show"<?php checked( $closed, 'show' ); ?>><?php esc_html_e( 'Display after the deadline.', 'hassle-free-date-list' ); ?>
								</label>
							</fieldset>
							<fieldset class="schedule_design">
								<legend><?php esc_html_e( 'Decoration when displayed', 'hassle-free-date-list' ); ?></legend>
								<label><input type="radio" name="closed_design" value="strikethrough" <?php checked( $closed_design, 'strikethrough' ); ?>> <?php esc_html_e( 'Strikethrough', 'hassle-free-date-list' ); ?></label><br />
								<label><input type="radio" name="closed_design" value="text" <?php checked( $closed_design, 'text' ); ?>> <?php esc_html_e( 'Any text', 'hassle-free-date-list' ); ?></label>:
								<input type="text" class="regular-text" name="closed_text" value="<?php echo esc_attr( $closed_text ); ?>">
							</fieldset>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Filled Dates', 'hassle-free-date-list' ); ?></th>
						<td>
							<fieldset>
								<label>
									<input type="hidden" name="full" value="hide">
									<input type="checkbox" name="full" value="show"<?php checked( $full, 'show' ); ?>> <?php esc_html_e( 'Display even if it be full', 'hassle-free-date-list' ); ?>
								</label>
							</fieldset>
							<fieldset class="schedule_design">
								<legend><?php esc_html_e( 'Decoration when displayed', 'hassle-free-date-list' ); ?></legend>
								<label><input type="radio" name="full_design" value="strikethrough" <?php checked( $full_design, 'strikethrough' ); ?>> <?php esc_html_e( 'Strikethrough', 'hassle-free-date-list' ); ?></label><br />
								<label><input type="radio" name="full_design" value="text" <?php checked( $full_design, 'text' ); ?>> <?php esc_html_e( 'Any text', 'hassle-free-date-list' ); ?></label>:
								<input type="text" class="regular-text" name="full_text" value="<?php echo esc_attr( $full_text ); ?>">
							</fieldset>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'If no dates to display', 'hassle-free-date-list' ); ?></th>
						<td>
							<p><?php esc_html_e( 'Text to be displayed in the form', 'hassle-free-date-list' ); ?>:</p>
							<input type="text" name="closed_dropdown_text" class="regular-text" value="<?php echo esc_attr( $closed_dropdown_text ); ?>">
							<p><?php esc_html_e( 'Content to be displayed in the block and the shortcode (HMTL can be used)', 'hassle-free-date-list' ); ?>:</p>
							<textarea name="closed_content" class="large-text code" cols="50" rows="10"><?php echo esc_textarea( $closed_content ); ?></textarea>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Date Format', 'hassle-free-date-list' ); ?></th>
						<td>
							<input type="text" class="regular-text" name="date_format" value="<?php echo esc_attr( $date_format ); ?>" placeholder="<?php echo esc_attr( get_option( 'date_format' ) ); ?>">
							<p><?php esc_html_e( 'If blank,WordPress general setting will be applied.', 'hassle-free-date-list' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Day Name Format', 'hassle-free-date-list' ); ?></th>
						<td>
							<fieldset>
								<label>
									<input type="hidden" name="custom_day_format" value="off">
									<input type="checkbox" name="custom_day_format" value="on"<?php checked( $custom_day_format, 'on' ); ?>><?php esc_html_e( 'Use custom day names.', 'hassle-free-date-list' ); ?>
								</label>
							</fieldset>
							<?php $this->day_name_list( $days ); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="tab_items" class="cp_tabpanel">
		<div class="wrap_aus_setting">
			<div class="form_area">
				<?php $this->add_del_buttons(); ?>
				<table class="widefat striped fixed" cellspacing="0">
					<thead id="items_header">
						<tr>
							<td class="check-column"><input type="checkbox" class="all_item_select" /></td>
							<th class="schedule-date-column"><?php esc_html_e( 'Date', 'hassle-free-date-list' ); ?></th>
							<th class="schedule-time-column"><?php esc_html_e( 'Time', 'hassle-free-date-list' ); ?></th>
						</tr>
					</thead>
					<tbody id="items_content" >
						<?php
							$this->make_item_list( $item_data, $item_count, $item_max );
						?>
					</tbody>
				</table>
				<?php $this->add_del_buttons(); ?>
				<input type="hidden" id="item_count" name="item_count" value="<?php echo esc_attr( $item_count ); ?>">
				<input type="hidden" id="item_max" name="item_max" value="<?php echo esc_attr( $item_max ); ?>">

			</div>
			<div class="note">
				<h3><?php esc_html_e( 'Specifications and usage', 'hassle-free-date-list' ); ?></h3>
				<ul>
					<li><?php esc_html_e( 'The time should be on a new line for each item.', 'hassle-free-date-list' ); ?></li>
					<li><?php esc_html_e( 'If you enter the time in the 00:00 format, you can adjust the font size of the hours and minutes using CSS.', 'hassle-free-date-list' ); ?></li>
					<li><?php esc_html_e( 'The order of the dates will be automatically sorted in descending order when you save it. But on front-end,the dates will be displayed in ascending order.', 'hassle-free-date-list' ); ?></li>
					<li>
					<?php esc_html_e( 'If the time has been filled, write "--" after the time. If "Display even if it be full" is checked, strikethrough or label will appear on time, else time don\'t show.', 'hassle-free-date-list' ); ?>
					<br>e.g. 18:00--</li>
					<li><?php echo esc_html_e( 'If you are using Contactform7, you can use the tag [datelist] or [datelist*]. This tag is similar to [select] and most of the options are the same as for [select].', 'hassle-free-date-list' ); ?></li>
				</ul>
			</div>
			</div>
		</div>
	</div>
</div>
		<?php
	}

	/**
	 * メタデータを保存する処理
	 *
	 * @param int $post_id 投稿のID.
	 * @return int
	 */
	public function save_options( $post_id ) {

		if ( ! isset( $_POST['hfdl_metabox_nonce'] ) ) {
			return $post_id;
		}
		if ( ! wp_verify_nonce( wp_unslash( $_POST['hfdl_metabox_nonce'] ), 'hfdl_metabox_nonce' ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		$post_type  = isset( $_POST['post_type'] ) ? wp_unslash( $_POST['post_type'] ) : '';
		$post_types = array( 'hfdl_schedule' );

		if ( in_array( $post_type, $post_types ) ) {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		} else {
			return $post_id;
		}

		// 承認ができたのでここからデータ保存処理
		$post_array = array(
			'cp_tab'               => 'number',
			'item_max'             => 'number',
			'item_count'           => 'number',
			'base_date'            => 'text',
			'base_date_abs'        => 'text',
			'base_date_rel'        => 'number',
			'closed'               => 'text',
			'closed_design'        => 'text',
			'closed_text'          => 'text',
			'closed_content'       => 'html',
			'closed_dropdown_text' => 'text',
			'full'                 => 'text',
			'full_design'          => 'text',
			'full_text'            => 'text',
			'date_format'          => 'text',
			'custom_day_format'    => 'text',
			'day-0'                => 'text',
			'day-1'                => 'text',
			'day-2'                => 'text',
			'day-3'                => 'text',
			'day-4'                => 'text',
			'day-5'                => 'text',
			'day-6'                => 'text',
		);

		foreach ( $post_array as $key => $value ) {
			$meta_value = '';
			if ( isset( $_POST[ $key ] ) ) {
				switch ( $value ) {
					case 'number':
						$meta_value = abs( wp_unslash( $_POST[ $key ] ) );
						break;
					case 'float-number':
						$meta_value = floatval( wp_unslash( $_POST[ $key ] ) );
						break;
					case 'color':
						$meta_value = sanitize_hex_color( wp_unslash( $_POST[ $key ] ) );
						break;
					case 'url':
						$meta_value = esc_url_raw( wp_unslash( $_POST[ $key ] ) );
						break;
					case 'html':
						// 投稿で使えるHTMLのみ通す
						$meta_value = wp_filter_post_kses( wp_unslash( $_POST[ $key ] ) );
						break;
					case 'text':
					default:
						// HTMLタグなどを全て取り除く
						$meta_value = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
						break;
				}
			}
			update_post_meta( $post_id, '_' . $key, $meta_value );

		}
		// save each schedule data
		if ( isset( $_POST['item_max'] ) ) {
			$max = intval( wp_unslash( $_POST['item_max'] ) );
		} else {
			$max = 0;
		}
		$save_data  = array();
		$item_array = array(
			'date'       => 'text',
			'date-check' => 'text',
			'time'       => 'text_area',
		);

		for ( $i = 1; $i < $max + 1; $i++ ) {
			$item_data = array();
			foreach ( $item_array as $key => $format ) {
				if ( isset( $_POST[ 'item_data-' . $i ][ $key ] ) ) {
					switch ( $format ) {
						case 'text_area':
							$item_data[ $key ] = sanitize_textarea_field( wp_unslash( $_POST[ 'item_data-' . $i ][ $key ] ) );
							break;
						case 'text':
							$item_data[ $key ] = sanitize_text_field( wp_unslash( $_POST[ 'item_data-' . $i ][ $key ] ) );
							break;
					}
				}
			}
			if ( ! empty( $item_data ) ) {
				$save_data[] = $item_data;
			}
		}

		array_multisort(
			array_map( 'strtotime', array_column( $save_data, 'date' ) ),
			SORT_ASC,
			$save_data
		);

		update_post_meta( $post_id, '_item_data', $save_data );

	}

	/**
	 * 編集画面のタイトル下にショートコードのソースを表示する
	 *
	 * @param object $post 投稿オブジェクト.
	 * @return void
	 */
	public function after_title_content( $post ) {
		if ( 'hfdl_schedule' === $post->post_type && 'publish' === $post->post_status ) {
			?>
	<p class="description">
		<label for="hfdl_schedule-formtag"><?php esc_html_e( 'Copy this form tag and paste it into your Contact Form 7 form template', 'hassle-free-date-list' ); ?>:</label>
		<span class="aus_shortcode wp-ui-highlight"><input type="text" id="hfdl_schedule-formtag" onfocus="jQuery(this).select();" readonly="readonly" class="large-text code" value="[datelist s_id=&quot;<?php echo esc_attr( $post->ID ); ?>&quot;]"></span>
	</p>
	<p class="description">
		<label for="hfdl_schedule-shortcode"><?php esc_html_e( 'Copy this shortcode and paste it into your post, page, or text widget content', 'hassle-free-date-list' ); ?>:</label>
		<span class="aus_shortcode wp-ui-highlight"><input type="text" id="hfdl_schedule-shortcode" onfocus="jQuery(this).select();" readonly="readonly" class="large-text code" value="[date_list sid=&quot;<?php echo esc_attr( $post->ID ); ?>&quot;]"></span>
	</p>
			<?php
		}
	}

	/**
	 * 管理画面のタブUIを返す
	 *
	 * @param array  $tabs タブ名の配列
	 * @param string $name タブID用の接頭辞
	 * @param int    $current 現在選択されているタブ
	 * @return void
	 */
	public function get_admin_tab_src( $tabs, $name = 'tab', $current ) {
		$order = 1;
		foreach ( $tabs as $tab ) {
			?>
		<input type="radio" name="cp_tab" id="<?php echo esc_attr( $name . '-' . $order ); ?>" value="<?php echo esc_attr( $order ); ?>" <?php checked( $current, $order ); ?> aria-controls="<?php echo esc_attr( $tab ); ?>">
		<label for="<?php echo esc_attr( $name . '-' . $order ); ?>"><?php echo esc_html( $tab ); ?></label>
			<?php
			$order ++;
		}
	}

	/**
	 * 管理画面のスニペット一覧表を作成
	 *
	 * @param array $item_data 日程のデータ
	 * @param int   $item_num アイテム通し番号
	 * @param int   $item_max アイテム数の最大値
	 * @return string スニペット一覧を返す
	 */
	public function make_item_list( $item_data, $item_num, $item_max ) {

		$item_counter = 1;

		// $item_dataが未定義の場合は値が空の行を1つ返す
		if ( empty( $item_data ) ) {
			$this->item_list_src( $item_counter, '', '', '' );
			return;
		}

		// $item_dataに一つもデータが無い場合は空の行を一つ返す
		if ( 0 == $item_num ) {
			$this->item_list_src( $item_counter, '', '', '' );
			return;
		}

		$items = array_reverse( $item_data );

		// ループでitemsの内容を回す.
		foreach ( $items as $item ) {
			$item_date       = $item['date'];
			$item_date_check = $item['date-check'];
			$item_time       = $item['time'];

			$this->item_list_src( $item_counter, $item_date, $item_time, $item_date_check );

			$item_counter ++;
		}
	}


	/**
	 * アイテム用のHTMLを出力
	 *
	 * @param int    $item_counter アイテムのカウンター数値
	 * @param string $item_date 日付データ
	 * @param string $item_time 時刻データ
	 * @param string $item_date_check 日付に取り消し線を付けるかどうか
	 * @return void
	 */
	public function item_list_src( $item_counter, $item_date, $item_time, $item_date_check ) {
		?>
			<tr class="registered item">
				<td class="data-check">
					<input type="checkbox" class="item_select" />
				</td>
				<td class="date-input">
					<input type="date" name="item_data-<?php echo esc_attr( $item_counter ); ?>[date]" value="<?php echo esc_attr( $item_date ); ?>" class="item_date"/>
					<p>
						<input type="hidden" name="item_data-<?php echo esc_attr( $item_counter ); ?>[date-check]" class="item-date-check" value="off" />
						<input type="checkbox" class="item-date-check" name="item_data-<?php echo esc_attr( $item_counter ); ?>[date-check]" <?php checked( $item_date_check, 'on', ); ?> value="on" /><?php esc_html_e( 'This date has been filled.', 'hassle-free-date-list' ); ?>
					</p>
				</td>
				<td class="data-time">
					<textarea name="item_data-<?php echo esc_attr( $item_counter ); ?>[time]" class="large-text item_time" style="width:auto;" col="5" rows="3" placeholder="11:00"><?php echo esc_html( $item_time ); ?></textarea>
				</td>
			</tr>
		<?php
	}
	/**
	 * Show item controll buttons
	 *
	 * @return void
	 */
	public function add_del_buttons() {
		?>
			<p>
				<button type="button" class="button add-item" id="add-item"><?php esc_html_e( 'Add Date', 'hassle-free-date-list' ); ?></button>
				<button type="button" class="button del-item" id="del-item"><?php esc_html_e( 'Delete checked Dates', 'hassle-free-date-list' ); ?></button>
			</p>
		<?php
	}

	/**
	 * Day name list
	 *
	 * @param array $days day name list
	 * @return void
	 */
	public function day_name_list( $days ) {
		?>
		<table class="day-name-list">
			<?php
			$i = 0;
			foreach ( $days as $day => $value ) {
				?>
				<tr>
					<th>
						<label for="day-<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $day ); ?></label>
					</th>
					<td>
						<input type="text" id="day-<?php echo esc_attr( $i ); ?>" name="day-<?php echo esc_attr( $i ); ?>" class="regular-text" value="<?php echo esc_attr( $value ); ?>">
					</td>
				</tr>
				<?php
				$i++;
			}
			?>
		</table>
		<?php
	}
}
