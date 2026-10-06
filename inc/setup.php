<?php
/**
 * setup.php — theme supports, menus and image sizes.
 *
 * @package Encore
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', static function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ] );
	add_theme_support( 'responsive-embeds' );

	register_nav_menus( [
		'primary' => __( 'Main menu', 'encore' ),
		'footer'  => __( 'Footer menu', 'encore' ),
	] );

	add_image_size( 'encore-square', 900, 900, true );     // Artwork, merch.
	add_image_size( 'encore-portrait', 800, 1000, true );  // Members.
	add_image_size( 'encore-wide', 1600, 900, true );      // News, video posters.
	add_image_size( 'encore-hero', 2400, 1600, false );    // Heroes.
} );

/** Post-type and page-slug body classes for module-independent styling. */
add_filter( 'body_class', static function ( array $classes ): array {
	if ( is_singular() ) {
		$post      = get_queried_object();
		$classes[] = 'type-' . sanitize_html_class( $post->post_type );
		if ( is_page() ) {
			$classes[] = 'page-' . sanitize_html_class( $post->post_name );
		}
	}
	if ( ! encore_plugin_ready() ) {
		$classes[] = 'no-encore-website';
	}
	return $classes;
} );

/** Excerpt length for news cards. */
add_filter( 'excerpt_length', static fn() => 28 );
add_filter( 'excerpt_more', static fn() => '…' );

/** "has-hero" when the page opens with a full-bleed module, so the header can sit over it. */
add_filter( 'body_class', static function ( array $classes ): array {
	if ( ( is_page() || is_front_page() ) && function_exists( 'encore_page_rows' ) ) {
		$first = encore_page_rows()[0]['acf_fc_layout'] ?? '';
		if ( in_array( $first, [ 'hero_module', 'page_header_module' ], true ) ) {
			$classes[] = 'has-hero';
		}
	}
	return $classes;
} );

/** Menu fallback before anyone builds one: top-level pages. */
function encore_menu_fallback(): void {
	$pages = get_pages( [ 'parent' => 0, 'sort_column' => 'menu_order,post_title', 'exclude' => (int) get_option( 'page_on_front' ) ] );
	if ( ! $pages ) {
		return;
	}
	echo '<ul class="nav__list" role="list">';
	foreach ( $pages as $page ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( get_permalink( $page ) ), esc_html( $page->post_title ) );
	}
	echo '</ul>';
}
