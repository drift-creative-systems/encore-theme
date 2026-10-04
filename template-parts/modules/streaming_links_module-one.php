<?php
/**
 * Streaming links — the band's profiles on each platform.
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;

$a     = encore_args( $args, [ 'section_title' => '' ] );
$links = encore_streaming_links();
if ( ! $links ) {
	return;
}

encore_module_open( $a, 'streaming' );
?>
	<div class="wrap streaming__inner">
		<?php encore_section_head( (string) $a['section_title'] ); ?>
		<?php echo encore_link_list( $links, 'link-list--large', __( 'Streaming', 'encore' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</section>
