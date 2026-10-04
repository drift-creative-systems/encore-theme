<?php
/**
 * News listing, archives, search — and the fallback for anything else.
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<div class="page-intro wrap">
	<h1 class="page-intro__title">
		<?php
		if ( is_home() ) {
			echo esc_html( get_the_title( (int) get_option( 'page_for_posts' ) ) ?: __( 'News', 'encore' ) );
		} elseif ( is_search() ) {
			/* translators: %s: search query. */
			printf( esc_html__( 'Results for "%s"', 'encore' ), esc_html( get_search_query() ) );
		} else {
			echo esc_html( wp_strip_all_tags( get_the_archive_title() ) );
		}
		?>
	</h1>
</div>
<div class="wrap">
	<?php if ( have_posts() ) : ?>
		<div class="card-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/snippets/post-card', null, [ 'post' => get_post() ] );
			endwhile;
			?>
		</div>
		<div class="pager"><?php the_posts_pagination( [ 'mid_size' => 1 ] ); ?></div>
	<?php else : ?>
		<p class="empty"><?php esc_html_e( 'Nothing here yet.', 'encore' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
