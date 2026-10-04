<?php
/**
 * One release: artwork, links, tracklist. MusicAlbum JSON-LD in inc/schema.php.
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$encore_id     = get_the_ID();
	$encore_meta   = encore_release_meta( $encore_id );
	$encore_links  = encore_release_links( $encore_id );
	$encore_tracks = encore_get_tracks( $encore_id );
	$encore_presave = (string) get_post_meta( $encore_id, 'presave_url', true );
	$encore_label  = (string) get_post_meta( $encore_id, 'label', true );
	?>
	<article class="release-single">
		<div class="wrap release-single__grid">
			<figure class="release-single__art">
				<?php
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'encore-square', [ 'loading' => 'eager', 'alt' => sprintf( /* translators: %s: release title. */ __( 'Artwork for %s', 'encore' ), get_the_title() ) ] );
				} else {
					echo '<div class="art-placeholder" aria-hidden="true"></div>';
				}
				?>
			</figure>
			<div class="release-single__body">
				<p class="eyebrow-text"><?php echo esc_html( implode( ' / ', array_filter( [ $encore_meta['type'], $encore_meta['year'] ] ) ) ); ?></p>
				<h1 class="release-single__title"><?php the_title(); ?></h1>
				<?php if ( $encore_meta['future'] ) : ?>
					<p class="status-chip"><?php
						/* translators: %s: release date. */
						printf( esc_html__( 'Out %s', 'encore' ), esc_html( $encore_meta['date']['full'] ) );
					?></p>
					<?php if ( $encore_presave ) : ?>
						<p class="btn-row"><a class="btn btn--accent btn--large" href="<?php echo esc_url( $encore_presave ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Pre-save', 'encore' ); ?></a></p>
					<?php endif; ?>
				<?php endif; ?>
				<?php echo encore_link_list( $encore_links, 'link-list--release', __( 'Listen on', 'encore' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped inside. ?>

				<?php if ( get_the_content() ) : ?>
					<div class="prose release-single__desc"><?php the_content(); ?></div>
				<?php endif; ?>

				<?php if ( $encore_tracks ) : ?>
					<h2 class="release-single__subhead"><?php esc_html_e( 'Tracklist', 'encore' ); ?></h2>
					<ol class="tracklist">
						<?php foreach ( $encore_tracks as $encore_track ) :
							$encore_duration = (string) get_post_meta( $encore_track->ID, 'duration', true );
							$encore_lyrics   = trim( (string) $encore_track->post_content );
							?>
							<li class="tracklist__item">
								<?php if ( $encore_lyrics ) : ?>
									<details>
										<summary><span class="tracklist__title"><?php echo esc_html( $encore_track->post_title ); ?></span><?php if ( $encore_duration ) : ?><span class="tracklist__time"><?php echo esc_html( $encore_duration ); ?></span><?php endif; ?></summary>
										<div class="tracklist__lyrics prose"><?php echo wp_kses_post( $encore_lyrics ); ?></div>
									</details>
								<?php else : ?>
									<span class="tracklist__title"><?php echo esc_html( $encore_track->post_title ); ?></span>
									<?php if ( $encore_duration ) : ?><span class="tracklist__time"><?php echo esc_html( $encore_duration ); ?></span><?php endif; ?>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ol>
				<?php endif; ?>

				<?php if ( $encore_label ) : ?>
					<p class="release-single__label"><?php echo esc_html( $encore_label ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
