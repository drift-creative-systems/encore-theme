<?php
/**
 * updates.php — self-update from GitHub releases (drift-creative-systems/surface-theme).
 *
 * To ship: bump Version in style.css, add a CHANGELOG entry, push to main,
 * then publish a GitHub Release tagged vX.Y.Z with surface-theme.zip
 * attached. Private repo: define SURFACE_THEME_GITHUB_TOKEN (or reuse the
 * plugin's DRIFT_SURFACE_GITHUB_TOKEN) in wp-config.php.
 *
 * @package Surface_Theme
 */

defined( 'ABSPATH' ) || exit;

$surface_puc = SURFACE_DIR . '/lib/plugin-update-checker/plugin-update-checker.php';
if ( ! is_readable( $surface_puc ) ) {
	return;
}
require_once $surface_puc;

if ( ! class_exists( '\YahnisElsts\PluginUpdateChecker\v5\PucFactory' ) ) {
	return;
}

$surface_updater = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
	'https://github.com/drift-creative-systems/surface-theme',
	SURFACE_DIR . '/style.css',
	get_template(),
	12
);
$surface_updater->setBranch( 'main' );

$surface_vcs = $surface_updater->getVcsApi();
if ( $surface_vcs && method_exists( $surface_vcs, 'enableReleaseAssets' ) ) {
	$surface_vcs->enableReleaseAssets();
}

$surface_token = '';
foreach ( [ 'SURFACE_THEME_GITHUB_TOKEN', 'DRIFT_SURFACE_GITHUB_TOKEN' ] as $surface_token_name ) {
	if ( defined( $surface_token_name ) && constant( $surface_token_name ) ) {
		$surface_token = (string) constant( $surface_token_name );
		break;
	}
}
unset( $surface_token_name );
if ( $surface_token ) {
	$surface_updater->setAuthentication( $surface_token );
}
unset( $surface_puc, $surface_updater, $surface_vcs, $surface_token );
