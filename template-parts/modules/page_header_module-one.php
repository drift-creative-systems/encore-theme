<?php
/**
 * Page header — the inner-page opener. Image falls back to Hero Image.
 *
 * @package Surface_Theme
 */
defined( 'ABSPATH' ) || exit;

$a = surface_args( $args, [
	'section_title'    => '',
	'section_content'  => '',
	'background_image' => '',
] );

$image = surface_image_id( $a['background_image'] ) ?: (int) surface_setting( 'hero_image', 0 );

surface_module_open( $a, 'page-header' . ( $image ? ' page-header--media' : '' ) );
?>
	<?php if ( $image ) : ?>
		<div class="page-header__media" aria-hidden="true"><?php echo wp_get_attachment_image( $image, 'surface-hero', false, [ 'alt' => '', 'loading' => 'eager', 'fetchpriority' => 'high' ] ); ?></div>
	<?php endif; ?>
	<div class="page-header__content wrap">
		<h1 class="page-header__title"><?php echo esc_html( $a['section_title'] ?: get_the_title() ); ?></h1>
		<?php if ( $a['section_content'] ) : ?>
			<p class="page-header__text"><?php echo esc_html( $a['section_content'] ); ?></p>
		<?php endif; ?>
	</div>
</section>
