<?php
/**
 * admin.php — ACF notice and editor niceties. The Drift: Surface plugin
 * requirement lives in inc/requirements.php.
 *
 * @package Surface_Theme
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_notices', static function () {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	if ( ! class_exists( 'ACF' ) || ! function_exists( 'acf_get_field_groups' ) ) {
		$screen = get_current_screen();
		if ( $screen && in_array( $screen->id, [ 'dashboard', 'themes', 'edit-page' ], true ) ) {
			echo '<div class="notice notice-warning"><p>' . esc_html__( 'ACF Pro isn\'t active, so page modules can\'t be edited. Pages still show their default modules.', 'surface-theme' ) . '</p></div>';
		}
	}
} );

/** Hide the content editor on pages — modules do the work. */
add_action( 'init', static function () {
	if ( function_exists( 'get_field' ) ) {
		remove_post_type_support( 'page', 'editor' );
	}
}, 20 );
