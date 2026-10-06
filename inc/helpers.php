<?php
/**
 * helpers.php — small, theme-wide helpers. Anything Airtable-derived comes
 * through encore_setting() (a guarded wrapper round the plugin's
 * encore_website_setting()), so the theme still renders if the plugin is off.
 *
 * @package Encore
 */

defined( 'ABSPATH' ) || exit;

/**
 * A synced site setting (Airtable "Site Settings"), with a fallback.
 *
 * @param string $key     Key from the Encore map, e.g. 'tagline'.
 * @param mixed  $default Fallback.
 * @return mixed
 */
function encore_setting( string $key, $default = '' ) {
	if ( function_exists( 'encore_website_setting' ) ) {
		return encore_website_setting( $key, $default );
	}
	return function_exists( 'drift_setting' ) ? drift_setting( $key, $default ) : $default; // Plugin 1.x.
}

/**
 * The hidden inputs an Encore Website form needs (action, form key, nonce,
 * honeypot). Prints nothing if the plugin is off.
 *
 * @param string $form Form key from the plugin's map, e.g. 'enquiry'.
 */
function encore_form_hidden_fields( string $form ): void {
	if ( function_exists( 'encore_website_form_hidden_fields' ) ) {
		encore_website_form_hidden_fields( $form );
	} elseif ( function_exists( 'drift_form_hidden_fields' ) ) {
		drift_form_hidden_fields( $form ); // Plugin 1.x.
	}
}

function encore_artist_name(): string {
	return (string) encore_setting( 'name', get_bloginfo( 'name' ) );
}

/**
 * Logo <img>, or '' if none. 'light' is the version for dark backgrounds.
 */
function encore_logo( string $variant = 'default', string $size = 'medium', array $attr = [] ): string {
	$id = (int) encore_setting( 'light' === $variant ? 'logo_light' : 'logo', 0 );
	if ( ! $id && 'light' === $variant ) {
		$id = (int) encore_setting( 'logo', 0 );
	}
	if ( ! $id ) {
		return '';
	}
	return (string) wp_get_attachment_image( $id, $size, false, array_merge( [ 'alt' => encore_artist_name(), 'loading' => 'eager', 'class' => 'logo-img' ], $attr ) );
}

/** @return array<string, array{label: string, url: string}> */
function encore_social_links(): array {
	$labels = [
		'instagram' => 'Instagram',
		'tiktok'    => 'TikTok',
		'youtube'   => 'YouTube',
		'facebook'  => 'Facebook',
		'x'         => 'X',
	];
	return encore_collect_links( $labels );
}

/** @return array<string, array{label: string, url: string}> */
function encore_streaming_links(): array {
	$labels = [
		'spotify'     => 'Spotify',
		'apple_music' => 'Apple Music',
		'bandcamp'    => 'Bandcamp',
		'soundcloud'  => 'SoundCloud',
	];
	return encore_collect_links( $labels );
}

function encore_collect_links( array $labels ): array {
	$out = [];
	foreach ( $labels as $key => $label ) {
		$url = (string) encore_setting( $key );
		if ( '' !== $url ) {
			$out[ $key ] = [ 'label' => $label, 'url' => $url ];
		}
	}
	return $out;
}

/** Streaming links for one release. */
function encore_release_links( int $post_id ): array {
	$labels = [
		'spotify_url'     => 'Spotify',
		'apple_music_url' => 'Apple Music',
		'bandcamp_url'    => 'Bandcamp',
		'youtube_url'     => 'YouTube',
	];
	$out = [];
	foreach ( $labels as $key => $label ) {
		$url = (string) get_post_meta( $post_id, $key, true );
		if ( '' !== $url ) {
			$out[ $key ] = [ 'label' => $label, 'url' => $url ];
		}
	}
	return $out;
}

/**
 * A row of pill links.
 *
 * @param array  $links From encore_*_links().
 * @param string $class Extra list class.
 * @param string $label aria-label for the list.
 */
function encore_link_list( array $links, string $class = '', string $label = '' ): string {
	if ( ! $links ) {
		return '';
	}
	$html = sprintf( '<ul class="link-list %s" role="list"%s>', esc_attr( $class ), $label ? ' aria-label="' . esc_attr( $label ) . '"' : '' );
	foreach ( $links as $key => $link ) {
		$html .= sprintf(
			'<li><a class="pill pill--%1$s" href="%2$s" target="_blank" rel="noopener">%3$s<span class="screen-reader-text"> %4$s</span></a></li>',
			esc_attr( (string) $key ),
			esc_url( $link['url'] ),
			esc_html( $link['label'] ),
			esc_html__( '(opens in a new tab)', 'encore' )
		);
	}
	return $html . '</ul>';
}

/** Today's date in the site's timezone, Y-m-d — what gig_date is compared against. */
function encore_today(): string {
	return wp_date( 'Y-m-d' );
}

