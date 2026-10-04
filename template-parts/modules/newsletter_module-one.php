<?php
/**
 * Mailing list — signs up into Airtable "Subscribers" via the Drift plugin,
 * or links to the band's own signup page (Mailing List URL) if they use one.
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;

$a        = encore_args( $args, [ 'section_title' => '', 'section_content' => '' ] );
$external = (string) encore_setting( 'mailing_list_url' );
$can_form = function_exists( 'drift_form_hidden_fields' );
if ( ! $external && ! $can_form ) {
	return;
}

encore_module_open( $a, 'newsletter' );
?>
	<div class="wrap newsletter__inner">
		<?php encore_section_head( (string) ( $a['section_title'] ?: __( 'Mailing list', 'encore' ) ), (string) ( $a['section_content'] ?: __( 'New music and tour dates, first. No spam.', 'encore' ) ) ); ?>
		<?php if ( $external ) : ?>
			<p class="btn-row"><a class="btn btn--accent btn--large" href="<?php echo esc_url( $external ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Sign up', 'encore' ); ?></a></p>
		<?php else : ?>
			<form class="drift-form newsletter__form" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" novalidate>
				<?php drift_form_hidden_fields( 'newsletter' ); ?>
				<label class="field">
					<span class="field__label"><?php esc_html_e( 'First name', 'encore' ); ?></span>
					<input type="text" name="name" autocomplete="given-name">
				</label>
				<label class="field">
					<span class="field__label"><?php esc_html_e( 'Email', 'encore' ); ?></span>
					<input type="email" name="email" autocomplete="email" required>
				</label>
				<button type="submit" class="btn btn--accent"><?php esc_html_e( 'Sign up', 'encore' ); ?></button>
				<p class="form-status" role="status" aria-live="polite"></p>
			</form>
		<?php endif; ?>
	</div>
</section>
