<?php
/**
 * Class holding the core related functionality
 *
 * Handles all the enquing and saving actions.
 *
 * @package Simplewlv\Admin
 * @since 1.1.0
 */

namespace Simplewlv\Admin;

use Simplewlv\Admin\Helper;

/**
 * Holds all the core related functionality.
 */
class Admin {
  /**
   * Plugin name.
   *
   * @var string
   */
  private $plugin_name;

  /**
   * Class constructor
   *
   * @param string $plugin_name    Name of the plugin.
   *
   * @since 1.1.0
   */
  public function __construct( $plugin_name ) {
    $this->plugin_name = $plugin_name;
  }

  /**
   * Save new fields for variations
   *
   * @param integer $post_id Current product id.
   *
   * @since 1.1.0 Moved to a new class, renamed and add sanitization.
   * @since 1.0.0
   */
  public function save_variation_settings_fields( $post_id ) {

    if ( ! isset( $_POST['simplewlv_linked_attributes_nonce'] ) && ! wp_verify_nonce( wp_unslash( sanitize_key( $_POST['simplewlv_linked_attributes_nonce'] ) ), 'simplewlv_linked_attributes_nonce_action' ) ) {
      return;
    }

    $checked = ( isset( $_POST['linked_attribute_value'] ) && ! empty( $_POST['linked_attribute_value'] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['linked_attribute_value'] ) ) : '';

    update_post_meta( $post_id, 'linked_attribute_value', $checked );

    $selected = ( isset( $_POST['linked_attribute'] ) && ! empty( $_POST['linked_attribute'] ) ) ? sanitize_text_field( wp_unslash( $_POST['linked_attribute'] ) ) : '';

    update_post_meta( $post_id, 'linked_attribute', esc_attr( $selected ) );
  }

  /**
   * Register the scripts and styles for the front end area.
   *
   * @since  1.1.0 Moved to a new class, renamed.
   * @since  1.0.0
   */
  public function enqueue_frontend_scripts() {
    if ( is_product() ) {
      $front_style  = Helper::get_manifest_assets_data( 'application.css' );
      $front_script = Helper::get_manifest_assets_data( 'application.js' );
      $vendors      = Helper::get_manifest_assets_data( 'vendors.js' );
      // phpcs:disable
      wp_enqueue_style( $this->plugin_name, $front_style, array(), false, 'all' );
      wp_enqueue_script( $this->plugin_name, $front_script, array(), false, false );
      wp_enqueue_script( $this->plugin_name, $vendors, array(), false, false );
      // phpcs:enable
    }

  }

  /**
   * Register the stylesheets for the front end area.
   *
   * @since  1.1.0 Moved to a new class, renamed.
   * @since  1.0.0
   */
  public function enqueue_backend_scripts() {
    if ( ! function_exists( 'get_current_screen' ) ) {
      return;
    }

    $current_screen = get_current_screen();

    if ( $current_screen->post_type !== 'product' ) {
      return;
    }

    $admin_style  = Helper::get_manifest_assets_data( 'applicationAdmin.css' );
    $admin_script = Helper::get_manifest_assets_data( 'applicationAdmin.js' );
    $vendors      = Helper::get_manifest_assets_data( 'vendors.js' );

    // phpcs:disable
    wp_enqueue_style( $this->plugin_name, $admin_style, array(), false, 'all' );
    wp_enqueue_script( $this->plugin_name, $admin_script, array(), false, false );
    wp_enqueue_script( $this->plugin_name, $vendors, array(), false, false );
    // phpcs:enable
  }
}
