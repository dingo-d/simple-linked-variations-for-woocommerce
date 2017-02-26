<?php
/**
 * Simple Linked Variations for WooCommerce
 *
 * @since             1.0.0
 * @package           simplewlv
 *
 * @wordpress-plugin
 * Plugin Name:       Simple Linked Variations for WooCommerce
 * Plugin URI:        http://madebydenis.com/simple-linked-variations-for-woocommerce/
 * Description:       An add-on plugin for WooCommerce which allows variations to be linked together, and will then toggle dropdowns on the front end based on the links made
 * Version:           1.0.0
 * Author:            Denis Zoljom
 * Author URI:        https://madebydenis.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       simplewlv
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Checks if the WooCommerce plugin is activated
 *
 * If the WooCommerce plugin is not active, then don't allow the
 * activation of this plugin.
 *
 * @since 1.0.0
 */
function simplewlv_activate() {

	if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
		include_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	}

	if ( current_user_can( 'activate_plugins' ) && ! class_exists( 'WooCommerce' ) ) {
		// Deactivate the plugin.
		deactivate_plugins( plugin_basename( __FILE__ ) );
		// Throw an error in the WordPress admin console.
		$error_message = '<p style="font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen-Sans,Ubuntu,Cantarell,\'Helvetica Neue\',sans-serif;font-size: 13px;line-height: 1.5;color:#444;">' . esc_html__( 'This plugin requires ', 'simplewlv' ) . '<a href="' . esc_url( 'https://wordpress.org/plugins/simplewlv/' ) . '">WooCommerce</a>' . esc_html__( ' plugin to be active.', 'simplewlv' ) . '</p>';
		die( $error_message ); // WPCS: XSS ok.
	}
}

register_activation_hook( __FILE__, 'simplewlv_activate' );

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    simplewlv
 * @author     Denis Zoljom <denis.zoljom@gmail.com>
 */
