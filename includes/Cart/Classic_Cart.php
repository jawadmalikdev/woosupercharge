<?php
/**
 * Class JawadMalik\Woosupercharge\Cart\Classic_Cart
 *
 * @package woosupercharge
 */

namespace JawadMalik\Woosupercharge\Cart;

use JawadMalik\Woosupercharge\Cart\Cart_Trait;
use JawadMalik\Woosupercharge\Helpers;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Cart class used for Classic themes.
 * 
 * @since 2.0
 */
class Classic_Cart {

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
	 * @param Ajax $ajax.
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
	}
}
