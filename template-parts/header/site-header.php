<?php
/**
 * Site header: name/logo, main menu, a tickets button when there are
 * upcoming gigs, and the mobile menu toggle.
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;

$encore_logo = encore_logo( 'light', 'medium' );
$encore_live = get_page_by_path( 'live' );
$encore_next = $encore_live ? encore_get_gigs( 'upcoming', 1 ) : [];
?>
<header class="site-header">
	<div class="site-header__inner wrap">
		<a class="site-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php if ( $encore_logo ) : ?>
				<?php echo $encore_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image(). ?>
			<?php else : ?>
				<span class="site-header__name"><?php echo esc_html( encore_artist_name() ); ?></span>
			<?php endif; ?>
		</a>

		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">
			<span class="nav-toggle__bars" aria-hidden="true"></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'encore' ); ?></span>
		</button>

		<nav id="site-nav" class="nav" aria-label="<?php esc_attr_e( 'Main', 'encore' ); ?>">
			<?php
			wp_nav_menu( [
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'nav__list',
				'depth'          => 2,
				'fallback_cb'    => 'encore_menu_fallback',
			] );
			?>
			<?php if ( $encore_next ) : ?>
				<a class="btn btn--accent nav__cta" href="<?php echo esc_url( get_permalink( $encore_live ) ); ?>"><?php esc_html_e( 'Tickets', 'encore' ); ?></a>
			<?php endif; ?>
		</nav>
	</div>
</header>
