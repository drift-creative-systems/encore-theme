<?php
/**
 * Site footer: the name set large, socials, listening links, footer menu,
 * booking contact and credits.
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;

$encore_booking = (string) encore_setting( 'booking_email' );
?>
<footer class="site-footer">
	<div class="wrap">
		<p class="site-footer__name" aria-hidden="true"><?php echo esc_html( encore_artist_name() ); ?></p>

		<div class="site-footer__cols">
			<?php if ( encore_social_links() ) : ?>
				<div>
					<h2 class="site-footer__head"><?php esc_html_e( 'Follow', 'encore' ); ?></h2>
					<?php echo encore_link_list( encore_social_links(), 'link-list--plain', __( 'Social media', 'encore' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>
			<?php if ( encore_streaming_links() ) : ?>
				<div>
					<h2 class="site-footer__head"><?php esc_html_e( 'Listen', 'encore' ); ?></h2>
					<?php echo encore_link_list( encore_streaming_links(), 'link-list--plain', __( 'Streaming', 'encore' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>
			<?php if ( $encore_booking ) : ?>
				<div>
					<h2 class="site-footer__head"><?php esc_html_e( 'Booking', 'encore' ); ?></h2>
					<p><a href="mailto:<?php echo esc_attr( antispambot( $encore_booking ) ); ?>"><?php echo esc_html( antispambot( $encore_booking ) ); ?></a></p>
				</div>
			<?php endif; ?>
			<?php if ( has_nav_menu( 'footer' ) ) : ?>
				<nav aria-label="<?php esc_attr_e( 'Footer', 'encore' ); ?>">
					<?php wp_nav_menu( [ 'theme_location' => 'footer', 'container' => false, 'menu_class' => 'link-list link-list--plain', 'depth' => 1 ] ); ?>
				</nav>
			<?php endif; ?>
		</div>

		<div class="site-footer__base">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) . ' ' . encore_artist_name() ); ?></p>
			<p>
				<?php if ( encore_ga4_id() ) : ?>
					<button type="button" class="link-button" data-consent-open><?php esc_html_e( 'Cookie settings', 'encore' ); ?></button>
				<?php endif; ?>
				<span>
					<?php
					printf(
						/* translators: %s: linked studio name. */
						esc_html__( 'Website by %s', 'encore' ),
						'<a href="' . esc_url( 'https://driftcreativesystems.co.uk/' ) . '" target="_blank">' . esc_html__( 'Drift Creative Systems', 'encore' ) . '</a>'
					);
					?>
				</span>
			</p>
		</div>
	</div>
</footer>
