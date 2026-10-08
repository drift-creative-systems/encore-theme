<?php
/**
 * Release card. $args['post'] (WP_Post).
 *
 * @package Surface_Theme
 */
defined( 'ABSPATH' ) || exit;

$release = $args['post'];
$meta    = surface_release_meta( $release->ID );
?>
<article class="release-card">
	<a class="release-card__link" href="<?php echo esc_url( get_permalink( $release ) ); ?>">
		<span class="release-card__art">
			<?php echo has_post_thumbnail( $release ) ? get_the_post_thumbnail( $release, 'surface-square', [ 'alt' => '' ] ) : '<span class="art-placeholder" aria-hidden="true"></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</span>
		<span class="release-card__title"><?php echo esc_html( $release->post_title ); ?></span>
		<span class="release-card__meta"><?php echo esc_html( implode( ' / ', array_filter( [ $meta['type'], $meta['future'] ? sprintf( /* translators: %s: date. */ __( 'out %s', 'surface-theme' ), $meta['date']['full'] ) : $meta['year'] ] ) ) ); ?></span>
	</a>
</article>
