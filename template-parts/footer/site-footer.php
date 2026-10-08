<?php
/**
 * Site footer: the name set large, socials, listening links, footer menu,
 * booking contact and credits.
 *
 * @package Surface_Theme
 */
defined( 'ABSPATH' ) || exit;

$surface_booking = (string) surface_setting( 'booking_email' );
?>
<footer class="site-footer">
	<div class="wrap">
		<p class="site-footer__name" aria-hidden="true"><?php echo esc_html( surface_artist_name() ); ?></p>

		<div class="site-footer__cols">
			<?php if ( surface_social_links() ) : ?>
				<div>
					<h2 class="site-footer__head"><?php esc_html_e( 'Follow', 'surface-theme' ); ?></h2>
					<?php echo surface_link_list( surface_social_links(), 'link-list--plain', __( 'Social media', 'surface-theme' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>
			<?php if ( surface_streaming_links() ) : ?>
				<div>
					<h2 class="site-footer__head"><?php esc_html_e( 'Listen', 'surface-theme' ); ?></h2>
					<?php echo surface_link_list( surface_streaming_links(), 'link-list--plain', __( 'Streaming', 'surface-theme' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>
			<?php if ( $surface_booking ) : ?>
				<div>
					<h2 class="site-footer__head"><?php esc_html_e( 'Booking', 'surface-theme' ); ?></h2>
					<p><a href="mailto:<?php echo esc_attr( antispambot( $surface_booking ) ); ?>"><?php echo esc_html( antispambot( $surface_booking ) ); ?></a></p>
				</div>
			<?php endif; ?>
			<?php if ( has_nav_menu( 'footer' ) ) : ?>
				<nav aria-label="<?php esc_attr_e( 'Footer', 'surface-theme' ); ?>">
					<?php wp_nav_menu( [ 'theme_location' => 'footer', 'container' => false, 'menu_class' => 'link-list link-list--plain', 'depth' => 1 ] ); ?>
				</nav>
			<?php endif; ?>
		</div>

		<div class="site-footer__base">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) . ' ' . surface_artist_name() ); ?></p>
			<p>
				<?php if ( surface_ga4_id() ) : ?>
					<button type="button" class="link-button" data-consent-open><?php esc_html_e( 'Cookie settings', 'surface-theme' ); ?></button>
				<?php endif; ?>
				<span>
					<?php
					printf(
						/* translators: %s: linked studio name. */
						esc_html__( 'Website by %s', 'surface-theme' ),
						'<a href="' . esc_url( 'https://driftcreativesystems.co.uk/' ) . '" target="_blank">' . esc_html__( 'Drift Creative Systems', 'surface-theme' ) . '</a>'
					);
					?>
				</span>
			</p>
		</div>
	</div>
</footer>
