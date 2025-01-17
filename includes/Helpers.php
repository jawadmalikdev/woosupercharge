<?php
namespace JawadMalik\Woosupercharge;

use phpDocumentor\Reflection\Types\Boolean;

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
	 * Determines whether to display the cart popup based on given display conditions.
	 *
	 * @param array<string> $display_conditions An array of display conditions.
	 * @return bool True if the cart popup should be displayed, false otherwise.
	 */
	public static function display_cart_popup( $display_conditions ) {

		$conditions_array = array();

		foreach ( $display_conditions as $index => $condition ) {
			$conditions_array[ $condition ] = $condition;
		}

		if ( isset( $display_conditions['all'] ) ) {
			return true;
		}

		if ( isset( $display_conditions['archive'] ) && function_exists( 'is_shop' ) && is_shop() ) {
			return true;
		}
		if ( isset( $display_conditions['categories-archive'] ) && is_tax( 'product_cat' ) ) {
			return true;
		}
		if ( isset( $display_conditions['tags-archive'] ) && is_tax( 'product_tag' ) ) {
			return true;
		}
		if ( isset( $display_conditions['product-attributes-archive'] ) && is_tax( 'product_attribute' ) ) {
			return true;
		}
		if ( isset( $display_conditions['single'] ) && is_singular( 'product' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Callback function for rendering a layout selection field.
	 *
	 * @param array<mixed> $args An array of arguments for rendering the field.
	 */
	public static function callback_layout_select( $args ):void {

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
	 * @param array<mixed> $args An array of arguments for rendering the field.
	 */
	public static function callback_position_select( $args ):void {

		$value = $args['value'];

		$name = $args['option_name'] . '[' . $args['section'] . '][' . $args['name'] . ']';
		ob_start();
		?>
				<div class="position-select-container" id="positionSelect">
					<label for="positionTop" <?php self::active( $value, 'top' ); ?> ><?php esc_html__( 'Top', 'woosupercharge' ); ?></label>
					<input type="radio" id="positionTop" name=<?php echo esc_attr( $name ); ?> value="top">
					<label for="positionBottom" <?php self::active( $value, 'bottom' ); ?> ><?php esc_html__( 'Bottom', 'woosupercharge' ); ?></label>
					<input type="radio" id="positionBottom" name=<?php echo esc_attr( $name ); ?> value="bottom">
				</div>
			<?php

			echo ob_get_clean();
	}

	/**
	 * Callback function for rendering a slider field.
	 *
	 * @param array<mixed> $args An array of arguments for rendering the field.
	 */
	public static function callback_slider( $args ):void {
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
	 * @param array<mixed> $args An array of arguments for rendering the field.
	 */
	public static function callback_conditions_repeater( $args ):void {
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

		$html .= '</tbody></table><button type="button" class="button woosupercharge-add-condition">' . esc_html__( 'Add Display Condition', 'woosupercharge' ) . '</button>';

		echo $html;
	}

	/**
	 * Process the cart information and return product data to be used
	 * in the view.
	 *
	 * @param string $cart_item_key Last saved item.
	 * @param string $list_view_option Cart popup layout.
	 *
	 * @return array The cart template arguments.
	 * @since 2.0
	 */
	public static function get_cart_template_args( $cart_item_key, $list_view_option ) {

		$cart = WC()->cart->get_cart();

		$item       = isset( $cart[ $cart_item_key ] ) ? $cart[ $cart_item_key ] : array();
		$product_id = isset( $item['product_id'] ) ? $item['product_id'] : 0;

		$product = wc_get_product( $product_id );

		$product_data = $product ? array(
			'thumbnail'     => $product->get_image(),
			'thumbnail_url' => esc_url( (string) get_the_post_thumbnail_url( $product->get_id() ) ),
			'product_name'  => $product->get_title(),
			'product_price' => wp_strip_all_tags( wc_price( (float) $product->get_price() ) ),
			'cart_url'      => wc_get_cart_url(),
		) : array(
			'thumbnail'     => '',
			'thumbnail_url' => '',
			'product_name'  => '',
			'product_price' => '',
			'cart_url'      => '',
		);

		// Prepare variables.
		$modal_style = 'list' === $list_view_option && $product_data['thumbnail_url']
		? 'style=background-image:url("' . esc_url( $product_data['thumbnail_url'] ) . '")'
		: '';

		$show_thumbnail = 'list' !== $list_view_option && $product_data['thumbnail'];

		// Allowed HTML for the thumbnail.
		$allowed_thumbnail_html = array(
			'img' => array(
				'src'      => array(),
				'width'    => array(),
				'height'   => array(),
				'class'    => array(),
				'alt'      => array(),
				'decoding' => array(),
				'loading'  => array(),
				'srcset'   => array(),
				'sizes'    => array(),
				'style'    => array(),
			),
		);

		$product_data['modal_style']            = $modal_style;
		$product_data['show_thumbnail']         = $show_thumbnail;
		$product_data['allowed_thumbnail_html'] = $allowed_thumbnail_html;

		return $product_data;
	}

	/**
	 * Check if Woocommerce is active.
	 *
	 * @since 2.0
	 */
	public static function is_woocommerce_active():bool {
		$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		if ( in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins ) || class_exists( 'WooCommerce' ) ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Outputs the HTML attribute for an "active" class based on a condition
	 * works similar to WordPress selected function.
	 *
	 * @param mixed $value The value to compare against.
	 * @param mixed $current The current value.
	 */
	public static function active( $value, $current ): void {

		if ( $value === $current ) {
			echo 'class="active"';
		}
		return;
	}
}
