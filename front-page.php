<?php
/**

 * Front page (same as page.php; exists so a "latest posts" front page setting
 * still shows the band home). Pages: modules from the page builder, or the
 * product map's default modules for this page (inc/page-builder.php).
 *
 * @package Surface_Theme
 */
defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$surface_rows = surface_page_rows( get_the_ID() );

	if ( $surface_rows ) {
		surface_render_rows( $surface_rows );
	} else {
		// No modules anywhere: a plain page with its title and content.
		surface_render_rows( [
			[ 'acf_fc_layout' => 'page_header_module', 'section_title' => get_the_title() ],
			[ 'acf_fc_layout' => 'content_module', 'content' => get_the_content() ],
		] );
	}
endwhile;

get_footer();
