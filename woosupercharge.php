<?php
/**
 * Plugin Name:       WooSupercharge
 * Description:       WooSupercharge is a powerful WooCommerce plugin that enhances your online store with advanced display features. It's designed to provide a seamless and dynamic user experience by incorporating a add to cart notification popup.
 * Requires at least: 5.6
 * Requires PHP:      7.4
 * Version:           1.0
 * Author:            Jawad Malik
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       woosupercharge
 *
 * @package           woosupercharge
 */

namespace JawadMalik\Woosupercharge;

use JawadMalik\Woosupercharge\Settings;
use JawadMalik\Woosupercharge\Woosupercharge;
use JawadMalik\Woosupercharge\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WOOSUPERCHARGE_VERSION', '1.0' );
define( 'WOOSUPERCHARGE_CHECK_MINIMUM_PHP', '7.4' );
define( 'WOOSUPERCHARGE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WOOSUPERCHARGE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Check for supported PHP version.
if ( version_compare( phpversion(), WOOSUPERCHARGE_CHECK_MINIMUM_PHP, '<' ) ) {
	add_action( 'admin_notices', __NAMESPACE__ . '\\woosupercharge_check_display_php_version_notice' );
	return;
}

// Check Composer autoloader exists.
if ( ! file_exists( WOOSUPERCHARGE_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	add_action( 'admin_notices', __NAMESPACE__ . '\\woosupercharge_check_display_composer_autoload_notice' );
	return;
}

// include autoloader from composer.
require_once __DIR__ . '/vendor/autoload.php';

// Check Woocommerce is active.
if ( ! Helpers::is_woocommerce_active() ) {
	add_action( 'admin_notices', __NAMESPACE__ . '\\woocommerce_not_active_notice' );
	return;
}

/**
 * Displays admin notice about unmet PHP version requirement.
 *
 * @since 2.0
 */
function woosupercharge_check_display_php_version_notice() {
	echo '<div class="notice notice-error"><p>';
	printf(
		/* translators: 1: required version, 2: currently used version */
		esc_html__( 'Woosupercharge requires at least PHP version %1$s. Your site is currently running on PHP %2$s.', 'woosupercharge' ),
		esc_html( WOOSUPERCHARGE_CHECK_MINIMUM_PHP ),
		esc_html( phpversion() )
	);
	echo '</p></div>';
}

/**
 * Displays admin notice about missing Composer autoload files.
 *
 * @since 2.0
 */
function woosupercharge_check_display_composer_autoload_notice() {
	echo '<div class="notice notice-error"><p>';
	printf(
		/* translators: composer command. */
		esc_html__( 'Your installation of the Woosupercharge plugin is incomplete. Please run %s.', 'woosupercharge' ),
		'<code>composer install</code>'
	);
	echo '</p></div>';
}

/**
 * Displays admin notice about missing Composer autoload files.
 *
 * @since 2.0
 */
function woocommerce_not_active_notice() {
	echo '<div class="notice notice-error"><p>';
	printf(
		esc_html__( 'Woosupercharge is meant to be used with WooCommerce. Plz make sure Woocommerce is active.', 'woosupercharge' ),
	);
	echo '</p></div>';
}

/**
 * Setup plugin.
 *
 * @since 2.0
 */
add_action(
	'woosupercharge',
	function ( Woosupercharge $plugin ) {
		$plugin->setup();
	}
);

/**
 * Start Plugin
 *
 * @since 2.0
 * @param Woosupercharge $plugin
 */
do_action(
	'woosupercharge',
	new Woosupercharge(
		new Settings(),
		WOOSUPERCHARGE_VERSION
	)
);
