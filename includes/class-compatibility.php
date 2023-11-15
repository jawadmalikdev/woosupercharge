<?php

if ( ! class_exists( 'WooSupercharge_Compatibility' ) ) {
	/**
	 * Check PHP and WP compatibility
	 *
	 * @since 1.0
	 */
	class WooSupercharge_Compatibility {
		/**
		 * Holds singleton instance
		 *
		 * @since 1.0
		 * @var WooSupercharge_Compatibility
		 */
		private static $instance;

		/**
		 * Return Singleton instance
		 *
		 * @return WooSupercharge_Compatibility
		 * @since 1.0
		 */
		public static function get_instance() {
			if ( empty( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		private $min_php_version = '7.0';

		private $min_wp_version = '5.6';

		/**
		 * Private constructor
		 *
		 * @since 1.0
		 */
		private function __construct() {}

		/**
		 * Check to see if PHP version meets the minimum required version
		 *
		 * @return bool
		 * @since 1.0
		 */
		public function is_php_compatible() {
			return version_compare( phpversion(), $this->min_php_version, '>=' );
		}

		/**
		 * Check to see if WP version meets the minimum required version
		 *
		 * @return bool
		 * @since 1.0
		 */
		public function is_wp_compatible() {
			global $wp_version;
            return version_compare( $wp_version, $this->min_wp_version, '>=' );
		}

		/**
		 * Display version notice in admin area if:
		 * Minimum PHP and WP versions are not met		 *
		 * @return void
		 * @since 1.0
		 */
		public function maybe_display_notice() {

			if ( ! $this->is_php_compatible() ) {
				add_action( 'admin_notices', array( $this, 'display_php_notice' ) );
			}

			if ( ! $this->is_wp_compatible() ) {
				add_action( 'admin_notices', array( $this, 'display_wp_notice' ) );
			}
		}

		/**
		 * Deactivate plugin if minimum PHP and WP requirements are not met.
		 *
		 * @param $plugin
		 *
		 * @return void
		 * @since 1.0
		 */
		public function maybe_deactivate_plugin( $plugin ) {

			$url                    = admin_url( 'plugins.php' );
			$compatible_php_version = $this->min_php_version;
			$compatible_wp_version  = $this->min_wp_version;

			if ( $this->is_php_compatible() ) {
				deactivate_plugins( $plugin );
				wp_die(
					sprintf( esc_html__( 'Sorry, but your current version of PHP does not meet the minimum required version %1$s%2$s%3$s or newer to run WooSupercharge properly. For information on how to upgrade your PHP version, contact your web host. %4$sClick here to return to the Dashboard%5$s.', 'wpsupercharge' ),
						'<strong>',
						$compatible_php_versioon, // phpcs:ignore
						'</strong>',
						'<a target="_blank" href="' . esc_url($url) . '">',
						'</a>'
					)
				);
			}

			if ( ! $this->is_wp_compatible() ) {
				deactivate_plugins( $plugin );
				wp_die(
					sprintf(
						esc_html__( 'Sorry, but your WordPress version is not %1$s%2$s%3$s or newer. Please update your WordPress version and then activate WooSupercharge. For help on how to update your WordPress %4$sclick here%5$s.', 'woosupercharge' ),
						'<strong>',
						$compatible_wp_version, // phpcs:ignore
						'</strong>',
						'<a target="_blank" href="' . esc_url($url) . '">',
						'</a>'
					)
				);
			}
		}

		/**
		 * Output a notice if the user has an out of date PHP version installed
		 *
		 * @return void
		 * @since 1.0
		 */
		public function display_php_notice() {

			$compatible_php_version = $this->min_php_version;

			?>
			<div class="error">
				<p>
					<?php
					echo sprintf(
						esc_html__( ' Your current version of PHP does not meet the minimum required version %1$s%2$s%3$s or newer to run WooSupercharge properly. For information on how to upgrade your PHP version, contact your web host.', 'woosupercharge' ),
						'<strong>',
						$compatible_php_version, // phpcs:ignore
						'</strong>'
					)
					?>
				</p>
			</div>
			<?php
		}

		/**
		 * Output a notice if the user has an out of date WP version installed
		 *
		 * @return void
		 * @since 1.0
		 */
		public function display_wp_notice() {

			$compatible_wp_version = $this->min_wp_version;

			?>
			<div class="error">
				<p>
					<?php
					echo sprintf(
						esc_html__( 'Your WordPress version is not %1$s%2$s%3$s or newer. Please update your WordPress version and then activate WooSupercharge.', 'woosupercharge' ),
						'<strong>',
						$compatible_wp_version, // phpcs:ignore
						'</strong>'
					);
					?>
				</p>
			</div>
			<?php
		}
	}
}
