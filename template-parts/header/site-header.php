<?php
/**
 * Site header: name/logo, main menu, a tickets button when there are
 * upcoming gigs, and the mobile menu toggle.
 *
 * @package Surface_Theme
 */
defined( 'ABSPATH' ) || exit;

$surface_logo = surface_logo( 'light', 'medium' );
$surface_live = get_page_by_path( 'live' );
$surface_next = $surface_live ? surface_get_gigs( 'upcoming', 1 ) : [];
?>
<header class="site-header">
	<div class="site-header__inner wrap">
		<a class="site-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php if ( $surface_logo ) : ?>
				<?php echo $surface_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image(). ?>
			<?php else : ?>
				<span class="site-header__name"><?php echo esc_html( surface_artist_name() ); ?></span>
			<?php endif; ?>
		</a>

		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">
			<span class="nav-toggle__bars" aria-hidden="true"></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'surface-theme' ); ?></span>
		</button>

		<nav id="site-nav" class="nav" aria-label="<?php esc_attr_e( 'Main', 'surface-theme' ); ?>">
			<?php
			wp_nav_menu( [
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'nav__list',
				'depth'          => 2,
				'fallback_cb'    => 'surface_menu_fallback',
			] );
			?>
			<?php if ( $surface_next ) : ?>
				<a class="btn btn--accent nav__cta" href="<?php echo esc_url( get_permalink( $surface_live ) ); ?>"><?php esc_html_e( 'Tickets', 'surface-theme' ); ?></a>
			<?php endif; ?>
		</nav>
	</div>
</header>
