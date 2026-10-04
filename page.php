<?php
/**
 * Pages (including the front page): modules from the page builder, or the
 * product map's default modules for this page (inc/page-builder.php).
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$encore_rows = encore_page_rows( get_the_ID() );

	if ( $encore_rows ) {
		encore_render_rows( $encore_rows );
	} else {
		// No modules anywhere: a plain page with its title and content.
		encore_render_rows( [
			[ 'acf_fc_layout' => 'page_header_module', 'section_title' => get_the_title() ],
			[ 'acf_fc_layout' => 'content_module', 'content' => get_the_content() ],
		] );
	}
endwhile;

get_footer();
