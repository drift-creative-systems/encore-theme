<?php
/**
 * Content — free text, optionally beside an image.
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;

$a = encore_args( $args, [ 'section_title' => '', 'content' => '', 'image' => '', 'image_position' => 'none' ] );

$image    = encore_image_id( $a['image'] );
$position = $image && in_array( $a['image_position'], [ 'left', 'right' ], true ) ? $a['image_position'] : 'none';
if ( '' === trim( wp_strip_all_tags( (string) $a['content'] ) ) && ! $a['section_title'] && ! $image ) {
	return;
}

encore_module_open( $a, 'content content--image-' . $position );
?>
	<div class="wrap <?php echo 'none' === $position ? 'wrap--narrow' : 'content__grid'; ?>">
		<?php if ( 'none' !== $position ) : ?>
			<figure class="content__image"><?php echo wp_get_attachment_image( $image, 'encore-portrait', false, [ 'alt' => '' ] ); ?></figure>
		<?php endif; ?>
		<div class="content__body">
			<?php encore_section_head( (string) $a['section_title'] ); ?>
			<div class="prose"><?php echo wp_kses_post( wpautop( (string) $a['content'] ) ); ?></div>
		</div>
	</div>
</section>
