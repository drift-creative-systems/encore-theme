<?php
/**
 * schema.php — structured data and fallback meta.
 *
 * JSON-LD (always): MusicGroup on the front page (with members and upcoming
 * gigs as events), MusicEvent on each gig, MusicAlbum on each release.
 * Meta description + Open Graph only when no SEO plugin is active.
 *
 * @package Encore
 */

defined( 'ABSPATH' ) || exit;

function encore_has_seo_plugin(): bool {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

function encore_image_url( int $id, string $size = 'encore-wide' ): string {
	return $id ? (string) wp_get_attachment_image_url( $id, $size ) : '';
}

/** The band as a schema.org MusicGroup reference (or full node). */
function encore_schema_group( bool $full = false ): array {
	$group = [
		'@type' => 'MusicGroup',
		'@id'   => home_url( '/#band' ),
		'name'  => encore_artist_name(),
		'url'   => home_url( '/' ),
	];
	if ( ! $full ) {
		return $group;
	}

	$genre = (string) encore_setting( 'genre' );
	if ( $genre ) {
		$group['genre'] = $genre;
	}
	$logo = encore_image_url( (int) encore_setting( 'logo', 0 ), 'full' );
	$hero = encore_image_url( (int) encore_setting( 'hero_image', 0 ), 'encore-hero' );
	if ( $logo ) {
		$group['logo'] = $logo;
	}
	if ( $hero || $logo ) {
		$group['image'] = $hero ?: $logo;
	}
	$about = (string) encore_setting( 'bio_short', encore_setting( 'tagline' ) );
	if ( $about ) {
		$group['description'] = wp_strip_all_tags( $about );
	}
	$town = (string) encore_setting( 'hometown' );
	if ( $town ) {
		$group['foundingLocation'] = [ '@type' => 'Place', 'name' => $town ];
	}
	$same = array_column( array_merge( encore_social_links(), encore_streaming_links() ), 'url' );
	if ( $same ) {
		$group['sameAs'] = array_values( $same );
	}
	$members = encore_get_members();
	if ( $members ) {
		$group['member'] = array_map( static function ( WP_Post $m ) {
			$role = (string) get_post_meta( $m->ID, 'role', true );
			return array_filter( [
				'@type'    => 'OrganizationRole',
				'member'   => [ '@type' => 'Person', 'name' => $m->post_title ],
				'roleName' => $role,
			] );
		}, $members );
	}
	$events = array_filter( array_map( 'encore_schema_event', encore_get_gigs( 'upcoming', 20 ) ) );
	if ( $events ) {
		$group['event'] = array_values( $events );
	}
	return $group;
}

/** One gig as a MusicEvent, or null if it has no date. */
function encore_schema_event( WP_Post $gig ): ?array {
	$date = (string) get_post_meta( $gig->ID, 'gig_date', true );
	if ( ! $date ) {
		return null;
	}
	$status = encore_gig_status( $gig->ID );
	$venue  = (string) get_post_meta( $gig->ID, 'venue', true );
	$city   = (string) get_post_meta( $gig->ID, 'city', true );
	$tix    = (string) get_post_meta( $gig->ID, 'ticket_url', true );

	$event = [
		'@type'               => 'MusicEvent',
		'name'                => encore_artist_name() . ( $venue ? ' at ' . $venue : '' ),
		'startDate'           => $date,
		'eventStatus'         => 'cancelled' === $status['class'] ? 'https://schema.org/EventCancelled' : 'https://schema.org/EventScheduled',
		'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
		'url'                 => get_permalink( $gig ),
		'performer'           => encore_schema_group(),
		'location'            => [
			'@type'   => 'Place',
			'name'    => $venue ?: $city,
			'address' => array_filter( [
				'@type'           => 'PostalAddress',
				'addressLocality' => $city,
				'addressCountry'  => (string) get_post_meta( $gig->ID, 'country', true ),
			] ),
		],
	];
	if ( $tix ) {
		$event['offers'] = [
			'@type'        => 'Offer',
			'url'          => $tix,
			'availability' => 'https://schema.org/' . $status['schema'],
		];
	}
	$hero = encore_image_url( (int) encore_setting( 'hero_image', 0 ) );
	if ( $hero ) {
		$event['image'] = $hero;
	}
	return $event;
}

function encore_schema_album( WP_Post $release ): array {
	$meta   = encore_release_meta( $release->ID );
	$tracks = encore_get_tracks( $release->ID );
	$types  = [ 'single' => 'SingleRelease', 'ep' => 'EPRelease', 'album' => 'AlbumRelease', 'live' => 'AlbumRelease', 'remix' => 'SingleRelease' ];

	$album = array_filter( [
		'@type'            => 'MusicAlbum',
		'name'             => $release->post_title,
		'url'              => get_permalink( $release ),
		'byArtist'         => encore_schema_group(),
		'datePublished'    => $meta['date']['iso'] ?? '',
		'image'            => (string) get_the_post_thumbnail_url( $release, 'encore-square' ),
		'albumReleaseType' => isset( $types[ strtolower( $meta['type'] ) ] ) ? 'https://schema.org/' . $types[ strtolower( $meta['type'] ) ] : '',
		'numTracks'        => $tracks ? count( $tracks ) : '',
		'recordLabel'      => ( $label = (string) get_post_meta( $release->ID, 'label', true ) ) ? [ '@type' => 'Organization', 'name' => $label ] : '',
	] );

	if ( $tracks ) {
		$album['track'] = [
			'@type'           => 'ItemList',
			'numberOfItems'   => count( $tracks ),
			'itemListElement' => array_map( static function ( WP_Post $t, int $i ) {
				return [
					'@type'    => 'ListItem',
					'position' => $i + 1,
					'item'     => array_filter( [
						'@type'    => 'MusicRecording',
						'name'     => $t->post_title,
						'duration' => encore_duration_iso( (string) get_post_meta( $t->ID, 'duration', true ) ),
					] ),
				];
			}, $tracks, array_keys( $tracks ) ),
		];
	}
	return $album;
}

add_action( 'wp_head', static function () {
	$node = null;
	if ( is_front_page() ) {
		$node = encore_schema_group( true );
	} elseif ( is_singular( 'encore_gig' ) ) {
		$node = encore_schema_event( get_queried_object() );
	} elseif ( is_singular( 'encore_release' ) ) {
		$node = encore_schema_album( get_queried_object() );
	}
	if ( $node ) {
		$node = [ '@context' => 'https://schema.org' ] + $node;
		echo '<script type="application/ld+json">' . wp_json_encode( $node, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
	}
}, 20 );

/** Description + Open Graph, only without an SEO plugin. */
add_action( 'wp_head', static function () {
	if ( encore_has_seo_plugin() ) {
		return;
	}

	$description = '';
	$image       = '';
	if ( is_singular() && ! is_front_page() ) {
		$post        = get_queried_object();
		$description = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( (string) $post->post_content ), 30 );
		$image       = (string) get_the_post_thumbnail_url( $post, 'encore-wide' );
	}
	if ( ! $description ) {
		$description = (string) encore_setting( 'seo_description', encore_setting( 'bio_short', encore_setting( 'tagline' ) ) );
	}
	if ( ! $image ) {
		$image = encore_image_url( (int) encore_setting( 'hero_image', 0 ) ) ?: encore_image_url( (int) encore_setting( 'logo', 0 ), 'full' );
	}

	if ( $description ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( wp_strip_all_tags( $description ) ) );
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( wp_strip_all_tags( $description ) ) );
	}
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( encore_artist_name() ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( wp_get_document_title() ) );
	printf( '<meta property="og:type" content="%s">' . "\n", is_singular( [ 'post' ] ) ? 'article' : ( is_singular( 'encore_release' ) ? 'music.album' : 'website' ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( is_singular() ? get_permalink() : home_url( add_query_arg( [] ) ) ) );
	if ( $image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	}
}, 3 );
