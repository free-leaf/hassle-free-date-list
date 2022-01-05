<?php
/**
 * The file that defines the plugin shortcorde
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
class Hassle_Free_Date_List_Shortcode {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_id    The ID of this plugin.
	 */
	private $plugin_id;

	/**
	 * ショートコード登録
	 *
	 * @param string $hassle_free_date_list The ID of this plugin
	 */
	public function __construct( $hassle_free_date_list ) {
		$this->plugin_id = $hassle_free_date_list;
		add_shortcode( 'date_list', array( $this, 's_code_date_list' ) );
	}

	/**
	 * ショートコードの定義
	 *
	 * @param array  $atts ショートコード属性.
	 * @param string $content ショートコードタグで囲んだコンテンツ.
	 * @return string ショートコード適用後のコード
	 */
	public function s_code_date_list( $atts, $content = null ) {
		$atts = shortcode_atts(
			array(
				'sid'           => '',
				'layout'        => 'row',
				'timelayout'    => 'row',
				'align'         => 'left',
				'largenumber'   => 'on',
				'bolddate'      => 'on',
				'largetime'     => 'on',
				'datecolor'     => '',
				'datebgcolor'   => '',
				'timecolor'     => '',
				'timebgcolor'   => '',
				'closedcolor'   => '',
				'closedbgcolor' => '',
				'fullcolor'     => '',
				'fullbgcolor'   => '',
			),
			$atts
		);

		$s_id = (int) $atts['sid'];

		$items = get_post_meta( $s_id, '_item_data', true );

		if ( empty( $items ) ) {
			return;
		}

		// Set date format
		$format = get_post_meta( $s_id, '_date_format', true );
		$format = empty( $format ) ? get_option( 'date_format' ) : $format;

		// Set day names
		$days      = get_post_meta( $s_id, '_custom_day_format', true );
		$day_names = array();
		if ( 'on' === $days ) {
			for ( $i = 0; $i < 7; $i++ ) {
				$day_names[] = get_post_meta( $s_id, '_day-' . $i, true );
			}
		}

		// Set class names
		$time_sep    = ':';
		$style_array = array();
		$class_array = array(
			'is-item-' . $atts['layout'],
			'is-time-' . $atts['timelayout'],
			'is-align-' . $atts['align'],
			'is-large-number-' . $atts['largenumber'],
			'is-bold-date-' . $atts['bolddate'],
			'is-large-time-' . $atts['largetime'],
		);

		// Set styles
		if ( ! empty( $atts['datebgcolor'] ) ) {
			$class_array[]                  = 'has-date-bg-color';
			$style_array['--date-bg-color'] = $atts['datebgcolor'];
		}
		if ( ! empty( $atts['timebgcolor'] ) ) {
			$class_array[]                  = 'has-time-bg-color';
			$style_array['--time-bg-color'] = $atts['timebgcolor'];
		}
		if ( ! empty( $atts['datecolor'] ) ) {
			$style_array['--date-color'] = $atts['datecolor'];
		}
		if ( ! empty( $atts['timecolor'] ) ) {
			$style_array['--time-color'] = $atts['timecolor'];
		}
		if ( ! empty( $atts['closedcolor'] ) ) {
			$style_array['--closed-color'] = $atts['closedcolor'];
		}
		if ( ! empty( $atts['closedbgcolor'] ) ) {
			$style_array['--closed-bg-color'] = $atts['closedbgcolor'];
		}
		if ( ! empty( $atts['fullcolor'] ) ) {
			$style_array['--full-color'] = $atts['fullcolor'];
		}
		if ( ! empty( $atts['fullbgcolor'] ) ) {
			$style_array['--full-bg-color'] = $atts['fullbgcolor'];
		}

		// Set class and style for outdate conditoin
		$closed = get_post_meta( $s_id, '_closed', true );
		if ( 'show' === $closed ) {
			$closed_design = get_post_meta( $s_id, '_closed_design', true );
			$closed_design = ! empty( $closed_design ) ? $closed_design : 'strikethrough';

			$class_array[] = 'is-closed-' . $closed_design;

			if ( 'text' === $closed_design ) {
				$closed_text = get_post_meta( $s_id, '_closed_text', true );
				$closed_text = '' !== $closed_text ? $closed_text : __( 'Closed', $this->plugin_id );
				if ( '' !== $closed_text ) {
					$style_array['--closed-text'] = "'" . $closed_text . "'";
				}
			}
		}

		// Set class and style for full conditon
		$full = get_post_meta( $s_id, '_full', true );
		if ( 'show' === $full ) {
			$full_design = get_post_meta( $s_id, '_full_design', true );
			$full_design = ! empty( $full_design ) ? $full_design : 'strikethrough';

			$class_array[] = 'is-full-' . $full_design;

			if ( 'text' === $full_design ) {
				$full_text = get_post_meta( $s_id, '_full_text', true );
				$full_text = '' !== $full_text ? $full_text : __( 'Sold Out', $this->plugin_id );
				if ( '' !== $full_text ) {
					$style_array['--full-text'] = "'" . $full_text . "'";
				}
			}
		}

		$class = $this->get_class( $class_array );

		$style = $this->get_style( $style_array, true );

		ob_start();
		?>
	<div class="hfdl-schedule <?php echo esc_attr( $class ); ?>"<?php echo $style; ?>>
		<?php
		$output_count = 0;
		foreach ( $items as $item ) {
			$date_class = array(
				'hfdl-schedule__item',
			);

			$date_value = $item['date'];
			if ( empty( $date_value ) ) {
				continue;
			}
			$date_elem = $this->get_date_value( $date_value, $s_id, $format, $day_names );
			// 日にちが今日より前の場合は処理を中断して抜ける
			$invert = intval( $date_elem[2] );
			if ( 0 < $invert ) {
				if ( 'hide' === $closed ) {
					continue;
				} else {
					$date_class[] = 'is-closed';
				}
			} else {
				if ( 'on' == $item['date-check'] ) {
					if ( 'show' !== $full ) {
						continue;
					} else {
						$date_class[] = 'hfdl-schedule__item-reserved';
					}
				}
			}

			?>
		<div class="<?php echo esc_attr( $this->get_class( $date_class ) ); ?>">
			<div class="hfdl-schedule__date">
				<?php echo wp_kses_post( $this->format_date( $date_elem ) ); ?>
			</div>
			<?php
			$time_value = $item['time'];
			if ( ! empty( $time_value ) ) {
				?>
			<div class="hfdl-schedule__times">
				<?php
				$times = $this->get_times( $time_value, $time_sep );
				foreach ( $times as $time ) {
					if ( isset( $time[1] ) && '' != $time[1] ) {
						$min = $time_sep . $time[1];
					} else {
						$min = '';
					}
					if ( 'closed' == $time[2] && 'show' !== $full ) {
						continue;
					}
					$time_class = 'closed' == $time[2] ? ' hfdl-schedule__time-reserved' : '';
					?>
				<div class="hfdl-schedule__time<?php echo esc_attr( $time_class ); ?>">
					<span class="hour-font-size"><?php echo esc_html( $time[0] ); ?></span>
					<span class="min-font-size"><?php echo esc_html( $min ); ?></span>
				</div>
					<?php
				}
				?>
			</div>
				<?php
			}
			?>
		</div>
			<?php
			$output_count ++;
		}
		if ( 0 === $output_count ) {
			$closed_content = get_post_meta( $s_id, '_closed_content', true );
			if ( '' !== $closed_content ) {
				echo wp_kses_post( apply_filters( 'the_content', $closed_content ) );
			}
		}
		?>
	</div>
		<?php
		$src = ob_get_clean();

		return $src;

	}

	/**
	 * 日にちのデータを年・月・日・曜日に分割して返す
	 *
	 * @param string $date_value 入力された日付文字列
	 * @param object $s_id スケジュールのID
	 * @param string $format 日付フォーマット
	 * @param array  $day_names カスタムの曜日
	 * @return array
	 */
	public static function get_date_value( $date_value, $s_id, $format, $day_names ) {
		// Today
		$today = new DateTime( 'today', wp_timezone() );
		// Schedule setting date
		$date = new DateTime( $date_value, wp_timezone() );

		// Get base date setting value
		$base = get_post_meta( $s_id, '_base_date', true );
		if ( 'absolute' === $base ) {
			$base_date_setting = get_post_meta( $s_id, '_base_date_abs', true );
			if ( '' == $base_date_setting ) {
				$base_date = new DateTime( $date_value, wp_timezone() );
			} else {
				$base_date = new DateTime( $base_date_setting, wp_timezone() );
			}
		} else {
			$diff_date = (int) get_post_meta( $s_id, '_base_date_rel', true );
			$base_date = new DateTime( $date_value, wp_timezone() );
			$base_date = $base_date->modify( '-' . $diff_date . ' days' );
		}

		// Compare $today and $base_date.
		$diff = $today->diff( $base_date );
		// $diff = $base_date->diff( $date );
		// invertが1の時、今日は基準を過ぎている。これで判定しないと翌日が表示されないケースがある。

		// カスタム曜日名を使うかどうか
		if ( ! empty( $day_names ) ) {
			$w = $day_names[ $date->format( 'w' ) ];
		} else {
			$w = '';
		}

		$date_values = array(
			$date->format( $format ),
			$w,
			$diff->invert,
		);

		return $date_values;
	}

	/**
	 * 時間のデータを分割して返す
	 *
	 * @param string $time_value 時刻の文字データ（改行区切り）
	 * @param string $needle 時刻を分ける文字
	 * @return array
	 */
	public static function get_times( $time_value, $needle ) {
		// 改行区切りを配列に変換する処理
		$array = explode( "\n", $time_value );
		$array = array_map( 'trim', $array );
		$array = array_filter( $array, 'strlen' );
		$array = array_values( $array );

		$times = array();

		// 最後に--があると募集終了を示すクラスを追加
		foreach ( $array as $value ) {
			if ( '--' === mb_substr( $value, -2 ) ) {
				$available = 'closed';
				$value     = rtrim( $value, '--' );
			} else {
				$available = 'open';
			}
			if ( '' == $needle ) {
				$time = array( $value, '', $available );
			} else {
				$time = explode( $needle, $value );
				if ( 1 == count( $time ) ) {
					$time[] = '';
				}
				$time[] = $available;

			}
			array_push( $times, $time );
		}

		return $times;
	}

	/**
	 * 配列からクラスを返す
	 *
	 * @param mixed   $class_names クラスの配列か文字列
	 * @param boolean $space クラスの文字列先頭にスペースを追加するかどうか
	 * @return string
	 */
	public function get_class( $class_names, $space = false ) {
		$class = '';
		if ( is_array( $class_names ) ) {
			$class = implode( ' ', $class_names );
		} else {
			$class = $class_names;
		}
		if ( $space ) {
			$class = ' ' . $class;
		}
		return $class;
	}

	/**
	 * 配列からスタイルを返す
	 *
	 * @param mixed   $style_names スタイルの配列
	 * @param boolean $attr_name style=""を出力するかどうか
	 * @return string
	 */
	public function get_style( $style_names, $attr_name = false ) {
		$style = '';
		foreach ( $style_names as $key => $value ) {
			$style .= $key . ':' . $value . ';';
		}
		if ( $attr_name && '' !== $style ) {
			$style = ' style="' . esc_attr( $style ) . '"';
		}
		return $style;
	}

	/**
	 * 日付の数字部分にクラスを追加する
	 *
	 * @param string $date_elem 日付のテキスト
	 * @return string
	 */
	public function format_date( $date_elem ) {

		$pattern = '/([0-9.,０-９．，]+)/u';
		$replace = '<span class="hfdl-large-number">$1</span>';
		$date    = preg_replace( $pattern, $replace, $date_elem[0] );

		return $date . $date_elem[1];
	}

	/**
	 * 時間の数字部分にクラスを追加する（未使用＆未検証）
	 *
	 * @param string $time_elem 時間のテキスト
	 * @return string
	 */
	public function format_time( $time_elem ) {

		$pattern = '/([0-9.,０-９．，]+)/u';
		$replace = '<span class="hfdl-large-number">$1</span>';
		$time    = preg_replace( $pattern, $replace, $time_elem[0] );

		return $time . $time_elem[1];
	}
}
