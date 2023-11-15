<?php
/**
 * Plugin Name:       WooSupercharge
 * Description:       WooSupercharge is a powerful WooCommerce plugin that enhances your online store with advanced display features. It's designed to provide a seamless and dynamic user experience by incorporating a add to cart notification popup and a custom Gutenberg block for showcasing the latest products.
 * Requires at least: 5.6
 * Requires PHP:      7.0
 * Version:           1.0
 * Author:            Jawad Malik
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       woosupercharge
 *
 * @package           woosupercharge
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class.
 *
 * @since 1.0
 *
 * @package woosupercharge
 * @author  Jawad Malik
 * @access public
 */
final class WooSupercharge {


	/**
	 * Holds the class object.
	 *
	 * @since 1.0
	 * @access public
	 * @var object Instance of instantiated WooSupercharge class.
	 */
	public static $instance;

	/**
	 * Holds the settings object.
	 *
	 * @since 1.0
	 * @access public
	 * @var object Instance of plugin instantiated settings class.
	 */
	public $settings_obj;

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since 1.0
	 * @access public
	 * @var string $version Plugin version.
	 */
	public $version = '1.0';

	/**
	 * The name of the plugin.
	 *
	 * @since 1.0
	 * @access public
	 * @var string $plugin_name Plugin name.
	 */
	public $plugin_name = 'WooSupercharge';

	/**
	 * Unique plugin slug identifier.
	 *
	 * @since 1.0
	 * @access public
	 * @var string $plugin_slug Plugin slug.
	 */
	public $plugin_slug = 'woosupercharge';

