<?php
/**
 * data.php — every query the templates need, in one place.
 *
 * All read plain WordPress data that the Drift: Surface plugin wrote at sync
 * time. Each one is guarded so a missing post type returns [] rather than
 * an error (e.g. plugin deactivated).
 *
 * @package Surface_Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Base query.
 *
 * @return WP_Post[]
 */
function surface_query( string $post_type, array $args = [] ): array {
	if ( ! post_type_exists( $post_type ) ) {
		return [];
	}
	return get_posts( array_merge(
		[
			'post_type'        => $post_type,
			'post_status'      => 'publish',
			'posts_per_page'   => -1,
			'orderby'          => [ 'menu_order' => 'ASC', 'title' => 'ASC' ],
			'no_found_rows'    => true,
			'suppress_filters' => false,
		],
		$args
	) );
}

/**
 * Gigs.
 *
 * @param string $when  upcoming | past | all.
 * @param int    $limit 0 = no limit.
 * @return WP_Post[]
 */
function surface_get_gigs( string $when = 'upcoming', int $limit = 0 ): array {
	$args = [
		'posts_per_page' => $limit > 0 ? $limit : -1,
		'meta_key'       => 'gig_date', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		'orderby'        => 'meta_value',
		'order'          => 'past' === $when ? 'DESC' : 'ASC',
	];
	if ( 'all' !== $when ) {
		$args['meta_query'] = [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			[
				'key'     => 'gig_date',
				'value'   => surface_today(),
				'compare' => 'past' === $when ? '<' : '>=',
				'type'    => 'CHAR', // Y-m-d sorts and compares correctly as text.
			],
		];
	}
	return surface_query( 'surface_gig', $args );
}

/**
 * Sorts posts by a Y-m-d meta value, newest first; posts without one go last.
 * Done in PHP rather than with meta_key ordering, which silently drops posts
 * that don't have the field.
 *
 * @param WP_Post[] $posts Posts.
 * @return WP_Post[]
 */
function surface_sort_by_date_meta( array $posts, string $key ): array {
	usort( $posts, static function ( WP_Post $a, WP_Post $b ) use ( $key ) {
		$da = (string) get_post_meta( $a->ID, $key, true );
		$db = (string) get_post_meta( $b->ID, $key, true );
		if ( $da === $db ) {
			return $a->menu_order <=> $b->menu_order;
		}
		if ( '' === $da || '' === $db ) {
			return '' === $da ? 1 : -1;
		}
		return strcmp( $db, $da );
	} );
	return $posts;
}

/** @return WP_Post[] Releases, newest first. */
function surface_get_releases( int $limit = 0, string $type = '' ): array {
	$args = [];
	if ( $type && taxonomy_exists( 'surface_release_type' ) ) {
		$args['tax_query'] = [ [ 'taxonomy' => 'surface_release_type', 'field' => 'name', 'terms' => $type ] ]; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	}
	$releases = surface_sort_by_date_meta( surface_query( 'surface_release', $args ), 'release_date' );
	return $limit > 0 ? array_slice( $releases, 0, $limit ) : $releases;
}

/** The release to feature: newest one ticked Featured, else the newest. */
function surface_get_latest_release(): ?WP_Post {
	$featured = surface_sort_by_date_meta( surface_query( 'surface_release', [
		'meta_query' => [ [ 'key' => 'featured', 'value' => '1' ] ], // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
	] ), 'release_date' );
	if ( $featured ) {
		return $featured[0];
	}
	$latest = surface_get_releases( 1 );
	return $latest[0] ?? null;
}

/** @return WP_Post[] Tracks of a release, in tracklist order. */
function surface_get_tracks( int $release_id ): array {
	if ( function_exists( 'drift_surface_linked_posts' ) ) {
		$tracks = drift_surface_linked_posts( $release_id, 'tracks' );
		if ( $tracks ) {
			return $tracks;
		}
	}
	return [];
}

/** @return WP_Post[] */
function surface_get_members(): array {
	return surface_query( 'surface_member' );
}

/** @return WP_Post[] */
function surface_get_press( int $limit = 0 ): array {
	return surface_query( 'surface_press', [ 'posts_per_page' => $limit > 0 ? $limit : -1 ] );
}

/** @return WP_Post[] */
function surface_get_merch(): array {
	return surface_query( 'surface_merch' );
}

/** @return WP_Post[] */
function surface_get_photos( string $album = '', int $limit = 0 ): array {
	$args = [ 'posts_per_page' => $limit > 0 ? $limit : -1 ];
	if ( $album && taxonomy_exists( 'surface_album' ) ) {
		$args['tax_query'] = [ [ 'taxonomy' => 'surface_album', 'field' => 'name', 'terms' => $album ] ]; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	}
	return surface_query( 'surface_photo', $args );
}

/** @return WP_Term[] Albums that have photos. */
function surface_get_albums(): array {
	if ( ! taxonomy_exists( 'surface_album' ) ) {
		return [];
	}
	$terms = get_terms( [ 'taxonomy' => 'surface_album', 'hide_empty' => true ] );
	return is_wp_error( $terms ) ? [] : $terms;
}

/** @return WP_Post[] Videos, newest first (undated last). */
function surface_get_videos( bool $featured_only = false, int $limit = 0 ): array {
	$args = [];
	if ( $featured_only ) {
		$args['meta_query'] = [ [ 'key' => 'featured', 'value' => '1' ] ]; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
	}
	$videos = surface_sort_by_date_meta( surface_query( 'surface_video', $args ), 'video_date' );
	return $limit > 0 ? array_slice( $videos, 0, $limit ) : $videos;
}

/** @return WP_Post[] */
function surface_get_news( int $limit = 6 ): array {
	return get_posts( [
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => $limit > 0 ? $limit : -1,
		'no_found_rows'  => true,
	] );
}
