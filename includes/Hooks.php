<?php
/**
 * Class JawadMalik\Woosupercharge\Hooks
 *
 * @since 2.0
 * @package woosupercharge
 */

namespace JawadMalik\Woosupercharge;

use JawadMalik\Woosupercharge\Admin\Settings_Page;

/**
 * Class responsilbe for adding general hooks of the plugin.
 *
 * @since 2.0
 */
class Hooks {

	/**
	 * Main plugin object.
	 *
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * Settings page.
	 *
	 * @var SettingsPage
	 */
	protected $settings_page;

	/**
	 * Constructor.
	 *
	 * @since 2.0
	 *
	 * @param Woosupercharge $plugin Main plugin object.
	 */
	public function __construct( Woosupercharge $plugin ) {
		$this->plugin = $plugin;

		// Initialze the main setting page.
		$this->settings_page = new Settings_Page( $plugin );
	}

	/**
	 * Register all hooks
	 */
	public function add_hooks(): void {
		add_action( 'plugins_loaded', array( $this->plugin, 'plugin_loaded' ) );
		add_action( 'admin_menu', array( $this->settings_page, 'add_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this->settings_page, 'register_assets' ) );
		add_action( 'admin_init', array( $this->plugin->get_settings(), 'do_settings' ) );
	}

	/**
	 * Remove Hooks
	 */
	public function remove_hooks(): void {
		remove_action( 'plugins_loaded', array( $this->plugin, 'plugin_loaded' ) );
		remove_action( 'admin_menu', array( $this->settings_page, 'add_page' ) );
		remove_action( 'admin_enqueue_scripts', array( $this->settings_page, 'register_assets' ) );
		remove_action( 'admin_init', array( $this->plugin->get_settings(), 'do_settings' ) );
	}
}
