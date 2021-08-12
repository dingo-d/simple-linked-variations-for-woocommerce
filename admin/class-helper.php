<?php
/**
 * Class holding the helpers
 *
 * Helpers that are used through the plugin.
 *
 * @package Simplewlv\Admin
 * @since 1.1.0
 */

namespace Simplewlv\Admin;

/**
 * Helper class that holds static helper funtions used
 * in the plugin.
 *
 * @package Simplewlv\Admin
 * @since 1.1.0
 */
class Helper {
  /**
   * Return full path for specific asset from manifest.json
   * This is used for cache busting assets.
   *
   * @param string $key File name key you want to get from manifest.
   * @return string Full path to asset.
   *
   * @since 1.1.0
   */
  public static function get_manifest_assets_data( $key = null ) {
    $data = json_decode( SIMPLEWLV_ASSETS_MANIFEST, true );

    if ( ! ( $key || $data ) ) {
      return;
    }

    $asset = ( gettype( $data ) === 'array' && array_key_exists( $key, $data ) ) ? $data[ $key ] : '';

    if ( ! empty( $asset ) ) {
      return rtrim( plugin_dir_url( __DIR__ ), '/' ) . $asset;
    }
  }

}