class Simple_Linked_Variations_For_WooCommerce {

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->simplewlv = 'simplewlv';
		$this->version   = '1.0.0';

		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->add_product_data_tabs();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Sets the domain and to register the hook with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		add_action( 'plugins_loaded', array( $this, 'simplewlv_load_plugin_textdomain' ) );
	}

	/**
	 * Add additional product data tabs
	 *
	 * Registers the tab with additional information.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function add_product_data_tabs() {
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'simplewlv_add_product_data_tabs' ) );
		add_action( 'woocommerce_product_data_panels',  array( $this, 'simplewlv_product_data_fields' ) );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function simplewlv_load_plugin_textdomain() {
		load_plugin_textdomain(
			'simplewlv',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		add_action( 'save_post', array( $this, 'simplewlv_save_variation_settings_fields' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'simplewlv_enqueue_backend_scripts' ) );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'simplewlv_enqueue_frontend_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'simplewlv_enqueue_frontend_scripts' ) );
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'simplewlv_frontend_hidden_meta' ) );
	}

	/**
	 * Register additional tab for linking
	 *
	 * Adding additional tab for linked variations.
	 *
	 * @param  array $product_data_tabs Initial product data tab.
	 * @return array Updated product data tab.
	 */
	public function simplewlv_add_product_data_tabs( $product_data_tabs  ) {
		$product_data_tabs['linked-attributes'] = array(
			'label'  => esc_attr__( 'Linked attributes', 'simplewlv' ),
			'target' => 'simplewlv_linked_attributes',
			'class'  => array( 'variations_tab', 'show_if_variable' ),

		);
		return $product_data_tabs;
	}

	/**
	 * Additional product tab fields
	 *
	 * @since 1.0.0
	 */
	public function simplewlv_product_data_fields() {
		global $woocommerce, $post;
		?>
		<div id="simplewlv_linked_attributes" class="panel woocommerce_options_panel">
			<?php
			$product = wc_get_product();
			if ( $product->has_child() ) :
				$available_variations = $product->get_available_variations();
				$all_attributes = $product->get_variation_attributes();
				$attributes = array( '-' => '-' );
				$attribute_values = array();

				foreach ( $all_attributes as $attribute_key => $attribute_value ) {
					$attribute_name = ucfirst( trim( str_replace( 'pa_', '', $attribute_key ) ) );
					$attributes[ $attribute_key ] = $attribute_name;
					foreach ( $attribute_value as $att_value_key => $att_value_value ) {
						$attribute_values[ $attribute_key . ':' . $att_value_value ] = $attribute_name . ' - ' . ucfirst( $att_value_value );
					}
				}
				?>
				<div class="linked_attributes_wrapper">
					<h3><?php esc_html_e( 'Link the variation attributes to certain attribute', 'simplewlv' ); ?></h3>
					<p class="info"><?php esc_html_e( 'The selected linked attribute will be hidden unless the selected attribute values are clicked.', 'simplewlv' ); ?></p>
					<div class="attributes left">
						<h4><?php esc_html_e( 'Attributes', 'simplewlv' ); ?></h4>
						<?php
						woocommerce_wp_select(
							array(
								'id'      => 'linked_attribute',
								'name'    => 'linked_attribute',
								'label'   => esc_html__( 'Linked attribute: ', 'simplewlv' ),
								'value'   => get_post_meta( $product->id, 'linked_attribute', true ),
								'options' => $attributes,
							)
						);
						?>
					</div>
					<div class="attributes right">
						<h4><?php esc_html_e( 'Attribute values', 'simplewlv' ); ?></h4>
						<?php
						$linked_post_meta = get_post_meta( $product->id, 'linked_attribute_value', true );
						print_r($linked_post_meta);
						foreach ( $attribute_values as $att_val_key => $att_val_value ) {
							woocommerce_wp_checkbox(
								array(
									'id'      => 'linked_attribute_value[' . $att_val_key . ']',
									'name'    => 'linked_attribute_value[' . $att_val_key . ']',
									'label'   => $att_val_value . ' ',
									'cbvalue' => ( isset( $linked_post_meta[ $att_val_key ] ) ) ? 'no' : 'yes',
									'value'   => 'no',
								)
							);
						}
						?>
					</div>
					<?php wp_nonce_field( 'simplewlv_linked_attributes_nonce_action', 'simplewlv_linked_attributes_nonce' ); ?>
				</div>
			<?php else : ?>
				<div class="no_variations_notice"><?php esc_html_e( 'Please add variations and update the product.', 'simplewlv' ); ?></div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Save new fields for variations
	 *
	 * @param integer $post_id Current product id.
	 * @since 1.0.0
	 */
	public function simplewlv_save_variation_settings_fields( $post_id ) {
		if ( isset( $_POST['simplewlv_linked_attributes_nonce'] ) && wp_verify_nonce( wp_unslash( sanitize_key( $_POST['simplewlv_linked_attributes_nonce'] ) ), 'simplewlv_linked_attributes_nonce_action' )  ) { // Input var okay.
			$checked = ( isset( $_POST['linked_attribute_value'] ) && ! empty( $_POST['linked_attribute_value'] ) ) ? $_POST['linked_attribute_value'] : ''; // Input var okay.
			update_post_meta( $post_id, 'linked_attribute_value', $checked );

			$selected = ( isset( $_POST['linked_attribute'] ) && ! empty( $_POST['linked_attribute'] ) ) ? $_POST['linked_attribute'] : ''; // Input var okay.
			update_post_meta( $post_id, 'linked_attribute', esc_attr( $selected ) );
		}
	}

	/**
	 * Display saved meta in hidden field on the front
	 *
	 * @since 1.0.0
	 */
	public function simplewlv_frontend_hidden_meta() {
		global $woocommerce, $post;

		$linked_attribute_value = get_post_meta( $post->ID, 'linked_attribute_value', true );
		$linked_attribute       = get_post_meta( $post->ID, 'linked_attribute', true );
		$json_output = array(
			'linked_attributes'  => $linked_attribute_value,
			'selected_attribute' => $linked_attribute,
		);
		?>
		<input type="hidden" class="simplewlv_hidden" value="<?php echo htmlentities( wp_json_encode( $json_output ) ); ?>"/>
		<?php
	}

	/**
	 * Register the JavaScript  for the front end area.
	 *
	 * @since    1.0.0
	 */
	public function simplewlv_enqueue_frontend_styles() {
		if ( is_product() ) {
			wp_enqueue_style( $this->simplewlv, plugin_dir_url( __FILE__ ) . 'assets/css/simplewlv.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the stylesheets for the front end area.
	 *
	 * @since    1.0.0
	 */
	public function simplewlv_enqueue_frontend_scripts() {
		if ( is_product() ) {
			wp_enqueue_script( $this->simplewlv, plugin_dir_url( __FILE__ ) . 'assets/js/simplewlv.js', array( 'jquery' ), $this->version, false );
		}

	}

	/**
	 * Register the stylesheets for the front end area.
	 *
	 * @since    1.0.0
	 */
	public function simplewlv_enqueue_backend_scripts() {
		wp_enqueue_style( $this->simplewlv, plugin_dir_url( __FILE__ ) . 'assets/css/simplewlv_admin.css', array(), $this->version, 'all' );
	}


}

$init = new Simple_Linked_Variations_For_WooCommerce();
