<?php
/**
 * Handles Plugin Uninstallation.
 * 
 * @since 2.0
 * 
 * @package woosupercharge
 */

namespace JawadMalik\Woosupercharge;

// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

delete_option( 'woosupercharge-settings' );
wp_cache_flush();
