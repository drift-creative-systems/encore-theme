<?php
/**
 * Holding page, served with a 503 while the Drift Website plugin isn't
 * active (inc/requirements.php). Standalone on purpose: no wp_head(), so no
 * analytics, fonts or plugin output, and nothing that needs band data.
 *
 * @package Encore
 */

defined( 'ABSPATH' ) || exit;

$encore_name = get_bloginfo( 'name' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex">
	<title><?php echo esc_html( $encore_name ? $encore_name : __( 'Coming soon', 'encore' ) ); ?></title>
	<link rel="stylesheet" href="<?php echo esc_url( ENCORE_URI . '/assets/css/core/base.css?ver=' . encore_asset_version( 'assets/css/core/base.css' ) ); ?>">
	<link rel="stylesheet" href="<?php echo esc_url( ENCORE_URI . '/assets/css/core/holding.css?ver=' . encore_asset_version( 'assets/css/core/holding.css' ) ); ?>">
</head>
<body class="encore-holding">
	<main class="encore-holding__inner">
		<?php if ( $encore_name ) : ?>
			<h1 class="encore-holding__title"><?php echo esc_html( $encore_name ); ?></h1>
		<?php endif; ?>
		<p class="encore-holding__text"><?php esc_html_e( 'This site is being set up. Please check back soon.', 'encore' ); ?></p>
		<?php if ( current_user_can( 'activate_plugins' ) ) : ?>
			<p class="encore-holding__admin">
				<a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>"><?php esc_html_e( 'Finish setup: activate the Drift Website plugin', 'encore' ); ?></a>
			</p>
		<?php endif; ?>
	</main>
</body>
</html>
