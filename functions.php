<?php
/**
 * Encore — master theme bootstrap.
 *
 * Loads inc/*.php in a fixed order. One concern per file. See CLAUDE.md for
 * the architecture rules (the short version: all PHP lives here, child
 * themes are CSS only, and nothing in this theme ever calls Airtable).
 *
 * @package Encore
 */

defined( 'ABSPATH' ) || exit;

define( 'ENCORE_VERSION', wp_get_theme( get_template() )->get( 'Version' ) ?: '1.3.0' );
define( 'ENCORE_DIR', get_template_directory() );
define( 'ENCORE_URI', get_template_directory_uri() );

$encore_modules = [
	'setup',        // Theme supports, menus, image sizes.
	'requirements', // Encore Website plugin: holding page + install/activate notice until it's active.
	'housekeeping', // Head cleanup, comments off, block editor off, SVG/WebP uploads.
	'helpers',      // Settings wrapper, links, formatting, embeds.
	'data',         // Queries for gigs, releases, members, media…
	'assets',       // Core CSS/JS, fonts (child-theme aware), module CSS loader.
	'brand',        // Airtable brand colours → CSS custom properties.
	'page-builder', // Module renderer + fallback rows when a page has none.
	'schema',       // JSON-LD (MusicGroup, MusicEvent, MusicAlbum) and fallback meta.
	'consent',      // Customizer: GA4 ID + cookie consent (only when analytics is set).
	'acf-json',     // ACF Local JSON load/save points.
	'admin',        // ACF notice, editor niceties.
	'updates',      // Self-update from GitHub releases.
];

foreach ( $encore_modules as $encore_module ) {
	$encore_file = ENCORE_DIR . '/inc/' . $encore_module . '.php';
	if ( is_readable( $encore_file ) ) {
		require_once $encore_file;
	} else {
		error_log( 'Encore — missing inc file: ' . $encore_file ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}
}
unset( $encore_modules, $encore_module, $encore_file );
