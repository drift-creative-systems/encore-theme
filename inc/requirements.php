<?php
/**
 * requirements.php — the Surface theme and the Drift: Surface plugin ship as a pair.
 *
 * Without the plugin there is no band content, so instead of a half-empty
 * site:
 * - the front end serves a neutral "coming soon" holding page with a 503
 *   (search engines treat it as temporary and keep existing rankings);
 * - wp-admin shows a persistent notice with a one-click "Install & activate
 *   Drift: Surface" (downloads the latest GitHub release), or "Activate" when
 *   it's installed but switched off.
 *
 * The plugin does the same in reverse (drift-surface/includes/
 * class-theme-check.php): it blocks its Setup Wizard until this theme is active.
 *
 * @package Surface_Theme
 */

defined( 'ABSPATH' ) || exit;

const SURFACE_PLUGIN_NAME = 'Drift: Surface';
const SURFACE_PLUGIN_ZIP  = 'https://github.com/drift-creative-systems/drift-surface/releases/latest/download/drift-surface.zip';

/**
 * Is the Drift: Surface plugin loaded?
 *
 * @return bool
 */
function surface_plugin_ready(): bool {
	return function_exists( 'drift_surface_setting' );
}

/**
 * The installed plugin's file (drift-surface/drift-surface.php), or '' if it
 * isn't installed. Matched by name too, in case the folder was renamed.
 *
 * @return string
 */
function surface_plugin_file(): string {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	foreach ( get_plugins() as $file => $data ) {
		if (
			'drift-surface.php' === basename( $file )
			|| SURFACE_PLUGIN_NAME === ( $data['Name'] ?? '' )
		) {
			return (string) $file;
		}
	}
	return '';
}

/* ── Front end: holding page ─────────────────────────────────────────── */

add_action( 'template_redirect', static function () {
	if ( surface_plugin_ready() || is_admin() || wp_doing_ajax() || wp_doing_cron() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
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
	if ( surface_plugin_ready() || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$error = get_transient( 'surface_plugin_error' );
	if ( $error ) {
		delete_transient( 'surface_plugin_error' );
		echo '<div class="notice notice-error"><p>' . esc_html( (string) $error ) . '</p></div>';
	}

	$file = surface_plugin_file();
	if ( $file ) {
		/* translators: %s: plugin name. */
		$label = sprintf( __( 'Activate %s', 'surface-theme' ), SURFACE_PLUGIN_NAME );
		$url   = wp_nonce_url( add_query_arg( [ 'action' => 'activate', 'plugin' => $file ], admin_url( 'plugins.php' ) ), 'activate-plugin_' . $file );
	} elseif ( current_user_can( 'install_plugins' ) ) {
		/* translators: %s: plugin name. */
		$label = sprintf( __( 'Install & activate %s', 'surface-theme' ), SURFACE_PLUGIN_NAME );
		$url   = wp_nonce_url( admin_url( 'admin-post.php?action=surface_install_plugin' ), 'surface_install_plugin' );
	} else {
		$label = '';
		$url   = '';
	}

	echo '<div class="notice notice-error"><p><strong>' . esc_html__( 'The Surface theme needs the Drift: Surface plugin.', 'surface-theme' ) . '</strong> ';
	esc_html_e( 'The theme and plugin work as a pair: until it\'s active, visitors see a "coming soon" page.', 'surface-theme' );
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
function surface_install_plugin_fail( string $message ): void {
	error_log( 'Surface theme — Drift: Surface install failed: ' . $message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	set_transient( 'surface_plugin_error', $message, MINUTE_IN_SECONDS );
	wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'plugins.php' ) );
	exit;
}

add_action( 'admin_post_surface_install_plugin', static function () {
	check_admin_referer( 'surface_install_plugin' );

	if ( ! current_user_can( 'install_plugins' ) || ! current_user_can( 'activate_plugins' ) ) {
		wp_die( esc_html__( 'Insufficient permissions.', 'surface-theme' ), '', [ 'response' => 403 ] );
	}

	$file = surface_plugin_file();
	if ( ! $file ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/misc.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		// FTP-credential hosts would need a form; tell them to upload instead.
		if ( 'direct' !== get_filesystem_method() ) {
			surface_install_plugin_fail( __( 'WordPress can\'t write to the plugins folder directly here. Upload drift-surface.zip under Plugins → Add New instead.', 'surface-theme' ) );
		}

		$upgrader = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );
		$result   = $upgrader->install( SURFACE_PLUGIN_ZIP );

		if ( is_wp_error( $result ) || ! $result ) {
			$message = is_wp_error( $result ) ? $result->get_error_message() : implode( ' ', (array) $upgrader->skin->get_error_messages() );
			/* translators: %s: error message. */
			surface_install_plugin_fail( sprintf( __( 'Drift: Surface couldn\'t be installed: %s', 'surface-theme' ), $message ) );
		}

		wp_clean_plugins_cache();
		$file = surface_plugin_file();
		if ( ! $file ) {
			surface_install_plugin_fail( __( 'Drift: Surface downloaded but WordPress can\'t find it. Check the Plugins screen.', 'surface-theme' ) );
		}
	}

	$activated = activate_plugin( $file );
	if ( is_wp_error( $activated ) ) {
		/* translators: %s: error message. */
		surface_install_plugin_fail( sprintf( __( 'Drift: Surface installed but couldn\'t be activated: %s', 'surface-theme' ), $activated->get_error_message() ) );
	}

	// Straight on to connecting the data source.
	wp_safe_redirect( admin_url( 'admin.php?page=drift-surface' ) );
	exit;
} );