/**
 * Display parts for a Y-m-d date.
 *
 * @return array{day: string, month: string, weekday: string, year: string, full: string, iso: string}|null
 */
function encore_date_parts( string $ymd ): ?array {
	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $ymd ) ) {
		return null;
	}
	$ts = ( new DateTimeImmutable( $ymd . ' 12:00:00', wp_timezone() ) )->getTimestamp();
	return [
		'day'     => wp_date( 'j', $ts ),
		'month'   => wp_date( 'M', $ts ),
		'weekday' => wp_date( 'D', $ts ),
		'year'    => wp_date( 'Y', $ts ),
		'full'    => wp_date( 'D j M Y', $ts ),
		'iso'     => $ymd,
	];
}

/**
 * Gig status → label, CSS modifier, and whether tickets can be bought.
 *
 * @return array{label: string, class: string, bookable: bool, schema: string}
 */
function encore_gig_status( int $post_id ): array {
	$raw = strtolower( trim( (string) get_post_meta( $post_id, 'status', true ) ) );
	$map = [
		'on sale'   => [ __( 'Tickets', 'encore' ), 'on-sale', true, 'InStock' ],
		'few left'  => [ __( 'Few left', 'encore' ), 'few-left', true, 'LimitedAvailability' ],
		'sold out'  => [ __( 'Sold out', 'encore' ), 'sold-out', false, 'SoldOut' ],
		'free'      => [ __( 'Free entry', 'encore' ), 'free', true, 'InStock' ],
		'cancelled' => [ __( 'Cancelled', 'encore' ), 'cancelled', false, 'Discontinued' ],
		'announced' => [ __( 'Tickets soon', 'encore' ), 'announced', false, 'PreOrder' ],
	];
	$hit = $map[ $raw ] ?? [ __( 'Tickets', 'encore' ), 'on-sale', true, 'InStock' ];
	return [ 'label' => $hit[0], 'class' => $hit[1], 'bookable' => $hit[2], 'schema' => $hit[3] ];
}

/** £20 / £19.50 */
function encore_price( $amount ): string {
	if ( '' === $amount || null === $amount || ! is_numeric( $amount ) ) {
		return '';
	}
	$amount = (float) $amount;
	return '£' . ( floor( $amount ) === $amount ? number_format( $amount, 0 ) : number_format( $amount, 2 ) );
}

/** "3:41" → "PT3M41S" (schema.org duration). */
function encore_duration_iso( string $duration ): string {
	if ( preg_match( '/^(?:(\d+):)?(\d{1,2}):(\d{2})$/', trim( $duration ), $m ) ) {
		$h = (int) $m[1];
		return 'PT' . ( $h ? $h . 'H' : '' ) . (int) $m[2] . 'M' . (int) $m[3] . 'S';
	}
	return '';
}

/** Release type name (Album, EP…) and year. */
function encore_release_meta( int $post_id ): array {
	$terms = get_the_terms( $post_id, 'encore_release_type' );
	$date  = encore_date_parts( (string) get_post_meta( $post_id, 'release_date', true ) );
	return [
		'type'   => ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '',
		'year'   => $date['year'] ?? '',
		'date'   => $date,
		'future' => $date && $date['iso'] > encore_today(),
	];
}

/**
 * YouTube / Vimeo details for a video URL.
 *
 * @return array{provider: string, id: string, embed: string, thumb: string}|null
 */
