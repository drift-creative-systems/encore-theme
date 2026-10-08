<?php
/**
 * Mailing list — signs up into the hub's "Subscribers" via the Drift: Surface plugin,
 * or links to the band's own signup page (Mailing List URL) if they use one.
 *
 * @package Surface_Theme
 */
defined( 'ABSPATH' ) || exit;

$a        = surface_args( $args, [ 'section_title' => '', 'section_content' => '' ] );
$external = (string) surface_setting( 'mailing_list_url' );
$can_form = surface_plugin_ready();
if ( ! $external && ! $can_form ) {
	return;
}

surface_module_open( $a, 'newsletter' );
?>
	<div class="wrap newsletter__inner">
		<?php surface_section_head( (string) ( $a['section_title'] ?: __( 'Mailing list', 'surface-theme' ) ), (string) ( $a['section_content'] ?: __( 'New music and tour dates, first. No spam.', 'surface-theme' ) ) ); ?>
		<?php if ( $external ) : ?>
			<p class="btn-row"><a class="btn btn--accent btn--large" href="<?php echo esc_url( $external ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Sign up', 'surface-theme' ); ?></a></p>
		<?php else : ?>
			<form class="surface-form newsletter__form" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" novalidate>
				<?php surface_form_hidden_fields( 'newsletter' ); ?>
				<label class="field">
					<span class="field__label"><?php esc_html_e( 'First name', 'surface-theme' ); ?></span>
					<input type="text" name="name" autocomplete="given-name">
				</label>
				<label class="field">
					<span class="field__label"><?php esc_html_e( 'Email', 'surface-theme' ); ?></span>
					<input type="email" name="email" autocomplete="email" required>
				</label>
				<button type="submit" class="btn btn--accent"><?php esc_html_e( 'Sign up', 'surface-theme' ); ?></button>
				<p class="form-status" role="status" aria-live="polite"></p>
			</form>
		<?php endif; ?>
	</div>
</section>
