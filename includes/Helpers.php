<?php
namespace JawadMalik\Woosupercharge;
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;


/**
 * Class WooSupercharge_Helpers
 *
 * Helper class containing static methods for various utility functions
 * used in the WooSupercharge plugin settings and functionality.
 */
class Helpers {

	/**
	 * Retrieves WooSupercharge settings including general and display settings.
	 *
	 * @return array An array containing general and display settings.
	 */
	public static function get_woosupercharge_settings() {
		$woosupercharge_settings = array(
			'general_settings' => array(),
			'display_settings' => array(),
		);

		$saved_general_settings = get_option( 'woosupercharge-general-settings' );
		$saved_display_settings = get_option( 'woosupercharge-display-conditions-settings' );

		$woosupercharge_settings['general_settings']['layout']             = isset( $saved_general_settings['layout'] ) ? $saved_general_settings['layout'] : 'card';
		$woosupercharge_settings['general_settings']['position']           = isset( $saved_general_settings['position'] ) ? $saved_general_settings['position'] : 'top';
		$woosupercharge_settings['general_settings']['popup_close_after']  = isset( $saved_general_settings['popup_close_after'] ) ? $saved_general_settings['popup_close_after'] : 5;
		$woosupercharge_settings['display_settings']['display_conditions'] = isset( $saved_display_settings['display_conditions'] ) ? $saved_display_settings['display_conditions'] : array( 'all' );

		return $woosupercharge_settings;
	}

	/**
	 * Determines whether to display the cart popup based on given display conditions.
	 *
	 * @param array $display_conditions An array of display conditions.
	 * @return bool True if the cart popup should be displayed, false otherwise.
	 */
	public static function display_cart_popup( $display_conditions ) {

		$conditions_array = array();

		foreach ( $display_conditions as $index => $condition ) {
			$conditions_array[ $condition ] = $condition;
		}

		if ( isset( $conditions_array['all'] ) ) {
			return true;
		}

		if ( isset( $conditions_array['archive'] ) && function_exists( 'is_shop' ) && is_shop() ) {
			return true;
		}
		if ( isset( $conditions_array['categories-archive'] ) && is_tax( 'product_cat' ) ) {
			return true;
		}
		if ( isset( $conditions_array['tags-archive'] ) && is_tax( 'product_tag' ) ) {
			return true;
		}
		if ( isset( $conditions_array['product-attributes-archive'] ) && is_tax( 'product_attribute' ) ) {
			return true;
		}
		if ( isset( $conditions_array['single'] ) && is_singular( 'product' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Callback function for rendering a layout selection field.
	 *
	 * @param array $args An array of arguments for rendering the field.
	 */
	public static function callback_layout_select( $args ) {

		$value = $args['value'];
		$name  = $args['option_name'] . '[' . $args['section'] . '][' . $args['name'] . ']';
		ob_start();
		?>
				<div class="image-select">
					<label for="card">
						<input type="radio" id="card" name=<?php echo esc_attr( $name ); ?> value="card" <?php checked( $value, 'card' ); ?> >
						<img src='<?php echo WOOSUPERCHARGE_PLUGIN_URL . 'assets/images/card.svg'; ?>' alt="Card Layout">
					</label>

					<label for="list">
						<input type="radio" id="list" name=<?php echo esc_attr( $name ); ?> value="list" <?php checked( $value, 'list' ); ?>>
						<img src='<?php echo WOOSUPERCHARGE_PLUGIN_URL . 'assets/images/list.svg'; ?>' alt="List Layout">
					</label>
				</div>
			<?php

			echo ob_get_clean();
	}

	/**
	 * Callback function for rendering a position selection field.
	 *
	 * @param array $args An array of arguments for rendering the field.
	 */
	public static function callback_position_select( $args ) {

		$value = $args['value'];

		$name  = $args['option_name'] . '[' . $args['section'] . '][' . $args['name'] . ']';
		ob_start();
		?>
				<div class="position-select-container" id="positionSelect">
					<label for="positionTop" <?php self::active( $value, 'top' ); ?> ><?php _e( 'Top', 'woosupercharge' ); ?></label>
					<input type="radio" id="positionTop" name=<?php echo esc_attr( $name ); ?> value="top">
					<label for="positionBottom" <?php self::active( $value, 'bottom' ); ?> ><?php _e( 'Bottom', 'woosupercharge' ); ?></label>
					<input type="radio" id="positionBottom" name=<?php echo esc_attr( $name ); ?> value="bottom">
				</div>
			<?php

			echo ob_get_clean();
	}

	/**
	 * Callback function for rendering a slider field.
	 *
	 * @param array $args An array of arguments for rendering the field.
	 */
	public static function callback_slider( $args ) {
		$value = $args['value'];
		$name  = $args['option_name'] . '[' . $args['section'] . '][' . $args['name'] . ']';
		ob_start();
		?>
			<div class="slider-container">
				<input type="range" id="slider" min="1" max="60" value=<?php echo esc_attr( $value ); ?> >
				<input name="<?php echo esc_attr( $name ); ?>" type="number" id="sliderValue" value=<?php echo esc_attr( $value ); ?> min="1" max="60">
			</div>
		<?php

		echo ob_get_clean();
	}

	/**
	 * Callback function for rendering a conditions repeater field.
	 *
	 * @param array $args An array of arguments for rendering the field.
	 */
	public static function callback_conditions_repeater( $args ) {
		$html  = '';
		$count = 0;
		$html .= '<table id="woosupercharge-conditions-table">
			<tbody>';

		$current_values = $args['value'];

		foreach ( $current_values as $index => $current_value ) {
			$html .= '<tr class="single_condition"><td>';
			$html .= sprintf( '<select class="select_condition" name="%1$s[%2$s][%3$s][' . $count . ']" id="%1$s[%2$s][%3$s]">', $args['option_name'], $args['section'], $args['name'] );

			foreach ( $args['options'] as $key => $value ) {

				$selected = ( $key == $current_value ) ? 'selected' : '';
				$html    .= sprintf( '<option value="%s" %s>%s</option>', $key, $selected, $value );

			}

			$html .= '</select></td>';
			$html .= '<td><span class="woosupercharge-rmv-condition">&#10060</span></td>
				</tr>';

			++$count;
		}

		$html .= '</tbody></table><button type="button" class="button woosupercharge-add-condition">' . __( 'Add Display Condition', 'woosupercharge' ) . '</button>';

		echo $html;
	}

	/**
	 * Outputs the HTML attribute for an "active" class based on a condition
	 * works similar to WordPress selected function.
	 *
	 * @param mixed $value The value to compare against.
	 * @param mixed $current The current value.
	 */
	public static function active( $value, $current ) {

		if ( $value === $current ) {
			echo 'class="active"';
		}
		return;
	}
}