	/**
	 * Plugin file.
	 *
	 * @since 1.0
	 * @access public
	 * @var string $file PHP File constant for main file.
	 */
	public $file;

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @access public
	 * @return object The WooSupercharge object.
	 * @since 1.0
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WooSupercharge();
		}

		return self::$instance;
	}

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0
	 * @access public
	 */
	public function __construct() {

		$this->file = __FILE__;

		// Defining our plugin constants.
		$this->define_globals();

		require_once WOOSUPERCHARGE_PLUGIN_DIR . '/woosupercharge-autoload.php';

		$this->settings_obj = new WooSupercharge_Settings();

		if( class_exists('WooCommerce') ) {
			$this->cart = new WooSupercharge_Cart();
		}

		$this->hooks();

		// Do the compatiblity check and show admin notices if applicable.
		( WooSupercharge_Compatibility::get_instance() )->maybe_display_notice();

		// If anyone want to do run anything at this point of time where most our plugin is loaded.
		do_action( 'woosupercharge_loaded' );
	}

	/**
	 * Adds actions and filters for settins and menu and js.
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'callback_register_settings' ) );
		add_action( 'admin_menu', array( $this, 'woosupercharge_menu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_script' ) );
	}

	/**
	 * Adds our js and css on our settings page.
	 */
	public function enqueue_admin_script( $hook ) {

		// Only loads our assets on our settings page.
		if ( 'toplevel_page_woosupercharge-settings' !== $hook ) {
			return;
		}

		wp_enqueue_style( 'woosupercharge_settings_page_css', WOOSUPERCHARGE_PLUGIN_URL . 'assets/css/settings.css', false, $this->version );
		wp_enqueue_script( 'woosupercharge_settings_page_js', WOOSUPERCHARGE_PLUGIN_URL . 'assets/js/settings.min.js', array( 'jquery' ), $this->version );

		/**
		 * our jQuery script on settings page need information
		 * about display conditions this is provided here.
		 */
		$available_conditions = $this->settings_obj->settings_fields['woosupercharge-display-conditions-settings'][0];
		wp_add_inline_script( 'woosupercharge_settings_page_js', ' const availableConditions = ' . wp_json_encode( $available_conditions['options'] ) . ' ' );

	}


	/**
	 * Do the plugin settings registration using the settings class object.
	 */
	public function callback_register_settings() {

		$settings_sections = $this->settings_obj->settings_sections;
		$settings_fields   = $this->settings_obj->settings_fields;

		$this->settings_obj->register_settings( $settings_sections );

		$this->settings_obj->add_settings_sections( $settings_sections );
		$this->settings_obj->add_settings_fields( $settings_fields );
	}

	/**
	 * Adds our plugin settings page in menu.
	 */
	public function woosupercharge_menu_page() {
		$menu_slug = 'woosupercharge-settings';
		add_menu_page( 'WooSupercharge Settings', 'WooSupercharge', 'manage_options', $menu_slug, array( $this, 'callback_menu_page' ), '', 6 );

	}

	/**
	 * Renders our settings and sections.
	 */
	public function callback_menu_page() {

		ob_start();

		settings_errors();
		?>

	   <form action="options.php" method="POST">
		   <?php settings_fields( 'woosupercharge-settings' ); ?>
		   <?php do_settings_sections( 'woosupercharge-settings' ); ?>
		   <?php submit_button(); ?>
	   </form>
		<?php
		echo ob_get_clean();

	}

	/**
	 * Throw error on object clone
	 *
	 * we don't want the object to be cloned.
	 *
	 * @return void
	 * @since 1.0
	 * @access public
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Not allowed to clone!', 'woosupercharge' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * Attempting to wakeup an WooSupercharge instance will throw a doing it wrong notice.
	 *
	 * @return void
	 * @since 1.0
	 * @access public
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'unserialization is not allowed!', 'woosupercharge' ), '1.0' );
	}

	/**
	 * Define woosupercharge constants.
	 *
	 * This function defines all of the woosupercharge PHP constants.
	 *
	 * @return void
	 * @since 1.0
	 * @access public
	 */
	public function define_globals() {
		if ( ! defined( 'WOOSUPERCHARGE_VERSION' ) ) {
			define( 'WOOSUPERCHARGE_VERSION', $this->version );
		}

		if ( ! defined( 'WOOSUPERCHARGE_PLUGIN_NAME' ) ) {
			define( 'WOOSUPERCHARGE_PLUGIN_NAME', $this->plugin_name );
		}

		if ( ! defined( 'WOOSUPERCHARGE_PLUGIN_SLUG' ) ) {
			define( 'WOOSUPERCHARGE_PLUGIN_SLUG', $this->plugin_slug );
		}

		if ( ! defined( 'WOOSUPERCHARGE_PLUGIN_FILE' ) ) {
			define( 'WOOSUPERCHARGE_PLUGIN_FILE', $this->file );
		}

		if ( ! defined( 'WOOSUPERCHARGE_PLUGIN_DIR' ) ) {
			define( 'WOOSUPERCHARGE_PLUGIN_DIR', plugin_dir_path( $this->file ) );
		}

		if ( ! defined( 'WOOSUPERCHARGE_PLUGIN_URL' ) ) {
			define( 'WOOSUPERCHARGE_PLUGIN_URL', plugin_dir_url( $this->file ) );
		}
	}

	/**
	 * Loads the plugin textdomain for translation.
	 *
	 * @access public
	 * @return void
	 * @since 1.0
	 */
	public function load_plugin_textdomain(){
		load_plugin_textdomain( 'woosupercharge', false, WOOSUPERCHARGE_PLUGIN_DIR . '/languages/' );
	}
}

/**
 * Fired when the plugin is activated.
 *
 * @access public
 *
 * @since 1.0
 */
function woosupercharge_activation_hook() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-compatibility.php';
	$compatibility = WooSupercharge_Compatibility::get_instance();
	$compatibility->maybe_deactivate_plugin( plugin_basename( __FILE__ ) );
}

register_activation_hook( __FILE__, 'woosupercharge_activation_hook' );

/**
 * Fired when the plugin is uninstalled. We will delete our database settings when user
 * deletes our plugin
 *
 * @access public
 * @return    void
 * @since 1.0
 */
function woosupercharge_uninstall_hook() {
	delete_option( 'woosupercharge-general-settings' );
	delete_option( 'woosupercharge-display-conditions-settings' );
	delete_option( 'woosupercharge_last_added_cart_key' );
	wp_cache_flush();
}

register_uninstall_hook( __FILE__, 'woosupercharge_uninstall_hook' );


if ( ! function_exists( 'WooSupercharge' ) ) {
	/**
	 * Instantiating the plugin core
	 * we are using singleton for our main core file.
	 */
	function WooSupercharge() {
		return WooSupercharge::get_instance();
	}

	add_action( 'plugins_loaded', 'WooSupercharge' );
}
