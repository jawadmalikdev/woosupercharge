<?php
/**
 * Trait JawadMalik\Woosupercharge\Cart\Cart_Trait
 *
 * @package woosupercharge
 */

namespace JawadMalik\Woosupercharge\Cart;

use JawadMalik\Woosupercharge\Helpers;

/**
 * Shared trait for Classic and Block Theme cart objects.
 *
 * @since 2.0
 */
trait Cart_Trait {

	/**
	 * Register hooks for actions and filters.
	 */
	public function common_hooks(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'load_cart_popup_script' ) );
		add_action( 'wp_footer', array( $this, 'cart_popup_markup' ) );
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'add_ajax_fragments' ), 10, 1 );
		add_action( 'woocommerce_add_to_cart', array( $this, 'save_last_added_item' ), 10, 1 );
	}

	/**
	 * Enqueues the script and styles for the cart popup.
	 */
	public function load_cart_popup_script(): void {

		$settings = $this->settings->get_plugin_settings();

		if ( ! Helpers::display_cart_popup( $settings['cart_display_conditions_settings']['display_conditions'] ) ) {
			return;
		}

		wp_enqueue_script( 'cart_popup_script', WOOSUPERCHARGE_PLUGIN_URL . 'assets/js/cart-popup.min.js', array( 'jquery' ), WOOSUPERCHARGE_VERSION, true );
		wp_localize_script(
			'cart_popup_script',
			'woosupercharge',
			array(
				'layout'             => $settings['cart_general_settings']['layout'],
				'position'           => $settings['cart_general_settings']['position'],
				'popup_close_after'  => apply_filters( 'woosupercharge_popup_close_time', $settings['cart_general_settings']['popup_close_after'] ),
				'wc_single_ajax_url' => \WC_AJAX::get_endpoint( 'woosupercharge_add_to_cart' ),
				'wc_cart_url'        => wc_get_cart_url(),
			)
		);

		wp_enqueue_style( 'cart_popup_css', WOOSUPERCHARGE_PLUGIN_URL . 'assets/css/cart-popup.css', array(), WOOSUPERCHARGE_VERSION );
	}

	/**
	 * Outputs the HTML markup for the cart popup.
	 */
	public function cart_popup_markup(): void {
		ob_start();
		?>

			<!-- Modal Container -->
			<div class="woosupercharge-modal <?php echo esc_attr( $this->settings->get_plugin_settings()['cart_general_settings']['position'] ); ?> " style='display: none;'>
				<div class="woosupercharge-modal-container">
					<span class="woosupercharge-modal-close">X</span>
					<div class="woosupercharge-modal-content"></div>
				</div>
			</div>

		<?php

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo ob_get_clean();
	}

	/**
	 * Saves the last added item to the cart.
	 *
	 * @param string $item_key The key of the added item.
	 */
	public function save_last_added_item( $item_key ): void {
		update_option( 'woosupercharge_last_added_cart_key', $item_key );
	}

	/**
	 * Adds AJAX fragments to update the cart content.
	 *
	 * @param array<mixed> $fragments The existing fragments.
	 * @return array<string,string> The updated fragments.
	 */
	public function add_ajax_fragments( $fragments ): array {

		$cart_content = $this->get_cart_content();

		// Cart content.
		$fragments['div.woosupercharge-modal-content'] = '<div class="woosupercharge-modal-content">' . $cart_content . '</div>';

		return $fragments;
	}

	/**
	 * Retrieves the cart content for display.
	 *
	 * @return string|false The HTML markup of the cart content.
	 */
	public function get_cart_content() {

		// Get last cart item entry.
		$cart_item_key = get_option( 'woosupercharge_last_added_cart_key' );
		// The cart layout to display.
		$list_view_option = $this->settings->get_plugin_settings()['cart_general_settings']['layout'];

		if ( ! $cart_item_key ) {
			return '';
		}

		// Remove key from db.
		delete_option( 'woosupercharge_last_added_cart_key' );

		$args = Helpers::get_cart_template_args( $cart_item_key, $list_view_option );

		ob_start();
		wc_get_template( 'product-card.php', $args, '', WOOSUPERCHARGE_PLUGIN_DIR . '/templates/' );
		return ob_get_clean();
	}
}