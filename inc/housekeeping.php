<?php
/**
 * housekeeping.php — front-end and admin cleanup. Deliberately left out:
 * an output-buffer that adds role="list" to every <ul>, an alt="" → alt=" "
 * rewrite (empty alt is correct for decorative images), and forcing
 * target="_blank" on every external link via JS.
 *
 * @package Surface_Theme
 */

defined( 'ABSPATH' ) || exit;

// Head cleanup.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );
add_filter( 'xmlrpc_enabled', '__return_false' );

// Classic editor everywhere; no block CSS on the front end.
add_filter( 'use_block_editor_for_post_type', '__return_false' );
add_action( 'wp_enqueue_scripts', static function () {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'global-styles' );
}, 100 );

// Comments off.
add_action( 'init', static function () {
	remove_post_type_support( 'post', 'comments' );
	remove_post_type_support( 'page', 'comments' );
}, 100 );
add_action( 'admin_menu', static fn() => remove_menu_page( 'edit-comments.php' ) );
add_action( 'wp_before_admin_bar_render', static function () {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu( 'comments' );
} );
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );

// Quieter dashboard.
add_action( 'wp_dashboard_setup', static function () {
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
} );
remove_action( 'welcome_panel', 'wp_welcome_panel' );

// Red bar on the admin bar while the site is hidden from search engines.
$surface_noindex_bar = static function () {
	if ( '0' === (string) get_option( 'blog_public' ) && is_admin_bar_showing() ) {
		echo '<style>#wpadminbar{border-top:4px solid #cf0000}</style>';
	}
};
add_action( 'admin_head', $surface_noindex_bar );
add_action( 'wp_head', $surface_noindex_bar );

// Uploads: WebP, and sanitised SVG.
add_filter( 'upload_mimes', static function ( array $mimes ): array {
	$mimes['webp'] = 'image/webp';
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['svg'] = 'image/svg+xml';
	}
	return $mimes;
} );
add_filter( 'wp_handle_upload_prefilter', static function ( array $file ): array {
	if ( 'image/svg+xml' !== ( $file['type'] ?? '' ) || ! class_exists( 'DOMDocument' ) ) {
		return $file;
	}
	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	if ( ! $dom->loadXML( (string) file_get_contents( $file['tmp_name'] ), LIBXML_NONET ) ) {
		$file['error'] = __( 'That SVG could not be read.', 'surface-theme' );
		return $file;
	}
	libxml_clear_errors();
	$tags  = [ 'svg', 'path', 'rect', 'circle', 'ellipse', 'polygon', 'polyline', 'line', 'g', 'defs', 'title' ];
	$attrs = [ 'width', 'height', 'viewBox', 'viewbox', 'fill', 'stroke', 'stroke-width', 'd', 'x', 'y', 'cx', 'cy', 'r', 'rx', 'ry', 'points', 'x1', 'x2', 'y1', 'y2', 'transform', 'xmlns', 'fill-rule', 'clip-rule', 'opacity' ];
	$walk  = static function ( DOMNode $node ) use ( &$walk, $tags, $attrs ) {
		foreach ( iterator_to_array( $node->childNodes ) as $child ) {
			if ( XML_ELEMENT_NODE !== $child->nodeType ) {
				continue;
			}
			if ( ! in_array( strtolower( $child->nodeName ), $tags, true ) ) {
				$node->removeChild( $child );
				continue;
			}
			foreach ( iterator_to_array( $child->attributes ) as $attr ) {
				if ( ! in_array( $attr->name, $attrs, true ) ) {
					$child->removeAttribute( $attr->name );
				}
			}
			$walk( $child );
		}
	};
	$walk( $dom );
	file_put_contents( $file['tmp_name'], $dom->saveXML() );
	return $file;
} );

// Featured image in RSS.
$surface_rss_image = static function ( $content ) {
	$post = get_post();
	return $post && has_post_thumbnail( $post ) ? get_the_post_thumbnail( $post, 'large' ) . $content : $content;
};
add_filter( 'the_excerpt_rss', $surface_rss_image );
add_filter( 'the_content_feed', $surface_rss_image );

// Light email obfuscation in post content (synced bios etc.).
add_filter( 'the_content', static function ( $content ) {
	return preg_replace_callback(
		'/(?<![\w@"\'=:\/])[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}(?![^<]*>)/',
		static fn( $m ) => antispambot( $m[0] ),
		(string) $content
	);
}, 20 );
