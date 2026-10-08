<?php
/**
 * assets.php — CSS/JS.
 *
 * Load order (later wins): fonts → core CSS → module CSS (only for modules
 * on this page) → child theme style.css → brand colours from the hub
 * (inc/brand.php).
 *
 * Fonts: a child theme picks its own Google Fonts without any PHP, by adding
 * a header line to its style.css:
 *     Surface Fonts: https://fonts.googleapis.com/css2?family=…&display=swap
 * (or "Surface Fonts: none" to load nothing). Otherwise Archivo loads.
 *
 * @package Surface_Theme
 */

defined( 'ABSPATH' ) || exit;

const SURFACE_DEFAULT_FONTS = 'https://fonts.googleapis.com/css2?family=Archivo:ital,wdth,wght@0,62..125,300..900;1,62..125,300..900&display=swap';

function surface_fonts_url(): string {
	if ( is_child_theme() ) {
		$header = get_file_data( get_stylesheet_directory() . '/style.css', [ 'fonts' => 'Surface Fonts' ] );
		$fonts  = trim( (string) ( $header['fonts'] ?? '' ) );
		if ( 'none' === strtolower( $fonts ) ) {
			return '';
		}
		if ( 0 === strpos( $fonts, 'https://fonts.googleapis.com/' ) ) {
			return $fonts;
		}
	}
	return (string) apply_filters( 'surface_fonts_url', SURFACE_DEFAULT_FONTS );
}

function surface_asset_version( string $rel ): string {
	$path = get_theme_file_path( $rel );
	return file_exists( $path ) ? (string) filemtime( $path ) : SURFACE_VERSION;
}

add_action( 'wp_enqueue_scripts', static function () {
	$fonts = surface_fonts_url();
	if ( $fonts ) {
		wp_enqueue_style( 'surface-fonts', $fonts, [], null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}

	$deps = $fonts ? [ 'surface-fonts' ] : [];
	foreach ( [ 'base', 'header', 'footer' ] as $file ) {
		$rel = "assets/css/core/{$file}.css";
		wp_enqueue_style( "surface-{$file}", SURFACE_URI . '/' . $rel, $deps, surface_asset_version( $rel ) );
		$deps = [ "surface-{$file}" ];
	}

	if ( is_singular( [ 'post', 'surface_gig', 'surface_release' ] ) || is_home() || is_archive() || is_search() || is_404() ) {
		wp_enqueue_style( 'surface-single', SURFACE_URI . '/assets/css/core/single.css', $deps, surface_asset_version( 'assets/css/core/single.css' ) );
		$deps = [ 'surface-single' ];
	}

	// Module CSS for this page's modules only, in <head> (no flash of unstyled content).
	foreach ( surface_current_layouts() as $layout ) {
		$rel = "assets/css/modules/{$layout}.css";
		if ( file_exists( get_theme_file_path( $rel ) ) ) {
			wp_enqueue_style( "surface-module-{$layout}", get_theme_file_uri( $rel ), $deps, surface_asset_version( $rel ) );
		}
	}

	if ( is_child_theme() ) {
		wp_enqueue_style( 'surface-child', get_stylesheet_uri(), [ 'surface-footer' ], surface_asset_version( 'style.css' ) );
	}

	wp_enqueue_script( 'surface-main', SURFACE_URI . '/assets/js/main.js', [], surface_asset_version( 'assets/js/main.js' ), [ 'in_footer' => true, 'strategy' => 'defer' ] );
	wp_localize_script( 'surface-main', 'Surface', [
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'ga4'     => surface_ga4_id(),
		'i18n'    => [
			'sending' => __( 'Sending…', 'surface-theme' ),
			'error'   => __( 'Something went wrong. Please try again, or email us directly.', 'surface-theme' ),
			'close'   => __( 'Close', 'surface-theme' ),
			'menu'    => __( 'Menu', 'surface-theme' ),
		],
	] );
} );

/** Preconnect for Google Fonts. */
add_filter( 'wp_resource_hints', static function ( array $urls, string $type ): array {
	if ( 'preconnect' === $type && surface_fonts_url() ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = [ 'href' => 'https://fonts.gstatic.com', 'crossorigin' ];
	}
	return $urls;
}, 10, 2 );
