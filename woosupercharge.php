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

namespace JawadMalik\Woosupercharge;

use JawadMalik\Woosupercharge\Settings;
use JawadMalik\Woosupercharge\Woosupercharge;

if ( !defined('ABSPATH') ) {
	exit;
}


define( 'WOOSUPERCHARGE_VERSION', '1.0' );

define( 'WOOSUPERCHARGE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

define( 'WOOSUPERCHARGE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// include autoloader from composer.
require_once __DIR__ . '/vendor/autoload.php';

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
 * @since 1.0.0
 * @param Woosupercharge $plugin
 */
do_action(
	'woosupercharge',
	new Woosupercharge(
		new Settings(),
		WOOSUPERCHARGE_VERSION
	)
);