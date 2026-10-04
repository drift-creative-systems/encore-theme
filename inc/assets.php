<?php
/**
 * assets.php — CSS/JS.
 *
 * Load order (later wins): fonts → core CSS → module CSS (only for modules
 * on this page) → child theme style.css → brand colours from Airtable
 * (inc/brand.php).
 *
 * Fonts: a child theme picks its own Google Fonts without any PHP, by adding
 * a header line to its style.css:
 *     Encore Fonts: https://fonts.googleapis.com/css2?family=…&display=swap
 * (or "Encore Fonts: none" to load nothing). Otherwise Archivo loads.
 *
 * @package Encore
 */

defined( 'ABSPATH' ) || exit;

const ENCORE_DEFAULT_FONTS = 'https://fonts.googleapis.com/css2?family=Archivo:ital,wdth,wght@0,62..125,300..900;1,62..125,300..900&display=swap';

function encore_fonts_url(): string {
	if ( is_child_theme() ) {
		$header = get_file_data( get_stylesheet_directory() . '/style.css', [ 'fonts' => 'Encore Fonts' ] );
		$fonts  = trim( (string) ( $header['fonts'] ?? '' ) );
		if ( 'none' === strtolower( $fonts ) ) {
			return '';
		}
		if ( 0 === strpos( $fonts, 'https://fonts.googleapis.com/' ) ) {
			return $fonts;
		}
	}
	return (string) apply_filters( 'encore_fonts_url', ENCORE_DEFAULT_FONTS );
}

function encore_asset_version( string $rel ): string {
	$path = get_theme_file_path( $rel );
	return file_exists( $path ) ? (string) filemtime( $path ) : ENCORE_VERSION;
}

add_action( 'wp_enqueue_scripts', static function () {
	$fonts = encore_fonts_url();
	if ( $fonts ) {
		wp_enqueue_style( 'encore-fonts', $fonts, [], null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}

	$deps = $fonts ? [ 'encore-fonts' ] : [];
	foreach ( [ 'base', 'header', 'footer' ] as $file ) {
		$rel = "assets/css/core/{$file}.css";
		wp_enqueue_style( "encore-{$file}", ENCORE_URI . '/' . $rel, $deps, encore_asset_version( $rel ) );
		$deps = [ "encore-{$file}" ];
	}

	if ( is_singular( [ 'post', 'encore_gig', 'encore_release' ] ) || is_home() || is_archive() || is_search() || is_404() ) {
		wp_enqueue_style( 'encore-single', ENCORE_URI . '/assets/css/core/single.css', $deps, encore_asset_version( 'assets/css/core/single.css' ) );
		$deps = [ 'encore-single' ];
	}

	// Module CSS for this page's modules only, in <head> (no flash of unstyled content).
	foreach ( encore_current_layouts() as $layout ) {
		$rel = "assets/css/modules/{$layout}.css";
		if ( file_exists( get_theme_file_path( $rel ) ) ) {
			wp_enqueue_style( "encore-module-{$layout}", get_theme_file_uri( $rel ), $deps, encore_asset_version( $rel ) );
		}
	}

	if ( is_child_theme() ) {
		wp_enqueue_style( 'encore-child', get_stylesheet_uri(), [ 'encore-footer' ], encore_asset_version( 'style.css' ) );
	}

	wp_enqueue_script( 'encore-main', ENCORE_URI . '/assets/js/main.js', [], encore_asset_version( 'assets/js/main.js' ), [ 'in_footer' => true, 'strategy' => 'defer' ] );
	wp_localize_script( 'encore-main', 'Encore', [
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'ga4'     => encore_ga4_id(),
		'i18n'    => [
			'sending' => __( 'Sending…', 'encore' ),
			'error'   => __( 'Something went wrong. Please try again, or email us directly.', 'encore' ),
			'close'   => __( 'Close', 'encore' ),
			'menu'    => __( 'Menu', 'encore' ),
		],
	] );
} );

/** Preconnect for Google Fonts. */
add_filter( 'wp_resource_hints', static function ( array $urls, string $type ): array {
	if ( 'preconnect' === $type && encore_fonts_url() ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = [ 'href' => 'https://fonts.gstatic.com', 'crossorigin' ];
	}
	return $urls;
}, 10, 2 );
