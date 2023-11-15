<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooSupercharge_Cart' ) ) {

	/**
	 * Class WooSupercharge_Cart
	 *
	 * Main class for handling cart-related functionalities and interactions.
	 */
	class WooSupercharge_Cart {
		
		/**
		 * @var array $product_list An array to store product data for later use.
		 */
		public $product_list;

		/**
		 * @var array $settings An array to store WooSupercharge settings.
		 */
		public $settings;

		/**
		 * Constructor method for initializing the class.
		 */
		public function __construct() {
			$this->hooks();
			$this->settings = WooSupercharge_Helpers::get_woosupercharge_settings();

			/**
			 * After testing thoroughly i found that 
			 * in block theme when using blocks to show
			 * the products the jquery events added_to_cart is not triggered
			 * so we are going to load the below assets and trigger that event
			 * ourselves with the needed information.
			 */
			if ( wp_is_block_theme() ) {
				$this->block_based_themes_hooks();
			}
		}

		/**
		 * Additional hooks specific to block-based themes.
		 */
		public function block_based_themes_hooks() {
			add_action( 'wp_footer', array( $this, 'print_product_data' ) );
			add_action( 'woocommerce_before_shop_loop_item', array( $this, 'feed_product_data' ) );
		}

		/**
		 * Register hooks for actions and filters.
		 */
		public function hooks() {
			add_action( 'wp_enqueue_scripts', array( $this, 'load_cart_popup_script' ) );
			add_action( 'wp_footer', array( $this, 'cart_popup_markup' ) );
			add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'add_ajax_fragments' ), 10, 1 );
			add_action( 'woocommerce_add_to_cart', array( $this, 'save_last_added_item' ), 10, 1 );
			add_action( 'wc_ajax_woosupercharge_add_to_cart', array( $this, 'woosupercharge_add_to_cart' ) );
		}

		/**
		 * Enqueues the script and styles for the cart popup.
		 */
		public function load_cart_popup_script() {

			if( ! WooSupercharge_Helpers::display_cart_popup( $this->settings['display_settings']['display_conditions'] ) ){
				return;
			}
			
			wp_enqueue_script( 'cart_popup_script', WOOSUPERCHARGE_PLUGIN_URL . 'assets/js/cart-popup.min.js', array( 'jquery' ), WOOSUPERCHARGE_VERSION );
			wp_localize_script(
				'cart_popup_script',
				'woosupercharge',
				array(
					'layout'             => $this->settings['general_settings']['layout'],
					'position'           => $this->settings['general_settings']['position'],
					'popup_close_after'  => apply_filters( 'change_woosupercharge_popup_close_time', $this->settings['general_settings']['popup_close_after'] ),
					'wc_single_ajax_url' => WC_AJAX::get_endpoint( 'woosupercharge_add_to_cart' ),
					'wc_cart_url'        => wc_get_cart_url(),
				)
			);

			wp_enqueue_style( 'cart_popup_css', WOOSUPERCHARGE_PLUGIN_URL . 'assets/css/cart-popup.css', false, WOOSUPERCHARGE_VERSION );
		}

		/**
		 * Outputs the HTML markup for the cart popup.
		 */
		public function cart_popup_markup() {
			ob_start();
			?>

			<!-- Modal Container -->
			<div class="woosupercharge-modal <?php echo esc_attr( $this->settings['general_settings']['position'] ); ?> ">
				<div class="woosupercharge-modal-container">
					<span class="woosupercharge-modal-close">X</span>
					<div class="woosupercharge-modal-content"></div>
				</div>
			</div>

			<?php

			echo ob_get_clean();
		}

		/**
		 * Prints product data for use in JavaScript.
		 */
		public function print_product_data() {

			if( ! WooSupercharge_Helpers::display_cart_popup( $this->settings['display_settings']['display_conditions'] ) ) {
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
					" jQuery( '.wp-block-query .post-" . $product['item_id'] . " .add_to_cart_button' ).on( 'click', function() {
					    if( typeof woosupercharge_script !== 'undefined' ){woosupercharge_script.fillModal( woosupercharge_wc_product_list.item_" . $product['item_id'] . ' )};
				});
				'
				);
			}
		}
		
		/**
		 * Feed product data for block-based themes.
		 */
		public function feed_product_data() {
			global $product, $woocommerce_loop;

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

		/**
		 * Handles AJAX request for adding to the cart.
		 */
		public function woosupercharge_add_to_cart() {

			if ( ! isset( $_POST['add-to-cart'] ) ) {
				wp_die();
			}

			// manually trigger the ajax action.
			do_action( 'woocommerce_ajax_added_to_cart', intval( $_POST['add-to-cart'] ) );

			WC_AJAX::get_refreshed_fragments();
		}

		/**
		 * Saves the last added item to the cart.
		 *
		 * @param string $item_key The key of the added item.
		 */
		public function save_last_added_item( $item_key ) {
			$key = $item_key;
			update_option( 'woosupercharge_last_added_cart_key', $item_key );
		}

		/**
		 * Adds AJAX fragments to update the cart content.
		 *
		 * @param array $fragments The existing fragments.
		 * @return array The updated fragments.
		 */
		public function add_ajax_fragments( $fragments ) {

			$cart_content = $this->get_cart_content();

			// Cart content
			$fragments['div.woosupercharge-modal-content'] = '<div class="woosupercharge-modal-content">' . $cart_content . '</div>';

			return $fragments;
		}

		/**
		 * Retrieves the cart content for display.
		 *
		 * @return string The HTML markup of the cart content.
		 */
		public function get_cart_content() {

			// Get last cart item entry
			$cart_item_key = get_option( 'woosupercharge_last_added_cart_key' );

			if ( ! $cart_item_key ) {
				return;
			}

			// Remove from the database
			delete_option( 'woosupercharge_last_added_cart_key' );

			$args = array(
				'cart_item_key' => $cart_item_key,
				'listview'      => $this->settings['general_settings']['layout'],
			);

			ob_start();
			wc_get_template( 'product-card.php', $args, '', WOOSUPERCHARGE_PLUGIN_DIR . '/templates/' );
			return ob_get_clean();
		}

	}

}
