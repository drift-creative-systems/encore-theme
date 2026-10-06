<?php
/**
 * acf-json.php — ACF Local JSON.
 *
 * Saves ALWAYS go to this master theme's acf-json/, never the child's, so
 * new module fields can't be saved into whichever child is active and lost.
 * Child themes are CSS only, so there is nothing for them to save.
 *
 * @package Encore
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'acf/settings/save_json', static fn() => ENCORE_DIR . '/acf-json' );

add_filter( 'acf/settings/load_json', static function ( array $paths ): array {
	unset( $paths[0] );
	$paths[] = ENCORE_DIR . '/acf-json';
	return $paths;
} );
