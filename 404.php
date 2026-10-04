<?php
/**
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<div class="wrap not-found">
	<p class="not-found__code" aria-hidden="true">404</p>
	<h1><?php esc_html_e( 'This page has left the stage.', 'encore' ); ?></h1>
	<p><?php esc_html_e( 'It may have moved, or the link may be old.', 'encore' ); ?></p>
	<p class="btn-row">
		<a class="btn btn--accent" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the home page', 'encore' ); ?></a>
	</p>
</div>
<?php
get_footer();
