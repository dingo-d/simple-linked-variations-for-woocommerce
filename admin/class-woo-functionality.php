<?php
/**
 * Class holding the hooks and main plugin functionality
 *
 * @package Simplewlv\Admin
 * @since 1.1.0
 */

namespace Simplewlv\Admin;

/**
 * Holds all the WooCommerce functionality.
 */
class Woo_Functionality {
  /**
   * Display saved meta in hidden field on the front
   */
  public function frontend_hidden_meta() {
    global $woocommerce, $post;

    $post_id = $post->ID;

    $linked_attribute_value = get_post_meta( $post_id, 'linked_attribute_value', true );
    $linked_attribute       = get_post_meta( $post_id, 'linked_attribute', true );

    $json_output = array(
        'linked_attributes'  => $linked_attribute_value,
        'selected_attribute' => $linked_attribute,
    );

    ?>
    <input type="hidden" class="simplewlv_hidden" value="<?php echo wp_kses_post( htmlspecialchars( wp_json_encode( $json_output ), ENT_QUOTES, 'UTF-8' ) ); ?>"/>
    <?php
  }


  /**
   * Register additional tab for linking
   *
   * Adding additional tab for linked variations.
   *
   * @param  array $product_data_tabs Initial product data tab.
   * @return array Updated product data tab.
   */
  public function add_product_data_tabs( $product_data_tabs ) {
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
  public function product_data_fields() {
    global $woocommerce, $post;
    ?>
    <div id="simplewlv_linked_attributes" class="panel woocommerce_options_panel">
      <?php
      $product = wc_get_product();
      if ( $product->has_child() ) :
        $available_variations = $product->get_available_variations();
        $all_attributes       = $product->get_variation_attributes();
        $attributes           = array( '-' => '-' );
        $attribute_values     = array();

        foreach ( $all_attributes as $attribute_key => $attribute_value ) {
          $attribute_name = ucfirst( trim( str_replace( 'pa_', '', $attribute_key ) ) );
          $attributes[ str_replace( ' ', '-', strtolower( $attribute_key ) ) ] = $attribute_name;
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
                  'value'   => get_post_meta( $product->get_id(), 'linked_attribute', true ),
                  'options' => $attributes,
              )
            );
            ?>
          </div>
          <div class="attributes right">
            <h4><?php esc_html_e( 'Attribute values', 'simplewlv' ); ?></h4>
            <?php
            $linked_post_meta = get_post_meta( $product->get_id(), 'linked_attribute_value', true );
            foreach ( $attribute_values as $att_val_key => $att_val_value ) {
              $att_val_key = strtolower( $att_val_key );
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

}
