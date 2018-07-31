<?php
/**
 * Class holding the hooks and main plugin functionality
 *
 * @package Simplewlv\Admin
 * @since 1.1.0
 */

namespace Simplewlv\Admin;

/**
 * Main plugin class
 */
class Main {
  /**
   * Plugin name constant
   */
  const PLUGIN_NAME = 'simplewlv';

  /**
   * Plugin version constant
   */
  const PLUGIN_VERSION = '1.1.0';

  /**
   * Define the core functionality of the plugin.
   *
   * Set the plugin name and the plugin version that can be used throughout the plugin.
   * Load the dependencies, define the locale, and set the hooks for the admin area and
   * the public-facing side of the site.
   *
   * @since 1.1.0 Moved to a separate class.
   * @since 1.0.0
   */
  public function __construct() {
    $this->woo_functionality = new Woo_Functionality();
    $this->admin             = new Admin( self::PLUGIN_NAME );

    $this->set_locale();
    $this->set_assets_manifest_data();
    $this->define_admin_hooks();
    $this->define_public_hooks();
  }

  /**
   * Define the locale for this plugin for internationalization.
   *
   * Sets the domain and to register the hook with WordPress.
   *
   * @since   1.1.0 Moved to a separate class.
   * @since   1.0.0
   * @access  private
   */
  private function set_locale() {
    add_action( 'plugins_loaded', array( $this, 'plugin_textdomain' ) );
  }

  /**
   * Register all of the hooks related to the admin area functionality
   * of the plugin.
   *
   * @since   1.1.0 Moved to a separate class add woo hooks.
   * @since   1.0.0
   * @access  private
   */
  private function define_admin_hooks() {
    add_action( 'save_post', array( $this->admin, 'save_variation_settings_fields' ), 10, 2 );
    add_action( 'admin_enqueue_scripts', array( $this->admin, 'enqueue_backend_scripts' ) );

    add_filter( 'woocommerce_product_data_tabs', array( $this->woo_functionality, 'add_product_data_tabs' ) );
    add_action( 'woocommerce_product_data_panels', array( $this->woo_functionality, 'product_data_fields' ) );
  }

  /**
   * Register all of the hooks related to the public-facing functionality
   * of the plugin.
   *
   * @since   1.1.0 Moved to a separate class.
   * @since   1.0.0
   * @access  private
   */
  private function define_public_hooks() {
    add_action( 'wp_enqueue_scripts', array( $this->admin, 'enqueue_frontend_scripts' ) );

    add_action( 'woocommerce_before_add_to_cart_button', array( $this->woo_functionality, 'frontend_hidden_meta' ) );
  }

  /**
   * Load the plugin text domain for translation.
   *
   * @since   1.1.0 Moved to a separate class and changed method name.
   * @since   1.0.0
   */
  public function plugin_textdomain() {
    load_plugin_textdomain(
      'simplewlv',
      false,
      plugin_dir_path( __DIR__ ) . '/languages/'
    );
  }

  /**
   * Define global variable to save memory when parsing manifest on every load.
   *
   * @since 1.1.0
   */
  public function set_assets_manifest_data() {
    $response = wp_remote_get( rtrim( plugin_dir_url( __DIR__ ), '/' ) . '/assets/public/manifest.json' );
    if ( ! is_array( $response ) && is_wp_error( $response ) ) {
      return;
    }

    if ( ! isset( $response['body'] ) && $response['body'] === '' ) {
      return;
    }

    define( 'SIMPLEWLV_ASSETS_MANIFEST', $response['body'] );
  }
}
