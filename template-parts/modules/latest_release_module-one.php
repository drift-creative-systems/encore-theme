<?php
/**
 * Latest release — one release, big. Picks the chosen release, else the
 * newest one ticked Featured in Airtable, else the newest.
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;

$a = encore_args( $args, [ 'section_title' => '', 'release' => 0 ] );

$release = $a['release'] ? get_post( is_object( $a['release'] ) ? $a['release']->ID : (int) $a['release'] ) : null;
$release = ( $release && 'publish' === $release->post_status ) ? $release : encore_get_latest_release();
if ( ! $release ) {
	return;
}

$meta    = encore_release_meta( $release->ID );
$links   = encore_release_links( $release->ID );
$presave = (string) get_post_meta( $release->ID, 'presave_url', true );
$label   = $a['section_title'] ?: ( $meta['future'] ? __( 'Coming soon', 'encore' ) : __( 'Out now', 'encore' ) );

encore_module_open( $a, 'latest-release' );
?>
	<div class="wrap latest-release__grid">
		<a class="latest-release__art" href="<?php echo esc_url( get_permalink( $release ) ); ?>" tabindex="-1" aria-hidden="true">
			<?php echo has_post_thumbnail( $release ) ? get_the_post_thumbnail( $release, 'encore-square', [ 'alt' => '' ] ) : '<span class="art-placeholder"></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
		<div class="latest-release__body">
			<p class="eyebrow-text"><?php echo esc_html( $label ); ?></p>
			<h2 class="latest-release__title"><a href="<?php echo esc_url( get_permalink( $release ) ); ?>"><?php echo esc_html( $release->post_title ); ?></a></h2>
			<p class="latest-release__meta">
				<?php
				echo esc_html( implode( ' / ', array_filter( [
					$meta['type'],
					$meta['future'] ? sprintf( /* translators: %s: date. */ __( 'released %s', 'encore' ), $meta['date']['full'] ) : $meta['year'],
				] ) ) );
				?>
			</p>
			<?php if ( $meta['future'] && $presave ) : ?>
				<p class="btn-row"><a class="btn btn--accent btn--large" href="<?php echo esc_url( $presave ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Pre-save', 'encore' ); ?></a></p>
			<?php endif; ?>
			<?php echo encore_link_list( $links, 'link-list--release', __( 'Listen on', 'encore' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</section>
