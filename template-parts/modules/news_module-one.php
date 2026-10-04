<?php
/**
 * News — latest posts.
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;

$a    = encore_args( $args, [ 'section_title' => '', 'limit' => 3 ] );
$news = encore_get_news( (int) $a['limit'] ?: 3 );
if ( ! $news ) {
	return;
}

encore_module_open( $a, 'news' );
?>
	<div class="wrap">
		<?php encore_section_head( (string) $a['section_title'] ); ?>
		<div class="card-grid">
			<?php
			foreach ( $news as $item ) {
				get_template_part( 'template-parts/snippets/post-card', null, [ 'post' => $item ] );
			}
			?>
		</div>
	</div>
</section>
