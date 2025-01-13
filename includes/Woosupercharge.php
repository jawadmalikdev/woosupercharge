<?php
/**
 * Class JawadMalik\Woosupercharge\Woosupercharge
 *
 * @package woosupercharge
 */

namespace JawadMalik\Woosupercharge;

use JawadMalik\Woosupercharge\Settings;
use JawadMalik\Woosupercharge\Cart;
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
	 * @var Cart
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
		$this->cart  = new Cart();
		$this->hooks = new Hooks( $this );
		$this->hooks->addHooks();
		return $this;
	}

	/**
	 * Get plugin settings.
	 *
	 * @return Settings
	 */
	public function getSettings(): Settings {
		return $this->settings;
	}

	/**
	 * Get plugin cart.
	 *
	 * @return Cart
	 */
	public function getCart(): Cart {
		return $this->cart;
	}

	/**
	 * When the plugin is loaded:
	 *  - Load the plugin's text domain.
	 *
	 * @uses "plugins_loaded" action
	 */
	public function pluginLoaded(): void {
		load_plugin_textdomain( 'woosupercharge', false, WOOSUPERCHARGE_PLUGIN_DIR . '/languages/' );
	}
}
