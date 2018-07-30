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
   * Plugin version
   *
   * @var string
   */
  private $plugin_version;

  /**
   * Class constructor
   *
   * @param string $plugin_name    Name of the plugin.
   * @param string $plugin_version Version of the plugin.
   *
   * @since 1.1.0
   */
  public function __construct( $plugin_name, $plugin_version ) {
    $this->plugin_name    = $plugin_name;
    $this->plugin_version = $plugin_version;
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
    if ( isset( $_POST['simplewlv_linked_attributes_nonce'] ) && wp_verify_nonce( wp_unslash( sanitize_key( $_POST['simplewlv_linked_attributes_nonce'] ) ), 'simplewlv_linked_attributes_nonce_action' ) ) { // Input var okay.
      $checked = ( isset( $_POST['linked_attribute_value'] ) && ! empty( $_POST['linked_attribute_value'] ) ) ? $_POST['linked_attribute_value'] : ''; // Input var okay.
      update_post_meta( $post_id, 'linked_attribute_value', $checked );

      $selected = ( isset( $_POST['linked_attribute'] ) && ! empty( $_POST['linked_attribute'] ) ) ? $_POST['linked_attribute'] : ''; // Input var okay.
      update_post_meta( $post_id, 'linked_attribute', esc_attr( $selected ) );
    }
  }

  /**
   * Register the JavaScript  for the front end area.
   *
   * @since  1.1.0 Moved to a new class, renamed.
   * @since  1.0.0
   */
  public function enqueue_frontend_styles() {
    if ( is_product() ) {
      wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/css/simplewlv.css', array(), $this->plugin_version, 'all' );
    }

  }

  /**
   * Register the stylesheets for the front end area.
   *
   * @since  1.1.0 Moved to a new class, renamed.
   * @since  1.0.0
   */
  public function enqueue_frontend_scripts() {
    if ( is_product() ) {
      wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/simplewlv.js', array( 'jquery' ), $this->plugin_version, false );
    }

  }

  /**
   * Register the stylesheets for the front end area.
   *
   * @since  1.1.0 Moved to a new class, renamed.
   * @since  1.0.0
   */
  public function enqueue_backend_scripts() {
    wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/css/simplewlv_admin.css', array(), $this->plugin_version, 'all' );
  }
}
