<?php
/**
 * Merch — items linking out to the band's own store.
 *
 * If the hub's Site Settings → Merch Embed holds iframe code (a Bandcamp
 * or shop widget), that replaces the synced grid, click-to-load.
 *
 * @package Surface_Theme
 */
defined( 'ABSPATH' ) || exit;

$a     = surface_args( $args, [ 'section_title' => '' ] );
$embed = surface_embed( (string) surface_setting( 'merch_embed' ), __( 'Show the shop', 'surface-theme' ) );
$items = '' === $embed ? surface_get_merch() : [];
if ( '' === $embed && ! $items ) {
	return;
}

surface_module_open( $a, 'merch' );
?>
	<div class="wrap">
		<?php surface_section_head( (string) $a['section_title'] ); ?>

		<?php if ( '' !== $embed ) : ?>
			<?php echo $embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Filtered and escaped in surface_embed(). ?>
		<?php else : ?>
			<ul class="merch-grid" role="list">
				<?php foreach ( $items as $item ) :
					$url   = (string) get_post_meta( $item->ID, 'store_url', true );
					$price = surface_price( get_post_meta( $item->ID, 'price', true ) );
					$badge = (string) get_post_meta( $item->ID, 'badge', true );
					$sold  = 'sold out' === strtolower( $badge );
					?>
					<li class="merch-item<?php echo $sold ? ' is-sold-out' : ''; ?>">
						<div class="merch-item__image">
							<?php echo has_post_thumbnail( $item ) ? get_the_post_thumbnail( $item, 'surface-square', [ 'alt' => '' ] ) : '<span class="art-placeholder" aria-hidden="true"></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php if ( $badge ) : ?><span class="merch-item__badge"><?php echo esc_html( $badge ); ?></span><?php endif; ?>
						</div>
						<h3 class="merch-item__name"><?php echo esc_html( $item->post_title ); ?></h3>
						<?php if ( $price ) : ?><p class="merch-item__price"><?php echo esc_html( $price ); ?></p><?php endif; ?>
						<?php if ( $url && ! $sold ) : ?>
							<a class="btn btn--ghost" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Buy', 'surface-theme' ); ?><span class="screen-reader-text"> <?php echo esc_html( $item->post_title ); ?></span></a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
