<?php
/**
 * updates.php — self-update from GitHub releases (drift-creative-systems/encore-theme).
 *
 * To ship: bump Version in style.css, add a CHANGELOG entry, push to main,
 * then publish a GitHub Release tagged vX.Y.Z with encore-theme.zip
 * attached. Private repo: define ENCORE_GITHUB_TOKEN (or reuse
 * DRIFT_WEBSITE_GITHUB_TOKEN) in wp-config.php.
 *
 * @package Encore
 */

defined( 'ABSPATH' ) || exit;

$encore_puc = ENCORE_DIR . '/lib/plugin-update-checker/plugin-update-checker.php';
if ( ! is_readable( $encore_puc ) ) {
	return;
}
require_once $encore_puc;

if ( ! class_exists( '\YahnisElsts\PluginUpdateChecker\v5\PucFactory' ) ) {
	return;
}

$encore_updater = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
	'https://github.com/drift-creative-systems/encore-theme',
	ENCORE_DIR . '/style.css',
	get_template(),
	12
);
$encore_updater->setBranch( 'main' );

$encore_vcs = $encore_updater->getVcsApi();
if ( $encore_vcs && method_exists( $encore_vcs, 'enableReleaseAssets' ) ) {
	$encore_vcs->enableReleaseAssets();
}

$encore_token = defined( 'ENCORE_GITHUB_TOKEN' ) ? ENCORE_GITHUB_TOKEN : ( defined( 'DRIFT_WEBSITE_GITHUB_TOKEN' ) ? DRIFT_WEBSITE_GITHUB_TOKEN : '' );
if ( $encore_token ) {
	$encore_updater->setAuthentication( $encore_token );
}
unset( $encore_puc, $encore_updater, $encore_vcs, $encore_token );
