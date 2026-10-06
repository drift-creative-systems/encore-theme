<?php
/**
 * Contact — booking/enquiry form (saved to Airtable "Enquiries", copy to the
 * Booking Email) plus the band's contact addresses.
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;

$a = encore_args( $args, [ 'section_title' => '', 'section_content' => '', 'form' => 'enquiry', 'show_emails' => true ] );

$emails = array_filter( [
	__( 'Booking', 'encore' )    => (string) encore_setting( 'booking_email' ),
	__( 'Management', 'encore' ) => (string) encore_setting( 'management_email' ),
	__( 'Press', 'encore' )      => (string) encore_setting( 'press_email' ),
] );

encore_module_open( $a, 'contact' );
?>
	<div class="wrap contact__grid">
		<div class="contact__intro">
			<?php encore_section_head( (string) $a['section_title'], (string) $a['section_content'] ); ?>
			<?php if ( $a['show_emails'] && $emails ) : ?>
				<dl class="facts">
					<?php foreach ( $emails as $label => $email ) : ?>
						<dt><?php echo esc_html( $label ); ?></dt>
						<dd><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></dd>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>
		</div>

		<?php if ( encore_plugin_ready() ) : ?>
			<form class="encore-form contact__form" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" novalidate>
				<?php encore_form_hidden_fields( (string) $a['form'] ?: 'enquiry' ); ?>
				<input type="hidden" name="source_page" value="<?php echo esc_url( get_permalink() ?: home_url( '/' ) ); ?>">
				<div class="field-row">
					<label class="field">
						<span class="field__label"><?php esc_html_e( 'Name', 'encore' ); ?> <span aria-hidden="true">*</span></span>
						<input type="text" name="name" autocomplete="name" required>
					</label>
					<label class="field">
						<span class="field__label"><?php esc_html_e( 'Email', 'encore' ); ?> <span aria-hidden="true">*</span></span>
						<input type="email" name="email" autocomplete="email" required>
					</label>
				</div>
				<div class="field-row">
					<label class="field">
						<span class="field__label"><?php esc_html_e( 'Phone', 'encore' ); ?></span>
						<input type="tel" name="phone" autocomplete="tel">
					</label>
					<label class="field">
						<span class="field__label"><?php esc_html_e( 'What\'s it about?', 'encore' ); ?></span>
						<select name="enquiry_type">
							<?php foreach ( [ 'Booking', 'Festival', 'Wedding / private event', 'Press', 'Other' ] as $type ) : ?>
								<option><?php echo esc_html( $type ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				</div>
				<div class="field-row">
					<label class="field">
						<span class="field__label"><?php esc_html_e( 'Event date', 'encore' ); ?></span>
						<input type="text" name="event_date" placeholder="<?php esc_attr_e( 'dd/mm/yyyy', 'encore' ); ?>">
					</label>
					<label class="field">
						<span class="field__label"><?php esc_html_e( 'Location', 'encore' ); ?></span>
						<input type="text" name="location">
					</label>
				</div>
				<label class="field">
					<span class="field__label"><?php esc_html_e( 'Message', 'encore' ); ?> <span aria-hidden="true">*</span></span>
					<textarea name="message" rows="6" required></textarea>
				</label>
				<button type="submit" class="btn btn--accent btn--large"><?php esc_html_e( 'Send', 'encore' ); ?></button>
				<p class="form-status" role="status" aria-live="polite"></p>
			</form>
		<?php endif; ?>
	</div>
</section>
