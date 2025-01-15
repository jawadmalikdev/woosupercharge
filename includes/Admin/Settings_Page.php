<?php
/**
 * Class JawadMalik\Woosupercharge\Admin\Settings_Page
 *
 * @since 2.0
 *
 * @package woosupercharge
 */

namespace JawadMalik\Woosupercharge\Admin;

use JawadMalik\Woosupercharge\Admin\Abstract_Admin_Page;
use JawadMalik\Woosupercharge\Woosupercharge;

/**
 * Class representing the plugin's admin settings page.
 *
 * @since 2.0
 */
class Settings_Page extends Abstract_Admin_Page {


	// Identifier for the WooSupercharge cart settings screen.
	const SCREEN = 'woosupercharge-cart-settings';

	/**
	 * Instance of the main plugin class.
	 *
	 * Provides access to shared plugin functionality and data across the plugin's scope.
	 *
	 * @var Woosupercharge $plugin
	 */
	protected $plugin;

	/**
	 * Constructor.
	 *
	 * @since 2.0
	 *
	 * @param Woosupercharge $plugin
	 */
	public function __construct( Woosupercharge $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Register assets
	 *
	 * @since 2.0
	 *
	 * @uses "admin_enqueue_scripts" action
	 */
	public function register_assets( $hook ): void {
		// Only loads our assets on our settings page.
		if ( 'toplevel_page_' . self::SCREEN !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'woosupercharge_settings_page_css',
			WOOSUPERCHARGE_PLUGIN_URL . 'assets/css/settings.css',
			false,
			$this->plugin->version
		);

		wp_enqueue_script(
			'woosupercharge_settings_page_js',
			WOOSUPERCHARGE_PLUGIN_URL . 'assets/js/settings.min.js',
			array( 'jquery' ),
			$this->plugin->version
		);

		/**
		 * our jQuery script on settings page need information
		 * about display conditions this is provided here.
		 */
		$settings_fields      = $this->plugin->getSettings()->get_settings_fields();
		$available_conditions = $settings_fields['woosupercharge-display-conditions-settings']['display_conditions'];
		wp_add_inline_script(
			'woosupercharge_settings_page_js',
			' const availableConditions = ' . wp_json_encode( $available_conditions['options'] ) . ' '
		);
	}

	/**
	 * Adds the admin page.
	 *
	 * @since 2.0
	 *
	 * @return string Admin page generated suffix.
	 */
	public function add_page(): string {
		$suffix = add_menu_page(
			$this->title(),
			'WooSupercharge',
			$this->capability(),
			self::SCREEN,
			array(
				$this,
				'render',
			),
			'',
			6
		);

		return $suffix;
	}

	/**
	 * Renders the admin page.
	 *
	 * @since 2.0
	 */
	public function render(): void {
		settings_errors();
		?>
		<form action="options.php" method="POST">
			<?php settings_fields( 'woosupercharge' ); ?>
			<?php do_settings_sections( 'woosupercharge-settings' ); ?>
			<?php submit_button(); ?>
		</form>
		<?php
	}

	/**
	 * Returns the admin page slug.
	 *
	 * @since 2.0
	 *
	 * @return string Admin page slug.
	 */
	protected function slug(): string {
		return 'woosupercharge-settings';
	}

	/**
	 * Returns the admin page title.
	 *
	 * @since 2.0
	 *
	 * @return string Admin page title.
	 */
	protected function title(): string {
		return __( 'WooSupercharge Settings', 'woosupercharge' );
	}

	/**
	 * Returns the admin page's required capability.
	 *
	 * @since 2.0
	 *
	 * @return string Admin page capability.
	 */
	protected function capability(): string {
		return 'manage_options';
	}
}