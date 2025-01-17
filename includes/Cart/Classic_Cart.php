<?php
/**
 * Class JawadMalik\Woosupercharge\Cart\Classic_Cart
 *
 * @package woosupercharge
 */

namespace JawadMalik\Woosupercharge\Cart;

use JawadMalik\Woosupercharge\Settings;
use JawadMalik\Woosupercharge\Cart\Cart_Trait;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Cart class used for Classic themes.
 *
 * @since 2.0
 */
class Classic_Cart {

	/**
	 * Plugin Settings.
	 *
	 * @var Settings $settings An array to store WooSupercharge settings.
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
	 * @param Settings $settings plugin settings.
	 * @param Ajax     $ajax cart ajax.
	 *
	 * @since 2.0
	 */
	public function __construct( Settings $settings, Ajax $ajax ) {
		$this->settings = $settings;
		$this->ajax     = $ajax;
		$this->hooks();
	}

	/**
	 * Register hooks for filters for classic themes.
	 */
	public function hooks(): void {
		// Register hooks from trait.
		$this->common_hooks();
	}
}
