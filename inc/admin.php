<?php
/**
 * admin.php — dependency notices and editor niceties.
 *
 * @package Encore
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_notices', static function () {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	if ( ! function_exists( 'drift_setting' ) ) {
		echo '<div class="notice notice-error"><p><strong>' . esc_html__( 'Encore needs the Drift Website plugin.', 'encore' ) . '</strong> ' . esc_html__( 'Without it there is no band content to show.', 'encore' ) . '</p></div>';
	}
	if ( ! class_exists( 'ACF' ) || ! function_exists( 'acf_get_field_groups' ) ) {
		$screen = get_current_screen();
		if ( $screen && in_array( $screen->id, [ 'dashboard', 'themes', 'edit-page' ], true ) ) {
			echo '<div class="notice notice-warning"><p>' . esc_html__( 'ACF Pro isn\'t active, so page modules can\'t be edited. Pages still show their default modules.', 'encore' ) . '</p></div>';
		}
	}
} );

/** Hide the content editor on pages — modules do the work. */
add_action( 'init', static function () {
	if ( function_exists( 'get_field' ) ) {
		remove_post_type_support( 'page', 'editor' );
	}
}, 20 );
