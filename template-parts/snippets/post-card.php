<?php
/**
 * News card. $args['post'] (WP_Post).
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;

$item = $args['post'];
?>
<article class="post-card">
	<a class="post-card__link" href="<?php echo esc_url( get_permalink( $item ) ); ?>">
		<?php if ( has_post_thumbnail( $item ) ) : ?>
			<span class="post-card__image"><?php echo get_the_post_thumbnail( $item, 'encore-wide', [ 'alt' => '' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<?php endif; ?>
		<time class="post-card__date" datetime="<?php echo esc_attr( get_the_date( 'c', $item ) ); ?>"><?php echo esc_html( get_the_date( 'j M Y', $item ) ); ?></time>
		<span class="post-card__title"><?php echo esc_html( get_the_title( $item ) ); ?></span>
		<span class="post-card__excerpt"><?php echo esc_html( get_the_excerpt( $item ) ); ?></span>
	</a>
</article>
