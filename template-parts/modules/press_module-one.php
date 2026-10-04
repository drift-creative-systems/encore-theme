<?php
/**
 * Press — quotes from reviews.
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;

$a     = encore_args( $args, [ 'section_title' => '', 'limit' => 0 ] );
$press = encore_get_press( (int) $a['limit'] );
if ( ! $press ) {
	return;
}

encore_module_open( $a, 'press' );
?>
	<div class="wrap">
		<?php encore_section_head( (string) $a['section_title'] ); ?>
		<div class="quote-grid">
			<?php foreach ( $press as $p ) :
				$author = (string) get_post_meta( $p->ID, 'author', true );
				$link   = (string) get_post_meta( $p->ID, 'link', true );
				$rating = (int) get_post_meta( $p->ID, 'rating', true );
				?>
				<figure class="quote">
					<?php if ( $rating > 0 ) : ?>
						<p class="quote__rating" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: stars. */ __( '%d out of 5 stars', 'encore' ), $rating ) ); ?>"><?php echo esc_html( str_repeat( '★', min( 5, $rating ) ) . str_repeat( '☆', max( 0, 5 - $rating ) ) ); ?></p>
					<?php endif; ?>
					<blockquote class="quote__text"><?php echo wp_kses_post( wpautop( wp_strip_all_tags( $p->post_content ) ) ); ?></blockquote>
					<figcaption class="quote__source">
						<?php if ( has_post_thumbnail( $p ) ) : ?>
							<?php echo get_the_post_thumbnail( $p, 'medium', [ 'alt' => esc_attr( $p->post_title ), 'class' => 'quote__logo' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php else : ?>
							<cite><?php echo esc_html( $p->post_title ); ?></cite>
						<?php endif; ?>
						<?php if ( $author ) : ?><span><?php echo esc_html( $author ); ?></span><?php endif; ?>
						<?php if ( $link ) : ?><a class="text-link" href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Read', 'encore' ); ?></a><?php endif; ?>
					</figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>
