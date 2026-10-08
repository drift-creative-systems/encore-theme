<?php
/**
 * One release: artwork, links, tracklist. MusicAlbum JSON-LD in inc/schema.php.
 *
 * @package Surface_Theme
 */
defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$surface_id     = get_the_ID();
	$surface_meta   = surface_release_meta( $surface_id );
	$surface_links  = surface_release_links( $surface_id );
	$surface_tracks = surface_get_tracks( $surface_id );
	$surface_presave = (string) get_post_meta( $surface_id, 'presave_url', true );
	$surface_label  = (string) get_post_meta( $surface_id, 'label', true );
	?>
	<article class="release-single">
		<div class="wrap release-single__grid">
			<figure class="release-single__art">
				<?php
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'surface-square', [ 'loading' => 'eager', 'alt' => sprintf( /* translators: %s: release title. */ __( 'Artwork for %s', 'surface-theme' ), get_the_title() ) ] );
				} else {
					echo '<div class="art-placeholder" aria-hidden="true"></div>';
				}
				?>
			</figure>
			<div class="release-single__body">
				<p class="eyebrow-text"><?php echo esc_html( implode( ' / ', array_filter( [ $surface_meta['type'], $surface_meta['year'] ] ) ) ); ?></p>
				<h1 class="release-single__title"><?php the_title(); ?></h1>
				<?php if ( $surface_meta['future'] ) : ?>
					<p class="status-chip"><?php
						/* translators: %s: release date. */
						printf( esc_html__( 'Out %s', 'surface-theme' ), esc_html( $surface_meta['date']['full'] ) );
					?></p>
					<?php if ( $surface_presave ) : ?>
						<p class="btn-row"><a class="btn btn--accent btn--large" href="<?php echo esc_url( $surface_presave ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Pre-save', 'surface-theme' ); ?></a></p>
					<?php endif; ?>
				<?php endif; ?>
				<?php echo surface_link_list( $surface_links, 'link-list--release', __( 'Listen on', 'surface-theme' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped inside. ?>

				<?php if ( get_the_content() ) : ?>
					<div class="prose release-single__desc"><?php the_content(); ?></div>
				<?php endif; ?>

				<?php if ( $surface_tracks ) : ?>
					<h2 class="release-single__subhead"><?php esc_html_e( 'Tracklist', 'surface-theme' ); ?></h2>
					<ol class="tracklist">
						<?php foreach ( $surface_tracks as $surface_track ) :
							$surface_duration = (string) get_post_meta( $surface_track->ID, 'duration', true );
							$surface_lyrics   = trim( (string) $surface_track->post_content );
							?>
							<li class="tracklist__item">
								<?php if ( $surface_lyrics ) : ?>
									<details>
										<summary><span class="tracklist__title"><?php echo esc_html( $surface_track->post_title ); ?></span><?php if ( $surface_duration ) : ?><span class="tracklist__time"><?php echo esc_html( $surface_duration ); ?></span><?php endif; ?></summary>
										<div class="tracklist__lyrics prose"><?php echo wp_kses_post( $surface_lyrics ); ?></div>
									</details>
								<?php else : ?>
									<span class="tracklist__title"><?php echo esc_html( $surface_track->post_title ); ?></span>
									<?php if ( $surface_duration ) : ?><span class="tracklist__time"><?php echo esc_html( $surface_duration ); ?></span><?php endif; ?>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ol>
				<?php endif; ?>

				<?php if ( $surface_label ) : ?>
					<p class="release-single__label"><?php echo esc_html( $surface_label ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
