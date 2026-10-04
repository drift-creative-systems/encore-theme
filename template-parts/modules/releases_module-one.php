<?php
/**
 * Releases — the discography grid, newest first.
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;

$a = encore_args( $args, [ 'section_title' => '', 'display' => 'grid', 'type' => '', 'limit' => 0 ] );

$releases = encore_get_releases( (int) $a['limit'], (string) $a['type'] );
if ( ! $releases ) {
	return;
}

encore_module_open( $a, 'releases releases--' . sanitize_html_class( (string) $a['display'] ) );
?>
	<div class="wrap">
		<?php encore_section_head( (string) $a['section_title'] ); ?>
		<div class="release-grid">
			<?php
			foreach ( $releases as $release ) {
				get_template_part( 'template-parts/snippets/release-card', null, [ 'post' => $release ] );
			}
			?>
		</div>
	</div>
</section>
