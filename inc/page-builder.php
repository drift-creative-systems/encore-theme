<?php
/**
 * page-builder.php — renders a page's modules.
 *
 * Rows come from the ACF flexible content field `page_builder`
 * (acf-json/group_surface_page_builder.json). When a page has no rows — or
 * ACF Pro isn't active — the rows the Drift: Surface plugin's map defines for
 * that page are used instead (front page = the map's "home" page, others
 * matched by slug), so a fresh site looks finished before anyone opens the
 * editor.
 *
 * Each row renders template-parts/modules/{layout}-{layout_option}.php,
 * falling back to {layout}-one.php. Variant files get the row's values in
 * $args and never call ACF themselves. Child themes can override a variant
 * file in an emergency (CLAUDE.md rule 3).
 *
 * @package Surface_Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Rows for a page: saved rows, else the map's default rows.
 *
 * @return array[]
 */
function surface_page_rows( ?int $post_id = null ): array {
	static $cache = [];
	$post_id = $post_id ?: (int) get_queried_object_id();

	if ( isset( $cache[ $post_id ] ) ) {
		return $cache[ $post_id ];
	}

	$rows = [];
	if ( $post_id && function_exists( 'get_field' ) ) {
		$saved = get_field( 'page_builder', $post_id );
		$rows  = is_array( $saved ) ? $saved : [];
	}
	if ( ! $rows && $post_id ) {
		$rows = surface_fallback_rows( $post_id );
	}

	$cache[ $post_id ] = (array) apply_filters( 'surface_page_rows', $rows, $post_id );
	return $cache[ $post_id ];
}

/**
 * Default rows from the Drift: Surface plugin's product map.
 *
 * @return array[]
 */
function surface_fallback_rows( int $post_id ): array {
	$rows = [];

	$creator = class_exists( 'Drift_Surface_Page_Creator' ) ? 'Drift_Surface_Page_Creator' : '';
	if ( $creator ) {
		$post    = get_post( $post_id );
		$is_home = (int) get_option( 'page_on_front' ) === $post_id;

		foreach ( $creator::definitions() as $def ) {
			if ( ( $is_home && '' === $def['slug'] ) || ( ! $is_home && $post && $def['slug'] === $post->post_name ) ) {
				$rows = (array) $def['rows'];
				break;
			}
		}
	}

	return (array) apply_filters( 'surface_fallback_rows', $rows, $post_id );
}

/** Layout slugs on the current page (for loading module CSS in <head>). */
function surface_current_layouts(): array {
	if ( ! is_singular( 'page' ) && ! is_front_page() ) {
		return [];
	}
	$layouts = [];
	foreach ( surface_page_rows() as $row ) {
		if ( ! empty( $row['acf_fc_layout'] ) ) {
			$layouts[] = sanitize_key( (string) $row['acf_fc_layout'] );
		}
	}
	return array_values( array_unique( $layouts ) );
}

/**
 * Renders rows. Unknown layouts are skipped (with a notice in debug mode).
 *
 * @param array[] $rows Rows with 'acf_fc_layout' + sub-field values.
 */
function surface_render_rows( array $rows ): void {
	foreach ( array_values( $rows ) as $i => $row ) {
		$layout = sanitize_key( (string) ( $row['acf_fc_layout'] ?? '' ) );
		if ( '' === $layout ) {
			continue;
		}

		$option = sanitize_key( (string) ( $row['layout_option'] ?? 'one' ) ) ?: 'one';
		$file   = locate_template( [
			"template-parts/modules/{$layout}-{$option}.php",
			"template-parts/modules/{$layout}-one.php",
		] );

		if ( ! $file ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				trigger_error( esc_html( "Surface theme: no template for module \"{$layout}\"." ), E_USER_NOTICE ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			}
			continue;
		}

		$row['_layout'] = $layout;
		$row['_anchor'] = ! empty( $row['anchor_id'] ) ? sanitize_title( (string) $row['anchor_id'] ) : $layout . '-' . ( $i + 1 );
		$row['_index']  = $i;

		load_template( $file, false, $row );
	}
}
