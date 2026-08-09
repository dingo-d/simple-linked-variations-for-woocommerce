<?php
/**
 * Simple Linked Variations for WooCommerce
 *
 * @package           Simplewlv
 * @since             1.1.0 Separate functionality to classes, add autoloader.
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Simple Linked Variations for WooCommerce
 * Plugin URI:        http://madebydenis.com/simple-linked-variations-for-woocommerce/
 * Description:       An add-on plugin for WooCommerce which allows variations to be linked together, and will then toggle dropdowns on the front end based on the links made
 * Version:           1.1.0
 * Author:            Denis Zoljom
 * Author URI:        https://madebydenis.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       simplewlv
 * Domain Path:       /languages
 */

namespace Simplewlv;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

/**
 * Include the autoloader so we can dynamically include the rest of the classes.
 *
 * @since 1.1.0
 */
require __DIR__ . '/vendor/autoload.php';

/**
 * Checks if the WooCommerce plugin is activated
 *
 * If the WooCommerce plugin is not active, then don't allow the
 * activation of this plugin.
 *
 * @since 1.0.0
 */
function activate() {

  if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
    include_once ABSPATH . '/wp-admin/includes/plugin.php';
  }

  if ( current_user_can( 'activate_plugins' ) && ! class_exists( 'WooCommerce' ) ) {
    // Deactivate the plugin.
    deactivate_plugins( plugin_basename( __FILE__ ) );
    // Throw an error in the WordPress admin console.
    $error_message = '<p style="font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen-Sans,Ubuntu,Cantarell,\'Helvetica Neue\',sans-serif;font-size: 13px;line-height: 1.5;color:#444;">' . esc_html__( 'This plugin requires ', 'simplewlv' ) . '<a href="' . esc_url( 'https://wordpress.org/plugins/simplewlv/' ) . '">WooCommerce</a>' . esc_html__( ' plugin to be active.', 'simplewlv' ) . '</p>';
    die( $error_message ); // WPCS: XSS ok.
  }
}

register_activation_hook( __FILE__, 'activate' );

new Admin\Main();
