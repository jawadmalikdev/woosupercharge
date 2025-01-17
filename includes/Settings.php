<?php
/**
 * Class JawadMalik\Woosupercharge\Settings.
 *
 * @since 2.0
 *
 * @package woosupercharge
 */

namespace JawadMalik\Woosupercharge;

use JawadMalik\Woosupercharge\Helpers;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Main plugin settings class.
 *
 * @since 2.0
 */
class Settings {

	const OPTION_GROUP = 'woosupercharge';

	const OPTION_NAME = 'woosupercharge-settings';

	/**
	 * Default values for settings.
	 *
	 * @var array<string,array> $default_settings.
	 */
	protected $default_settings = array(
		'cart_general_settings'            => array(
			'layout'            => 'list',
			'position'          => 'bottom',
			'popup_close_after' => '5',
		),
		'cart_display_conditions_settings' => array(
			'display_conditions' => array( 'all' ),
		),
	);

	/**
	 * Settings saved in the database
	 * if not then their corresponding
	 * default value.
	 *
	 * @var array<mixed> $settings.
	 */
	protected $settings;

	/**
	 * Constructor sets the plugin settings.
	 *
	 * @since 2.0
	 */
	public function __construct() {
		// Get plugin settings if found in db otherwise use default settings.
		$settings = get_option( 'woosupercharge-settings' );
		if ( ! is_array( $settings ) ) {
			$this->settings = $this->default_settings;
			update_option( self::OPTION_NAME, $this->settings );
		} else {
			$this->settings = array_replace_recursive( $this->default_settings, $settings );
		}
	}

	/**
	 * Return the saved plugin settings.
	 *
	 * @since 2.0
	 */
	public function get_plugin_settings(): array {
		return $this->settings;
	}

	/**
	 * Setting section to register.
	 *
	 * @since 2.0
	 */
	public function get_settings_sections(): array {
		$sections = array(
			array(
				'id'       => 'cart_general_settings',
				'title'    => __( 'WooSupercharge General Settings', 'woosupercharge' ),
				'callback' => array( $this, 'cart_general_settings_callback' ),
				'page'     => 'woosupercharge-settings',
			),
			array(
				'id'       => 'cart_display_conditions_settings',
				'title'    => __( 'WooSupercharge Display Conditions Settings', 'woosupercharge' ),
				'callback' => array( $this, 'display_conditions_settings_callback' ),
				'page'     => 'woosupercharge-settings',
			),
		);

		return $sections;
	}

	/**
	 * Callback function for general settings section.
	 */
	public function cart_general_settings_callback(): void {
		printf(
			esc_html__(
				'This section controls the General Settings for the popup.',
				'woosupercharge'
			)
		);
	}

	/**
	 * Callback function for display settings section.
	 */
	public function display_conditions_settings_callback(): void {
		printf(
			esc_html__(
				'This section controls the Display Settings for the popup. 
				Add the pages where you want the popup to appear. If left empty the default condition is all pages.',
				'woosupercharge'
			)
		);
	}

	/**
	 * Settings options to register.
	 *
	 * @since 2.0
	 */
	public function get_settings_fields(): array {
		$settings_fields = array(
			'cart_general_settings'            => array(
				'layout'            => array(
					'label' => __( 'Layout', 'woosupercharge' ),
					'type'  => 'layout_select',
				),
				'position'          => array(
					'label' => __( 'POSITION', 'woosupercharge' ),
					'type'  => 'position_select',
				),
				'popup_close_after' => array(
					'label' => __( 'CLOSE AFTER (SECONDS)', 'woosupercharge' ),
					'type'  => 'slider',
				),
			),
			'cart_display_conditions_settings' => array(
				'display_conditions' => array(
					'label'   => __( 'Display Conditions', 'woosupercharge' ),
					'type'    => 'conditions_repeater',
					'options' => array(
						'all'                        => 'All pages',
						'archive'                    => 'Shop Archive',
						'categories-archive'         => 'Shop Archive Categories (product categories)',
						'tags-archive'               => 'Shop Archive Tags (product tags)',
						'product-attributes-archive' => 'Shop Archive Product Attributes',
						'single'                     => 'Single Products',
					),
				),
			),
		);

		return $settings_fields;
	}

	/**
	 * Add settings sections.
	 */
	public function add_settings_sections(): void {
		$settings_sections = $this->get_settings_sections();
		// Adds the settings sections.
		foreach ( $settings_sections as $section ) {
			add_settings_section( $section['id'], $section['title'], $section['callback'], $section['page'] );
		}
	}

	/**
	 * Add setting fields.
	 */
	public function add_settings_fields(): void {
		$settings_fields = $this->get_settings_fields();
		// Adds the settings fields.
		foreach ( $settings_fields as $section => $setting ) {

			foreach ( $setting as $name => $field ) {
				$args     = array(
					'name'        => $name,
					'label'       => $field['label'],
					'section'     => $section,
					'options'     => isset( $field['options'] ) ? $field['options'] : '',
					'value'       => isset( $this->settings[ $section ][ $name ] ) ? $this->settings[ $section ][ $name ] : false,
					'type'        => $field['type'],
					'option_name' => self::OPTION_NAME,
				);
				$callback = array( Helpers::class, 'callback_' . $field['type'] );
				if ( is_callable( $callback ) ) {
					add_settings_field( $name, $field['label'], $callback, 'woosupercharge-settings', $section, $args );
				}
			}
		}
	}

	/**
	 * Register the plugin settings and add section and settings fields.
	 *
	 * @uses "admin_init" action
	 */
	public function do_settings(): void {

		register_setting(
			self::OPTION_GROUP,
			self::OPTION_NAME,
			$this->get_settings_fields()
		);

		$this->add_settings_sections();
		$this->add_settings_fields();
	}
}
