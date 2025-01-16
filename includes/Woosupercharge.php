<?php
/**
 * Class JawadMalik\Woosupercharge\Woosupercharge
 *
 * @package woosupercharge
 */

namespace JawadMalik\Woosupercharge;

use JawadMalik\Woosupercharge\Settings;
use JawadMalik\Woosupercharge\Cart\Ajax as Cart_Ajax;
use JawadMalik\Woosupercharge\Cart\Block_Cart;
use JawadMalik\Woosupercharge\Cart\Classic_Cart;
use JawadMalik\Woosupercharge\Hooks;

/**
 * Plugin main class.
 *
 * @since 2.0
 */
class Woosupercharge {

	/**
	 * Plugin settings.
	 *
	 * @var Settings
	 */
	protected $settings;

	/**
	 * Cart object.
	 *
	 * @var Classic_Cart|Block_Cart $cart.
	 */
	protected $cart;

	/**
	 * Plugin hooks.
	 *
	 * @var Hooks
	 */
	protected $hooks;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $version;

	/**
	 * Constructor.
	 *
	 * @since 2.0
	 *
	 * @param Settings $settings Main settings object for the plugin.
	 * @param string   $version Main version of plugin.
	 */
	public function __construct( Settings $settings, $version ) {
		$this->settings = $settings;
		$this->version  = $version;
	}

	/**
	 * Setup the plugin.
	 *
	 * @since 2.0
	 */
	public function setup(): Woosupercharge {

		if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
			$cart = new Block_Cart(
				$this->get_settings(),
				new Cart_Ajax()
			);
		} else {
			$cart = new Classic_Cart(
				$this->get_settings(),
				new Cart_Ajax()
			);
		}

		$this->cart  = $cart;
		$this->hooks = new Hooks( $this );
		$this->hooks->add_hooks();
		return $this;
	}

	/**
	 * Get plugin settings.
	 *
	 * @return Settings
	 */
	public function get_settings(): Settings {
		return $this->settings;
	}

	/**
	 * Get plugin cart.
	 *
	 * @return Classic_Cart|Block_Cart
	 */
	public function get_cart(): Classic_Cart|Block_Cart {
		return $this->cart;
	}

	/**
	 * When the plugin is loaded:
	 *  - Load the plugin's text domain.
	 *
	 * @uses "plugins_loaded" action
	 */
	public function plugin_loaded(): void {
		load_plugin_textdomain( 'woosupercharge', false, WOOSUPERCHARGE_PLUGIN_DIR . '/languages/' );
	}
}
