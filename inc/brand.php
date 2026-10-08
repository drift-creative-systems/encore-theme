<?php
/**
 * brand.php — the band's colours from the hub (Site Settings → Primary /
 * Secondary Colour) as CSS custom properties, printed after everything else
 * so they win over the child theme's defaults.
 *
 * --accent      Primary Colour
 * --accent-2    Secondary Colour (falls back to the accent)
 * --on-accent   Black or white, whichever reads better on the accent
 * --accent-rgb  "r, g, b" for rgba() tints
 *
 * @package Surface_Theme
 */

defined( 'ABSPATH' ) || exit;

/** "#ee4367" / "ee4367" / "#e46" → [r, g, b], or null. */
function surface_hex_rgb( string $hex ): ?array {
	$hex = ltrim( trim( $hex ), '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( ! preg_match( '/^[0-9a-f]{6}$/i', $hex ) ) {
		return null;
	}
	return array_map( 'hexdec', str_split( $hex, 2 ) );
}

/** WCAG relative luminance. */
function surface_luminance( array $rgb ): float {
	$c = array_map( static function ( $v ) {
		$v /= 255;
		return $v <= 0.03928 ? $v / 12.92 : ( ( $v + 0.055 ) / 1.055 ) ** 2.4;
	}, $rgb );
	return 0.2126 * $c[0] + 0.7152 * $c[1] + 0.0722 * $c[2];
}

function surface_brand_css(): string {
	$primary   = surface_hex_rgb( (string) surface_setting( 'colour_primary' ) );
	$secondary = surface_hex_rgb( (string) surface_setting( 'colour_secondary' ) );

	if ( ! $primary && ! $secondary ) {
		return '';
	}

	$vars = [];
	if ( $primary ) {
		$lum             = surface_luminance( $primary );
		$vars['--accent']     = vsprintf( '#%02x%02x%02x', $primary );
		$vars['--accent-rgb'] = implode( ', ', $primary );
		// Contrast vs white (1.05/(L+.05)) and black ((L+.05)/.05): pick the larger.
		$vars['--on-accent']  = ( ( $lum + 0.05 ) / 0.05 ) > ( 1.05 / ( $lum + 0.05 ) ) ? '#000' : '#fff';
	}
	if ( $secondary ) {
		$vars['--accent-2'] = vsprintf( '#%02x%02x%02x', $secondary );
	}

	$css = ':root{';
	foreach ( $vars as $name => $value ) {
		$css .= $name . ':' . $value . ';';
	}
	return $css . '}';
}

add_action( 'wp_enqueue_scripts', static function () {
	$css = surface_brand_css();
	if ( $css ) {
		wp_add_inline_style( is_child_theme() ? 'surface-child' : 'surface-footer', $css );
	}
}, 20 );

/** theme-color for mobile browser chrome. */
add_action( 'wp_head', static function () {
	$rgb = surface_hex_rgb( (string) surface_setting( 'colour_primary' ) );
	if ( $rgb ) {
		printf( '<meta name="theme-color" content="%s">' . "\n", esc_attr( vsprintf( '#%02x%02x%02x', $rgb ) ) );
	}
}, 2 );
