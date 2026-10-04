<?php
/**
 * Gallery — photos, optionally one album, with album filters and a lightbox.
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;

$a      = encore_args( $args, [ 'section_title' => '', 'album' => '', 'show_filters' => false, 'limit' => 0 ] );
$photos = encore_get_photos( (string) $a['album'], (int) $a['limit'] );
if ( ! $photos ) {
	return;
}
$albums = ( $a['show_filters'] && ! $a['album'] ) ? encore_get_albums() : [];

encore_module_open( $a, 'gallery' );
?>
	<div class="wrap">
		<?php encore_section_head( (string) $a['section_title'] ); ?>
		<?php if ( count( $albums ) > 1 ) : ?>
			<div class="filter" role="group" aria-label="<?php esc_attr_e( 'Filter by album', 'encore' ); ?>">
				<button type="button" class="filter__btn" aria-pressed="true" data-filter=""><?php esc_html_e( 'All', 'encore' ); ?></button>
				<?php foreach ( $albums as $album ) : ?>
					<button type="button" class="filter__btn" aria-pressed="false" data-filter="<?php echo esc_attr( $album->slug ); ?>"><?php echo esc_html( $album->name ); ?></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<ul class="photo-grid" role="list">
			<?php foreach ( $photos as $photo ) :
				$thumb = (int) get_post_thumbnail_id( $photo );
				if ( ! $thumb ) {
					continue;
				}
				$terms   = get_the_terms( $photo, 'encore_album' );
				$slugs   = ( $terms && ! is_wp_error( $terms ) ) ? implode( ' ', wp_list_pluck( $terms, 'slug' ) ) : '';
				$caption = trim( (string) $photo->post_excerpt );
				$credit  = (string) get_post_meta( $photo->ID, 'credit', true );
				$alt     = $caption ?: sprintf( /* translators: %s: artist. */ __( 'Photo of %s', 'encore' ), encore_artist_name() );
				?>
				<li class="photo" data-albums="<?php echo esc_attr( $slugs ); ?>">
					<a href="<?php echo esc_url( (string) wp_get_attachment_image_url( $thumb, 'encore-hero' ) ); ?>" class="photo__link" data-lightbox data-caption="<?php echo esc_attr( trim( $caption . ( $credit ? ' — ' . sprintf( /* translators: %s: photographer. */ __( 'Photo: %s', 'encore' ), $credit ) : '' ), ' —' ) ); ?>">
						<?php echo wp_get_attachment_image( $thumb, 'large', false, [ 'alt' => $alt ] ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