function encore_video( string $url ): ?array {
	if ( preg_match( '~(?:youtube\.com/(?:watch\?v=|embed/|shorts/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $url, $m ) ) {
		return [
			'provider' => 'youtube',
			'id'       => $m[1],
			'embed'    => 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?autoplay=1&rel=0',
			'thumb'    => 'https://i.ytimg.com/vi/' . $m[1] . '/hqdefault.jpg',
		];
	}
	if ( preg_match( '~vimeo\.com/(?:video/)?(\d+)~', $url, $m ) ) {
		return [
			'provider' => 'vimeo',
			'id'       => $m[1],
			'embed'    => 'https://player.vimeo.com/video/' . $m[1] . '?autoplay=1&dnt=1',
			'thumb'    => '',
		];
	}
	return null;
}

/**
 * Click-to-play video: a poster button that swaps in the player only when
 * pressed, so no third-party cookies or requests until the visitor asks.
 */
function encore_video_player( string $url, string $title, int $poster_id = 0 ): string {
	$video = encore_video( $url );
	if ( ! $video ) {
		return '';
	}
	$poster = $poster_id
		? wp_get_attachment_image( $poster_id, 'encore-wide', false, [ 'alt' => '' ] )
		: ( $video['thumb'] ? sprintf( '<img src="%s" alt="" loading="lazy" width="480" height="360">', esc_url( $video['thumb'] ) ) : '' );

	return sprintf(
		'<div class="video" data-embed="%1$s"><button type="button" class="video__play" aria-label="%2$s">%3$s<span class="video__icon" aria-hidden="true"></span></button><p class="video__note">%4$s</p></div>',
		esc_url( $video['embed'] ),
		/* translators: %s: video title. */
		esc_attr( sprintf( __( 'Play video: %s', 'encore' ), $title ) ),
		$poster, // Escaped by wp_get_attachment_image() / sprintf above.
		esc_html( 'youtube' === $video['provider'] ? __( 'Plays from YouTube.', 'encore' ) : __( 'Plays from Vimeo.', 'encore' ) )
	);
}

/**
 * Iframe-only filter for embed code synced from Airtable (Live Embed, Merch
 * Embed). The plugin already filters it at sync time; this repeats the
 * filter on output so templates never trust stored HTML. https sources only,
 * no srcdoc, no scripts.
 */
function encore_kses_iframe( string $html ): string {
	$allowed = [
		'iframe' => [
			'src'             => true,
			'title'           => true,
			'width'           => true,
			'height'          => true,
			'style'           => true,
			'allow'           => true,
			'allowfullscreen' => true,
			'frameborder'     => true,
			'scrolling'       => true,
			'loading'         => true,
			'referrerpolicy'  => true,
			'name'            => true,
		],
	];
	return wp_kses( $html, $allowed, [ 'https' ] );
}

/**
 * Click-to-load embed. The iframes sit in an inert <template> until the
 * visitor presses the button, so nothing third-party loads and no cookies
 * are set until they ask, the same as encore_video_player().
 *
 * @param string $html  Embed code, e.g. encore_setting( 'live_embed' ).
 * @param string $label Button text, also the iframe title if it has none.
 * @return string Markup, or '' if there's no usable iframe.
 */
function encore_embed( string $html, string $label ): string {
	$html = encore_kses_iframe( $html );
	if ( ! preg_match( '~<iframe\b[^>]*\bsrc="(https://[^"]+)"~i', $html, $m ) ) {
		return '';
	}
	$host = preg_replace( '/^www\./', '', (string) wp_parse_url( html_entity_decode( $m[1] ), PHP_URL_HOST ) );

	return sprintf(
		'<div class="embed"><template>%1$s</template><button type="button" class="btn btn--accent embed__load">%2$s</button><p class="embed__note">%3$s</p></div>',
		$html, // Filtered by encore_kses_iframe() above.
		esc_html( $label ),
		/* translators: %s: embed provider's domain, e.g. bandcamp.com. */
		esc_html( sprintf( __( 'Loads from %s, which may set its own cookies.', 'encore' ), $host ) )
	);
}

/**
 * Section heading + optional intro, used by most modules.
 */
function encore_section_head( string $title, string $intro = '', string $tag = 'h2' ): void {
	if ( '' === $title && '' === $intro ) {
		return;
	}
	$tag = in_array( $tag, [ 'h1', 'h2', 'h3' ], true ) ? $tag : 'h2';
	echo '<header class="section-head">';
	if ( '' !== $title ) {
		printf( '<%1$s class="section-head__title">%2$s</%1$s>', $tag, esc_html( $title ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tag whitelisted.
	}
	if ( '' !== $intro ) {
		echo '<div class="section-head__intro">' . wp_kses_post( wpautop( $intro ) ) . '</div>';
	}
	echo '</header>';
}

/**
 * Module arguments with defaults. Values the Setup Wizard seeds as an
 * image placeholder sentinel come through as ''.
 */
function encore_args( array $args, array $defaults ): array {
	$out = array_merge( $defaults, array_intersect_key( $args, $defaults ) );
	foreach ( $out as $key => $value ) {
		// '__drift_' is what the plugin's 1.x wizard seeded; both may be stored.
		if ( is_string( $value ) && ( 0 === strpos( $value, '__encore_' ) || 0 === strpos( $value, '__drift_' ) ) ) {
			$out[ $key ] = '';
		}
	}
	$out['_anchor'] = (string) ( $args['_anchor'] ?? '' );
	$out['_layout'] = (string) ( $args['_layout'] ?? '' );
	return $out;
}

/** Opening <section> for a module, with anchor id and layout class. */
function encore_module_open( array $a, string $extra_class = '' ): void {
	printf(
		'<section id="%1$s" class="module module--%2$s %3$s">',
		esc_attr( $a['_anchor'] ),
		esc_attr( str_replace( '_', '-', preg_replace( '/_module$/', '', $a['_layout'] ) ) ),
		esc_attr( $extra_class )
	);
}

/** Image ID from an ACF image value (ID, array or URL-less), else 0. */
function encore_image_id( $value ): int {
	if ( is_array( $value ) ) {
		return (int) ( $value['ID'] ?? $value['id'] ?? 0 );
	}
	return is_numeric( $value ) ? (int) $value : 0;
}
