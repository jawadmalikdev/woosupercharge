<?php
namespace JawadMalik\Woosupercharge;
use JawadMalik\Woosupercharge\Helpers;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Settings {

	/**
	 * settings sections array
	 *
	 * @var array
	 */
	protected $settings_sections = array();

	/**
	 * Settings fields array
	 *
	 * @var array
	 */
	protected $settings_fields = array();

	/**
	 * Settings saved in the database
	 * if not then their corresponding
	 * default value.
	 *
	 * @var array
	 */
	protected $settings;


	public function __construct() {

		$this->settings = Helpers::get_woosupercharge_settings();

		$this->settings_sections = $this->settings_sections();
		$this->settings_fields   = $this->settings_fields();
	}

	public function settings_sections() {

		$sections = array(
			array(
				'id'       => 'woosupercharge-general-settings',
				'title'    => __( 'WooSupercharge General Settings', 'woosupercharge' ),
				'callback' => array( $this, 'general_settings_callback' ),
				'page'     => 'woosupercharge-settings',
				'args'     => array(),
			),
			array(
				'id'       => 'woosupercharge-display-conditions-settings',
				'title'    => __( 'WooSupercharge Display Conditions Settings', 'woosupercharge' ),
				'callback' => array( $this, 'display_conditions_settings_callback' ),
				'page'     => 'woosupercharge-settings',
				'args'     => array(),
			),
		);

		return $sections;
	}

	public function general_settings_callback( $args ) {
		printf(
			'<p>%s</p>',
			esc_html__(
				'This section controls the General Settings for the popup.',
				'woosupercharge'
			)
		);
	}
	public function display_conditions_settings_callback() {
		printf(
			'<p>%s</p>',
			__(
				'This section controls the Display Settings for the popup.<br/> Add the pages where you want the popup to appear.<br/> If left empty the default condition is all pages.',
				'woosupercharge'
			)
		);
	}

	/**
	 * Returns all the settings fields
	 *
	 * @return array settings fields
	 */
	public function settings_fields() {

		$settings_fields = array(
			'woosupercharge-general-settings'            => array(
				array(
					'name'    => 'layout',
					'label'   => __( 'Layout', 'woosupercharge' ),
					'type'    => 'layout_select',
					'default' => 'list',
				),
				array(
					'name'    => 'position',
					'label'   => __( 'POSITION', 'woosupercharge' ),
					'type'    => 'position_select',
					'default' => 'bottom',
				),
				array(
					'name'    => 'popup_close_after',
					'label'   => __( 'CLOSE AFTER (SECONDS)', 'woosupercharge' ),
					'type'    => 'slider',
					'default' => '5',
				),
			),
			'woosupercharge-display-conditions-settings' => array(
				array(
					'name'    => 'display_conditions',
					'label'   => __( 'Display Conditions', 'woosupercharge' ),
					'type'    => 'conditions_repeater',
					'default' => array( 'all' ),
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

	public function get_settings_sections() {
		return $this->settings_sections;
	}

	public function get_settings_fields() {
		return $this->settings_fields;
	}

	public function add_settings_sections() {
		// Adds the settings sections
		foreach ( $this->settings_sections as $section ) {
			add_settings_section( $section['id'], $section['title'], $section['callback'], $section['page'], $section['args'] );
		}
	}

	public function add_settings_fields() {
		// Adds the settings fields
		foreach ( $this->settings_fields as $section => $setting ) {

			foreach ( $setting as $field ) {
				$name     = $field['name'];
				$args     = array(
					'name'    => $field['name'],
					'label'   => $field['label'],
					'section' => $section,
					'options' => isset( $field['options'] ) ? $field['options'] : '',
					'value'   => $name === 'display_conditions' ? $this->settings['display_settings'][ $name ] : $this->settings['general_settings'][ $name ],
					'type'    => $field['type'],
				);
				$callback = array( Helpers::class, 'callback_' . $field['type'] );
				add_settings_field( $field['name'], $field['label'], $callback, 'woosupercharge-settings', $section, $args );
			}
		}
	}

	public function do_settings() {
		// creates our settings in the options table
		foreach ( $this->settings_sections as $settings_section ) {
			register_setting( 'woosupercharge-settings', $settings_section['id'] );
		}
		$this->add_settings_sections();
		$this->add_settings_fields();
	}
}
