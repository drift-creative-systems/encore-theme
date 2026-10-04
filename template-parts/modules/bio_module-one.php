<?php
/**
 * Bio — Full Bio (or Short Bio) from Airtable, with an optional press-kit
 * download.
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;

$a = encore_args( $args, [ 'section_title' => '', 'use_short' => false, 'show_download' => false ] );

$bio = $a['use_short'] ? wpautop( esc_html( (string) encore_setting( 'bio_short' ) ) ) : (string) encore_setting( 'bio' );
if ( ! trim( wp_strip_all_tags( $bio ) ) ) {
	$bio = wpautop( esc_html( (string) encore_setting( 'bio_short' ) ) );
}
if ( ! trim( wp_strip_all_tags( $bio ) ) ) {
	return;
}
$kit = $a['show_download'] ? (int) encore_setting( 'press_kit', 0 ) : 0;

encore_module_open( $a, 'bio' );
?>
	<div class="wrap wrap--narrow">
		<?php encore_section_head( (string) $a['section_title'] ); ?>
		<div class="prose prose--lead"><?php echo wp_kses_post( $bio ); ?></div>
		<?php
		$meta = array_filter( [ (string) encore_setting( 'genre' ), (string) encore_setting( 'hometown' ) ] );
		if ( $meta ) :
			?>
			<p class="bio__meta"><?php echo esc_html( implode( ' / ', $meta ) ); ?></p>
		<?php endif; ?>
		<?php if ( $kit && wp_get_attachment_url( $kit ) ) : ?>
			<p class="btn-row"><a class="btn btn--ghost" href="<?php echo esc_url( (string) wp_get_attachment_url( $kit ) ); ?>" download><?php esc_html_e( 'Download press kit', 'encore' ); ?></a></p>
		<?php endif; ?>
	</div>
</section>
