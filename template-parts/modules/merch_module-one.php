<?php
/**
 * Merch — items linking out to the band's own store.
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;

$a     = encore_args( $args, [ 'section_title' => '' ] );
$items = encore_get_merch();
if ( ! $items ) {
	return;
}

encore_module_open( $a, 'merch' );
?>
	<div class="wrap">
		<?php encore_section_head( (string) $a['section_title'] ); ?>
		<ul class="merch-grid" role="list">
			<?php foreach ( $items as $item ) :
				$url   = (string) get_post_meta( $item->ID, 'store_url', true );
				$price = encore_price( get_post_meta( $item->ID, 'price', true ) );
				$badge = (string) get_post_meta( $item->ID, 'badge', true );
				$sold  = 'sold out' === strtolower( $badge );
				?>
				<li class="merch-item<?php echo $sold ? ' is-sold-out' : ''; ?>">
					<div class="merch-item__image">
						<?php echo has_post_thumbnail( $item ) ? get_the_post_thumbnail( $item, 'encore-square', [ 'alt' => '' ] ) : '<span class="art-placeholder" aria-hidden="true"></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php if ( $badge ) : ?><span class="merch-item__badge"><?php echo esc_html( $badge ); ?></span><?php endif; ?>
					</div>
					<h3 class="merch-item__name"><?php echo esc_html( $item->post_title ); ?></h3>
					<?php if ( $price ) : ?><p class="merch-item__price"><?php echo esc_html( $price ); ?></p><?php endif; ?>
					<?php if ( $url && ! $sold ) : ?>
						<a class="btn btn--ghost" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Buy', 'encore' ); ?><span class="screen-reader-text"> <?php echo esc_html( $item->post_title ); ?></span></a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
