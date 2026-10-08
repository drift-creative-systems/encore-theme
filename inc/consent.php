<?php
/**
 * consent.php — optional Google Analytics 4 with cookie consent.
 *
 * Appearance → Customise → Analytics holds a GA4 Measurement ID. With none
 * set, the site sets no non-essential cookies (videos are click-to-play,
 * see surface_video_player()) and no banner shows. With one set, a consent
 * banner appears and GA only loads after "Accept" (assets/js/main.js).
 *
 * @package Surface_Theme
 */

defined( 'ABSPATH' ) || exit;

function surface_ga4_id(): string {
	$id = strtoupper( trim( (string) get_theme_mod( 'surface_ga4_id', '' ) ) );
	return preg_match( '/^G-[A-Z0-9]{4,20}$/', $id ) ? $id : '';
}

add_action( 'customize_register', static function ( WP_Customize_Manager $wp_customize ) {
	$wp_customize->add_section( 'surface_analytics', [
		'title'       => __( 'Analytics', 'surface-theme' ),
		'description' => __( 'Optional. When set, visitors are asked for consent and Google Analytics only loads if they accept.', 'surface-theme' ),
		'priority'    => 160,
	] );
	$wp_customize->add_setting( 'surface_ga4_id', [
		'default'           => '',
		'sanitize_callback' => static fn( $v ) => preg_match( '/^G-[A-Z0-9]{4,20}$/i', trim( (string) $v ) ) ? strtoupper( trim( (string) $v ) ) : '',
	] );
	$wp_customize->add_control( 'surface_ga4_id', [
		'label'       => __( 'GA4 Measurement ID', 'surface-theme' ),
		'section'     => 'surface_analytics',
		'type'        => 'text',
		'input_attrs' => [ 'placeholder' => 'G-XXXXXXXXXX' ],
	] );
} );

/** The banner. Hidden until JS decides it's needed. */
add_action( 'wp_footer', static function () {
	if ( ! surface_ga4_id() ) {
		return;
	}
	?>
	<div class="consent" role="region" aria-label="<?php esc_attr_e( 'Cookie consent', 'surface-theme' ); ?>" hidden>
		<p><?php esc_html_e( 'We\'d like to use analytics cookies to see how the site is used. Nothing else is tracked.', 'surface-theme' ); ?></p>
		<div class="consent__actions">
			<button type="button" class="btn btn--accent" data-consent="granted"><?php esc_html_e( 'Accept', 'surface-theme' ); ?></button>
			<button type="button" class="btn btn--ghost" data-consent="denied"><?php esc_html_e( 'No thanks', 'surface-theme' ); ?></button>
		</div>
	</div>
	<?php
}, 5 );
