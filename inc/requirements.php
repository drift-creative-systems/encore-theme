<?php
/**
 * requirements.php — Encore and the Drift Website plugin ship as a pair.
 *
 * Without the plugin there is no band content, so instead of a half-empty
 * site:
 * - the front end serves a neutral "coming soon" holding page with a 503
 *   (search engines treat it as temporary and keep existing rankings);
 * - wp-admin shows a persistent notice with a one-click "Install & activate
 *   Drift Website" (downloads the latest GitHub release), or "Activate" when
 *   it's installed but switched off.
 *
 * The plugin does the same in reverse (drift-website/includes/
 * class-theme-check.php): it blocks its Setup Wizard until Encore is active.
 *
 * @package Encore
 */

defined( 'ABSPATH' ) || exit;

const ENCORE_PLUGIN_NAME = 'Drift Website';
const ENCORE_PLUGIN_ZIP  = 'https://github.com/drift-creative-systems/drift-website/releases/latest/download/drift-website.zip';

/**
 * Is the Drift Website plugin loaded?
 *
 * @return bool
 */
function encore_plugin_ready(): bool {
	return function_exists( 'drift_setting' );
}

/**
 * The installed plugin's file (e.g. drift-website/drift-website.php), or ''
 * if it isn't installed. Matched by name too, in case the folder was renamed.
 *
 * @return string
 */
function encore_plugin_file(): string {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	foreach ( get_plugins() as $file => $data ) {
		if ( 'drift-website.php' === basename( $file ) || ENCORE_PLUGIN_NAME === ( $data['Name'] ?? '' ) ) {
			return (string) $file;
		}
	}
	return '';
}

/* ── Front end: holding page ─────────────────────────────────────────── */

add_action( 'template_redirect', static function () {
	if ( encore_plugin_ready() || is_admin() || wp_doing_ajax() || wp_doing_cron() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return;
	}

	status_header( 503 );
	nocache_headers();
	header( 'Retry-After: 3600' );
	get_template_part( 'template-parts/holding' );
	exit; // The holding page is the whole response; nothing else may render.
}, 0 );

/* ── Admin: install / activate notice ────────────────────────────────── */

add_action( 'admin_notices', static function () {
	if ( encore_plugin_ready() || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$error = get_transient( 'encore_plugin_error' );
	if ( $error ) {
		delete_transient( 'encore_plugin_error' );
		echo '<div class="notice notice-error"><p>' . esc_html( (string) $error ) . '</p></div>';
	}

	$file = encore_plugin_file();
	if ( $file ) {
		/* translators: %s: plugin name. */
		$label = sprintf( __( 'Activate %s', 'encore' ), ENCORE_PLUGIN_NAME );
		$url   = wp_nonce_url( add_query_arg( [ 'action' => 'activate', 'plugin' => $file ], admin_url( 'plugins.php' ) ), 'activate-plugin_' . $file );
	} elseif ( current_user_can( 'install_plugins' ) ) {
		/* translators: %s: plugin name. */
		$label = sprintf( __( 'Install & activate %s', 'encore' ), ENCORE_PLUGIN_NAME );
		$url   = wp_nonce_url( admin_url( 'admin-post.php?action=encore_install_plugin' ), 'encore_install_plugin' );
	} else {
		$label = '';
		$url   = '';
	}

	echo '<div class="notice notice-error"><p><strong>' . esc_html__( 'Encore needs the Drift Website plugin.', 'encore' ) . '</strong> ';
	esc_html_e( 'The theme and plugin work as a pair: until it\'s active, visitors see a "coming soon" page.', 'encore' );
	if ( $url ) {
		echo ' <a class="button button-primary" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
	}
	echo '</p></div>';
} );

/**
 * Sends the user back with a one-time error for the notice.
 *
 * @param string $message Error shown to the user.
 */
function encore_install_plugin_fail( string $message ): void {
	error_log( 'Encore — Drift Website install failed: ' . $message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	set_transient( 'encore_plugin_error', $message, MINUTE_IN_SECONDS );
	wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'plugins.php' ) );
	exit;
}

add_action( 'admin_post_encore_install_plugin', static function () {
	check_admin_referer( 'encore_install_plugin' );

	if ( ! current_user_can( 'install_plugins' ) || ! current_user_can( 'activate_plugins' ) ) {
		wp_die( esc_html__( 'Insufficient permissions.', 'encore' ), '', [ 'response' => 403 ] );
	}

	$file = encore_plugin_file();
	if ( ! $file ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/misc.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		// FTP-credential hosts would need a form; tell them to upload instead.
		if ( 'direct' !== get_filesystem_method() ) {
			encore_install_plugin_fail( __( 'WordPress can\'t write to the plugins folder directly here. Upload drift-website.zip under Plugins → Add New instead.', 'encore' ) );
		}

		$upgrader = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );
		$result   = $upgrader->install( ENCORE_PLUGIN_ZIP );

		if ( is_wp_error( $result ) || ! $result ) {
			$message = is_wp_error( $result ) ? $result->get_error_message() : implode( ' ', (array) $upgrader->skin->get_error_messages() );
			/* translators: %s: error message. */
			encore_install_plugin_fail( sprintf( __( 'Drift Website couldn\'t be installed: %s', 'encore' ), $message ) );
		}

		wp_clean_plugins_cache();
		$file = encore_plugin_file();
		if ( ! $file ) {
			encore_install_plugin_fail( __( 'Drift Website downloaded but WordPress can\'t find it. Check the Plugins screen.', 'encore' ) );
		}
	}

	$activated = activate_plugin( $file );
	if ( is_wp_error( $activated ) ) {
		/* translators: %s: error message. */
		encore_install_plugin_fail( sprintf( __( 'Drift Website installed but couldn\'t be activated: %s', 'encore' ), $activated->get_error_message() ) );
	}

	// Straight on to connecting Airtable.
	wp_safe_redirect( admin_url( 'admin.php?page=drift-website' ) );
	exit;
} );
