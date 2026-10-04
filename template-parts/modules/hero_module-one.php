<?php
/**
 * Hero — full-bleed opener. Blank fields fall back to Airtable settings:
 * title → Artist Name, text → Tagline, image → Hero Image, video → Hero Video URL.
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;

$a = encore_args( $args, [
	'section_title'    => '',
	'section_content'  => '',
	'background_image' => '',
	'show_video'       => true,
	'cta_label'        => '',
	'cta_url'          => '',
] );

$title = $a['section_title'] ?: encore_artist_name();
$text  = $a['section_content'] ?: (string) encore_setting( 'tagline' );
$image = encore_image_id( $a['background_image'] ) ?: (int) encore_setting( 'hero_image', 0 );
$video = $a['show_video'] ? (string) encore_setting( 'hero_video' ) : '';
$logo  = encore_logo( 'light', 'large', [ 'class' => 'hero__logo' ] );

// Buttons: the module's own, else next gig + latest release.
$buttons = [];
if ( $a['cta_label'] && $a['cta_url'] ) {
	$buttons[] = [ $a['cta_label'], $a['cta_url'], 'accent' ];
} else {
	$live = get_page_by_path( 'live' );
	if ( $live && encore_get_gigs( 'upcoming', 1 ) ) {
		$buttons[] = [ __( 'Live dates', 'encore' ), get_permalink( $live ), 'accent' ];
	}
	$latest = encore_get_latest_release();
	if ( $latest ) {
		/* translators: %s: release title. */
		$buttons[] = [ sprintf( __( 'Listen to %s', 'encore' ), $latest->post_title ), get_permalink( $latest ), 'ghost' ];
	}
}

encore_module_open( $a, 'hero' . ( $image || $video ? ' hero--media' : '' ) );
?>
	<div class="hero__media" aria-hidden="true">
		<?php
		if ( $video && preg_match( '/\.(mp4|webm)(\?|$)/i', $video ) ) {
			printf( '<video class="hero__video" src="%s" autoplay muted loop playsinline preload="metadata"></video>', esc_url( $video ) );
		} elseif ( $image ) {
			echo wp_get_attachment_image( $image, 'encore-hero', false, [ 'alt' => '', 'loading' => 'eager', 'fetchpriority' => 'high', 'class' => 'hero__image' ] );
		}
		?>
	</div>
	<div class="hero__content wrap">
		<?php if ( $logo && ! $a['section_title'] ) : ?>
			<h1 class="hero__title hero__title--logo"><?php echo $logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="screen-reader-text"><?php echo esc_html( $title ); ?></span></h1>
		<?php else : ?>
			<h1 class="hero__title"><?php echo esc_html( $title ); ?></h1>
		<?php endif; ?>
		<?php if ( $text ) : ?>
			<p class="hero__text"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>
		<?php if ( $buttons ) : ?>
			<p class="btn-row">
				<?php foreach ( $buttons as $b ) : ?>
					<a class="btn btn--<?php echo esc_attr( $b[2] ); ?> btn--large" href="<?php echo esc_url( $b[1] ); ?>"><?php echo esc_html( $b[0] ); ?></a>
				<?php endforeach; ?>
			</p>
		<?php endif; ?>
	</div>
</section>
