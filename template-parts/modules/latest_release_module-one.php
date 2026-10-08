<?php
/**
 * Latest release — one release, big. Picks the chosen release, else the
 * newest one ticked Featured in the hub, else the newest.
 *
 * @package Surface_Theme
 */
defined( 'ABSPATH' ) || exit;

$a = surface_args( $args, [ 'section_title' => '', 'release' => 0 ] );

$release = $a['release'] ? get_post( is_object( $a['release'] ) ? $a['release']->ID : (int) $a['release'] ) : null;
$release = ( $release && 'publish' === $release->post_status ) ? $release : surface_get_latest_release();
if ( ! $release ) {
	return;
}

$meta    = surface_release_meta( $release->ID );
$links   = surface_release_links( $release->ID );
$presave = (string) get_post_meta( $release->ID, 'presave_url', true );
$label   = $a['section_title'] ?: ( $meta['future'] ? __( 'Coming soon', 'surface-theme' ) : __( 'Out now', 'surface-theme' ) );

surface_module_open( $a, 'latest-release' );
?>
	<div class="wrap latest-release__grid">
		<a class="latest-release__art" href="<?php echo esc_url( get_permalink( $release ) ); ?>" tabindex="-1" aria-hidden="true">
			<?php echo has_post_thumbnail( $release ) ? get_the_post_thumbnail( $release, 'surface-square', [ 'alt' => '' ] ) : '<span class="art-placeholder"></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
		<div class="latest-release__body">
			<p class="eyebrow-text"><?php echo esc_html( $label ); ?></p>
			<h2 class="latest-release__title"><a href="<?php echo esc_url( get_permalink( $release ) ); ?>"><?php echo esc_html( $release->post_title ); ?></a></h2>
			<p class="latest-release__meta">
				<?php
				echo esc_html( implode( ' / ', array_filter( [
					$meta['type'],
					$meta['future'] ? sprintf( /* translators: %s: date. */ __( 'released %s', 'surface-theme' ), $meta['date']['full'] ) : $meta['year'],
				] ) ) );
				?>
			</p>
			<?php if ( $meta['future'] && $presave ) : ?>
				<p class="btn-row"><a class="btn btn--accent btn--large" href="<?php echo esc_url( $presave ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Pre-save', 'surface-theme' ); ?></a></p>
			<?php endif; ?>
			<?php echo surface_link_list( $links, 'link-list--release', __( 'Listen on', 'surface-theme' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</section>
