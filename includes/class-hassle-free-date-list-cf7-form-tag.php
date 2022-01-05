<?php
/**
 * The file that defines the form tag for contact form 7
 *
 * @since      1.0.0
 *
 * @package    Hassle_Free_Date_List
 * @subpackage Hassle_Free_Date_List/includes
 */

/**
 * Form tag for contact form 7
 *
 * @since      1.0.0
 * @package    Hassle_Free_Date_List
 * @subpackage Hassle_Free_Date_List/includes
 * @author     Makoto Nakao
 */
class Hassle_Free_Date_List_Cf7_Form_Tag {

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
	}

	/**
	 * スケジュール選択セレクトボックス用のフォームコードを登録
	 *
	 * @return void
	 */
	public function wpcf7_add_form_tag_datelist() {
		wpcf7_add_form_tag(
			array( 'datelist', 'datelist*' ),
			array( $this, 'wpcf7_datelist_form_tag_handler' ),
			array(
				'name-attr'         => true,
				'selectable-values' => true,
			)
		);
	}

	/**
	 * フォーム用ショートコードの表示
	 *
	 * @param string $tag タグの名前
	 * @return string
	 */
	public function wpcf7_datelist_form_tag_handler( $tag ) {
		if ( empty( $tag->name ) ) {
			return '';
		}

		$validation_error = wpcf7_get_validation_error( $tag->name );

		$class = wpcf7_form_controls_class( $tag->type );

		if ( $validation_error ) {
			$class .= ' wpcf7-not-valid';
		}

		// get schedule meta data
		$s_id  = $this->get_s_id_option( $tag );
		$items = get_post_meta( $s_id, '_item_data', true );
		if ( empty( $items ) ) {
			return;
		}

		// Set atts
		$atts = array();

		$atts['class']    = $tag->get_class_option( $class );
		$atts['id']       = $tag->get_id_option();
		$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

		if ( $tag->is_required() ) {
			$atts['aria-required'] = 'true';
		}

		if ( $validation_error ) {
			$atts['aria-invalid']     = 'true';
			$atts['aria-describedby'] = wpcf7_get_validation_error_reference(
				$tag->name
			);
		} else {
			$atts['aria-invalid'] = 'false';
		}

		// オプションの処理
		$multiple       = $tag->has_option( 'multiple' );
		$include_blank  = $tag->has_option( 'include_blank' );
		$first_as_label = $tag->has_option( 'first_as_label' );

		if ( $tag->has_option( 'size' ) ) {
			$size = $tag->get_option( 'size', 'int', true );

			if ( $size ) {
				$atts['size'] = $size;
			} elseif ( $multiple ) {
				$atts['size'] = 4;
			} else {
				$atts['size'] = 1;
			}
		}

		$default_choice = $tag->get_default_option(
			null,
			array(
				'multiple' => $multiple,
			)
		);

		// get date format
		$format = get_post_meta( $s_id, '_date_format', true );
		$format = empty( $format ) ? get_option( 'date_format' ) : $format;

		$days      = get_post_meta( $s_id, '_custom_day_format', true );
		$day_names = array();
		if ( 'on' === $days ) {
			for ( $i = 0; $i < 7; $i++ ) {
				$day_names[] = get_post_meta( $s_id, '_day-' . $i, true );
			}
		}

		// optionタグ
		$html = '';

		$hangover = wpcf7_get_hangover( $tag->name );

		foreach ( $items as $item ) {
			// 日付の設定
			$date_value = $item['date'];
			if ( empty( $date_value ) ) {
				continue;
			}
			if ( isset( $item['date-check'] ) && 'on' === $item['date-check'] ) {
				continue;
			}

			$date_elem = Hassle_Free_Date_List_Shortcode::get_date_value( $date_value, $s_id, $format, $day_names );
			$invert    = intval( $date_elem[2] );
			if ( 0 < $invert ) {
				continue;
			}

			// 時間の設定
			$time_value = $item['time'];
			if ( empty( $time_value ) ) {
				continue;
			} else {
				$times = Hassle_Free_Date_List_Shortcode::get_times( $time_value, '' );
			}

			foreach ( $times as $time ) {
				if ( 'closed' == $time[2] ) {
					continue;
				}

				$value = $date_elem[0] . $date_elem[1] . ' ' . $time[0];

				if ( $hangover ) {
					$selected = in_array( $value, (array) $hangover, true );
				} else {
					$selected = in_array( $value, (array) $default_choice, true );
				}

				$item_atts = array(
					'value'    => $value,
					'selected' => $selected ? 'selected' : '',
				);
				$item_atts = wpcf7_format_atts( $item_atts );

				$label = $value;

				$html .= sprintf(
					'<option %1$s>%2$s</option>',
					$item_atts,
					esc_html( $label )
				);
			}
		}

		// Set 1st option
		$label_1st = '';
		$values    = $tag->values;

		if ( '' == $html ) {
			// there are no schedule
			$label_1st = get_post_meta( $s_id, '_closed_dropdown_text', true );
		} else {
			if ( isset( $values[0] ) && '' !== $values[0] ) {
				$label_1st = $values[0];
			} else {
				if ( $include_blank ) {
					$label_1st = '---';
				}
			}
		}
		if ( '' !== $label_1st ) {
			$html = sprintf(
				'<option %1$s>%2$s</option>',
				wpcf7_format_atts( array( 'value' => '' ) ),
				esc_html( $label_1st )
			) . $html;
		}

		if ( $multiple ) {
			$atts['multiple'] = 'multiple';
		}

		$atts['name'] = $tag->name . ( $multiple ? '[]' : '' );

		$atts = wpcf7_format_atts( $atts );

		$html = sprintf(
			'<span class="wpcf7-form-control-wrap %1$s"><select %2$s>%3$s</select>%4$s</span>',
			sanitize_html_class( $tag->name ),
			$atts,
			$html,
			$validation_error
		);

		return $html;
	}

	/**
	 * Validation filste for form
	 *
	 * @param object $result $POST data
	 * @param object $tag Tag
	 * @return object
	 */
	public function wpcf7_datelist_validation_filter( $result, $tag ) {
		$name = $tag->name;

		$has_value = isset( $_POST[ $name ] ) && '' !== $_POST[ $name ];

		if ( $has_value && $tag->has_option( 'multiple' ) ) {
			$vals = array_filter(
				(array) $_POST[ $name ],
				function( $val ) {
					return '' !== $val;
				}
			);

			$has_value = ! empty( $vals );
		}

		if ( $tag->is_required() && ! $has_value ) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
		}

		return $result;
	}

	/**
	 * Add wpcf7 custom tag
	 *
	 * @return void
	 */
	public function wpcf7_add_tag_generator_datelist() {
		$tag_generator = WPCF7_TagGenerator::get_instance();
		$tag_generator->add(
			'datelist',
			__( 'date list', 'hassle-free-date-list' ),
			array( $this, 'wpcf7_tag_generator_datelist' )
		);
	}

	/**
	 * Definition of wpcf7 custom tag
	 *
	 * @param object $contact_form フォームオブジェクト
	 * @param string $args パラメータ
	 * @return void
	 */
	public function wpcf7_tag_generator_datelist( $contact_form, $args = '' ) {
		$args = wp_parse_args( $args, array() );

		$description = __( 'Generate a form-tag for a date list menu. For more details, see %s.', 'hassle-free-date-list' );
		$desc_link   = wpcf7_link( __( 'https://free-leaf.org/hassle-free-date-list/', 'hassle-free-date-list' ), __( 'Date List', 'hassle-free-date-list' ) );

		$schedules = Hassle_Free_Date_List_Block::schedule_list();

		?>
	<div class="control-box">
	<fieldset>
	<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

	<table class="form-table">
	<tbody>
		<tr>
		<th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></th>
		<td>
			<fieldset>
			<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></legend>
			<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'contact-form-7' ) ); ?></label>
			</fieldset>
		</td>
		</tr>

		<tr>
			<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
			<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
		</tr>
		<tr>
		<tr>
			<th scope="row">
				<label for="<?php echo esc_attr( $args['content'] . '-s_id' ); ?>"><?php echo esc_html( __( 'Schedule Id', 'contact-form-7' ) ); ?></label>
			</th>
			<td>
				<input type="text" name="s_id" class="s_idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-s_id' ); ?>" />
				<?php
				if ( ! empty( $schedules ) ) {
					?>
				<ul>
					<?php
					foreach ( $schedules as $schedule ) {
						?>
					<li>ID:<?php echo esc_html( $schedule['id'] . ' ' . $schedule['title'] ); ?></li>
						<?php
					}
					?>
				</ul>
					<?php
				}
				?>
			</td>
		</tr>
		<th scope="row"><?php echo esc_html( __( '1st option', 'contact-form-7' ) ); ?></th>
		<td>
			<fieldset>
			<legend class="screen-reader-text"><?php echo esc_html( __( 'First Option', 'contact-form-7' ) ); ?></legend>
			<label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><span class="description"><?php echo esc_html( __( 'First option text', 'contact-form-7' ) ); ?></span></label><br />
			<input type="text" name="values" class="values oneline" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>" /><br>
			<label><input type="checkbox" name="include_blank" class="option" /> <?php echo esc_html( __( 'Insert a blank item as the first option', 'contact-form-7' ) ); ?></label>
			</fieldset>
		</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label>
			</th>
			<td>
				<input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label>
			</th>
			<td>
				<input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" />
			</td>
		</tr>

	</tbody>
	</table>
	</fieldset>
	</div>

	<div class="insert-box">
		<input type="text" name="datelist" class="tag code" readonly="readonly" onfocus="this.select()" />

		<div class="submitbox">
		<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
		</div>

		<br class="clear" />

		<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( 'To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.', 'contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
	</div>
		<?php
	}

	/**
	 * Return s_id from option values in datelist form tag.
	 *
	 * @param array $tag tag data
	 * @return string
	 */
	public function get_s_id_option( $tag ) {
		$options = $tag['options'];
		$s_id    = '';
		$needle  = 's_id:';
		foreach ( $options as $option ) {
			if ( strpos( $option, $needle ) !== false ) {
				$s_id = str_replace( 's_id:', '', $option );
				return (int) $s_id;
			}
		}
		return $s_id;
	}
}
