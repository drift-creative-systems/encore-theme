<?php
/**
 * Videos — click-to-play, so YouTube/Vimeo load nothing until pressed.
 * The first video is shown large.
 *
 * @package Surface_Theme
 */
defined( 'ABSPATH' ) || exit;

$a      = surface_args( $args, [ 'section_title' => '', 'featured_only' => false, 'limit' => 0 ] );
$videos = surface_get_videos( (bool) $a['featured_only'], (int) $a['limit'] ?: ( $a['featured_only'] ? 1 : 0 ) );
if ( ! $videos && $a['featured_only'] ) {
	$videos = surface_get_videos( false, 1 );
}
if ( ! $videos ) {
	return;
}

surface_module_open( $a, 'videos' . ( 1 === count( $videos ) ? ' videos--single' : '' ) );
?>
	<div class="wrap">
		<?php surface_section_head( (string) $a['section_title'] ); ?>
		<div class="video-grid">
			<?php foreach ( $videos as $v ) :
				$player = surface_video_player( (string) get_post_meta( $v->ID, 'video_url', true ), $v->post_title, (int) get_post_thumbnail_id( $v ) );
				if ( ! $player ) {
					continue;
				}
				?>
				<figure class="video-item">
					<?php echo $player; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from escaped parts. ?>
					<figcaption><?php echo esc_html( $v->post_title ); ?></figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>
