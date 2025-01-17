<?php
/**
 * Class JawadMalik\Woosupercharge\Cart\Ajax
 *
 * @since 2.0
 * @package woosupercharge
 */

namespace JawadMalik\Woosupercharge\Cart;

/**
 * Add our callback in woocommerce ajax action.
 *
 * @since 2.0
 */
class Ajax {

	/**
	 * Constructor.
	 *
	 * @since 2.0
	 */
	public function __construct() {
		add_action( 'wc_ajax_woosupercharge_add_to_cart', array( $this, 'woosupercharge_add_to_cart' ) );
	}

	/**
	 * Handles AJAX request for adding to the cart.
	 */
	public function woosupercharge_add_to_cart(): void {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['add-to-cart'] ) ) {
			wp_die();
		}

		\WC_AJAX::get_refreshed_fragments();
	}
}
