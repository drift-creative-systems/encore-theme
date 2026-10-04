<?php
/**
 * consent.php — optional Google Analytics 4 with cookie consent.
 *
 * Appearance → Customise → Analytics holds a GA4 Measurement ID. With none
 * set, the site sets no non-essential cookies (videos are click-to-play,
 * see encore_video_player()) and no banner shows. With one set, a consent
 * banner appears and GA only loads after "Accept" (assets/js/main.js).
 *
 * @package Encore
 */

defined( 'ABSPATH' ) || exit;

function encore_ga4_id(): string {
	$id = strtoupper( trim( (string) get_theme_mod( 'encore_ga4_id', '' ) ) );
	return preg_match( '/^G-[A-Z0-9]{4,20}$/', $id ) ? $id : '';
}

add_action( 'customize_register', static function ( WP_Customize_Manager $wp_customize ) {
	$wp_customize->add_section( 'encore_analytics', [
		'title'       => __( 'Analytics', 'encore' ),
		'description' => __( 'Optional. When set, visitors are asked for consent and Google Analytics only loads if they accept.', 'encore' ),
		'priority'    => 160,
	] );
	$wp_customize->add_setting( 'encore_ga4_id', [
		'default'           => '',
		'sanitize_callback' => static fn( $v ) => preg_match( '/^G-[A-Z0-9]{4,20}$/i', trim( (string) $v ) ) ? strtoupper( trim( (string) $v ) ) : '',
	] );
	$wp_customize->add_control( 'encore_ga4_id', [
		'label'       => __( 'GA4 Measurement ID', 'encore' ),
		'section'     => 'encore_analytics',
		'type'        => 'text',
		'input_attrs' => [ 'placeholder' => 'G-XXXXXXXXXX' ],
	] );
} );

/** The banner. Hidden until JS decides it's needed. */
add_action( 'wp_footer', static function () {
	if ( ! encore_ga4_id() ) {
		return;
	}
	?>
	<div class="consent" role="region" aria-label="<?php esc_attr_e( 'Cookie consent', 'encore' ); ?>" hidden>
		<p><?php esc_html_e( 'We\'d like to use analytics cookies to see how the site is used. Nothing else is tracked.', 'encore' ); ?></p>
		<div class="consent__actions">
			<button type="button" class="btn btn--accent" data-consent="granted"><?php esc_html_e( 'Accept', 'encore' ); ?></button>
			<button type="button" class="btn btn--ghost" data-consent="denied"><?php esc_html_e( 'No thanks', 'encore' ); ?></button>
		</div>
	</div>
	<?php
}, 5 );
