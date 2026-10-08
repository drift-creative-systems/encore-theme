<?php
/**
 * Holding page, served with a 503 while the Drift: Surface plugin isn't
 * active (inc/requirements.php). Standalone on purpose: no wp_head(), so no
 * analytics, fonts or plugin output, and nothing that needs band data.
 *
 * @package Surface_Theme
 */

defined( 'ABSPATH' ) || exit;

$surface_name = get_bloginfo( 'name' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex">
	<title><?php echo esc_html( $surface_name ? $surface_name : __( 'Coming soon', 'surface-theme' ) ); ?></title>
	<link rel="stylesheet" href="<?php echo esc_url( SURFACE_URI . '/assets/css/core/base.css?ver=' . surface_asset_version( 'assets/css/core/base.css' ) ); ?>">
	<link rel="stylesheet" href="<?php echo esc_url( SURFACE_URI . '/assets/css/core/holding.css?ver=' . surface_asset_version( 'assets/css/core/holding.css' ) ); ?>">
</head>
<body class="surface-holding">
	<main class="surface-holding__inner">
		<?php if ( $surface_name ) : ?>
			<h1 class="surface-holding__title"><?php echo esc_html( $surface_name ); ?></h1>
		<?php endif; ?>
		<p class="surface-holding__text"><?php esc_html_e( 'This site is being set up. Please check back soon.', 'surface-theme' ); ?></p>
		<?php if ( current_user_can( 'activate_plugins' ) ) : ?>
			<p class="surface-holding__admin">
				<a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>"><?php esc_html_e( 'Finish setup: activate the Drift: Surface plugin', 'surface-theme' ); ?></a>
			</p>
		<?php endif; ?>
	</main>
</body>
</html>
