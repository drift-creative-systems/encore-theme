<?php
/**
 * Streaming links — the band's profiles on each platform.
 *
 * @package Surface_Theme
 */
defined( 'ABSPATH' ) || exit;

$a     = surface_args( $args, [ 'section_title' => '' ] );
$links = surface_streaming_links();
if ( ! $links ) {
	return;
}

surface_module_open( $a, 'streaming' );
?>
	<div class="wrap streaming__inner">
		<?php surface_section_head( (string) $a['section_title'] ); ?>
		<?php echo surface_link_list( $links, 'link-list--large', __( 'Streaming', 'surface-theme' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</section>
