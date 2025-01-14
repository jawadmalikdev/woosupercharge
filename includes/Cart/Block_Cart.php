<?php
/**
 * Class JawadMalik\Woosupercharge\Cart\Block_Cart
 *
 * @package woosupercharge
 */

namespace JawadMalik\Woosupercharge\Cart;

use JawadMalik\Woosupercharge\Cart\Cart_Trait;
use JawadMalik\Woosupercharge\Helpers;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Cart class used for Block themes.
 * 
 * @since 2.0
 */
class Block_Cart {

	/**
	 * @var array $product_list An array to store product data for later use.
	 */
	protected $product_list;

	/**
	 * @var array $settings An array to store WooSupercharge settings.
	 */
	protected $settings;

	/**
	 * Cart Ajax.
	 *
	 * @var Ajax $ajax Adds ajax endpoints for cart.
	 */
	protected $ajax;

	/**
	 * Our Cart trait used both for classic and block based themes.
	 */
	use Cart_Trait;

	/**
	 * Constructor method for initializing the class.
     * 
     * @param Ajax $ajax
     * 
     * @since 2.0
	 */
	public function __construct( Ajax $ajax ) {
		$this->settings = Helpers::get_woosupercharge_settings();
		$this->ajax     = $ajax;
		$this->hooks();
	}

	/**
	 * Register hooks for filters for classic themes.
	 */
	public function hooks(): void {
		// Register hooks from trait.
		$this->common_hooks();

		// Register the hooks to make functionality work for block based themes.
		add_action( 'wp_footer', array( $this, 'print_product_data' ) );
		add_action( 'woocommerce_before_shop_loop_item', array( $this, 'feed_product_data' ) );
	}

	/**
	 * Prints product data for use in JavaScript.
	 */
	public function print_product_data(): void {

		if ( ! Helpers::display_cart_popup( $this->settings['display_settings']['display_conditions'] ) ) {
			return;
		}

		if ( ! ( $this->product_list ) ) {
			return;
		}

		$code = 'const woosupercharge_wc_product_list = {';

		foreach ( $this->product_list as $product ) {
			$code .= " 'item_" . $product['item_id'] . "' : " . json_encode( $product ) . ',';
		}

		$code .= '};';

		wc_enqueue_js( $code );

		foreach ( $this->product_list as $product ) {

			wc_enqueue_js(
				" jQuery( '.wc-block-product-template .post-" . $product['item_id'] . " .add_to_cart_button' ).on( 'click', function() {
					    if( typeof woosupercharge_script !== 'undefined' ){woosupercharge_script.fillModal( woosupercharge_wc_product_list.item_" . $product['item_id'] . ' )};
				});
				'
			);
		}
	}

	/**
	 * Feed product data for block-based themes.
	 */
	public function feed_product_data(): void {
		global $product;

		// Exclude feeds.
		if ( is_feed() ) {
			return;
		}

		$product_id = $product->get_id();

		$item = array();

		$item['name']      = get_the_title( $product_id );
		$item['thumbnail'] = get_the_post_thumbnail_url( $product_id );
		$item['price']     = $product->get_price();
		$item['item_id']   = $product->get_id();

		// Append the item to the list.
		$this->product_list[] = $item;
	}
}
