<?php
/**
 * Class JawadMalik\Woosupercharge\Admin\Abstract_Admin_Page
 *
 * @since 2.0
 * @package woosupercharge
 */

namespace JawadMalik\Woosupercharge\Admin;

/**
 * Base class representing a WordPress admin page.
 *
 * @since 2.0
 */
abstract class Abstract_Admin_Page {

	/**
	 * Admin page slug.
	 *
	 * @since 2.0
	 * @var string
	 */
	private $slug;

	/**
	 * Admin page title.
	 *
	 * @since 2.0
	 * @var string
	 */
	private $title;

	/**
	 * Admin page capability.
	 *
	 * @since 2.0
	 * @var string
	 */
	private $capability;

	/**
	 * Constructor.
	 *
	 * @since 2.0
	 */
	public function __construct() {
		$this->slug       = $this->slug();
		$this->title      = $this->title();
		$this->capability = $this->capability();
	}

	/**
	 * Gets the admin page slug.
	 *
	 * @since 2.0
	 *
	 * @return string Admin page slug.
	 */
	final public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * Gets the admin page title.
	 *
	 * @since 2.0
	 *
	 * @return string Admin page title.
	 */
	final public function get_title(): string {
		return $this->title;
	}

	/**
	 * Gets the admin page's required capability.
	 *
	 * @since 2.0
	 *
	 * @return string Admin page capability.
	 */
	final public function get_capability(): string {
		return $this->capability;
	}

	/**
	 * Registers the Assets for the page.
	 *
	 * @param string $hook Page slug passed by wp.
	 * @since 2.0
	 */
	abstract public function register_assets( $hook ): void;

	/**
	 * Register the page in WordPress admin menu.
	 *
	 * @since 2.0
	 */
	abstract public function add_page(): void;

	/**
	 * Renders the admin page.
	 *
	 * @since 2.0
	 */
	abstract public function render(): void;

	/**
	 * Returns the admin page slug.
	 *
	 * @since 2.0
	 *
	 * @return string Admin page slug.
	 */
	abstract protected function slug(): string;

	/**
	 * Returns the admin page title.
	 *
	 * @since 2.0
	 *
	 * @return string Admin page title.
	 */
	abstract protected function title(): string;

	/**
	 * Returns the admin page's required capability.
	 *
	 * @since 2.0
	 *
	 * @return string Admin page capability.
	 */
	abstract protected function capability(): string;
}
