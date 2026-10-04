<?php
/**
 * News posts (and any other single without its own template).
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'article' ); ?>>
		<header class="article__head wrap wrap--narrow">
			<p class="article__meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time></p>
			<h1 class="article__title"><?php the_title(); ?></h1>
		</header>
		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="article__image wrap"><?php the_post_thumbnail( 'encore-wide', [ 'loading' => 'eager' ] ); ?></figure>
		<?php endif; ?>
		<div class="article__body prose wrap wrap--narrow"><?php the_content(); ?></div>
		<footer class="article__foot wrap wrap--narrow">
			<?php
			$encore_news = get_post_type_archive_link( 'post' ) ?: home_url( '/news/' );
			$encore_page = get_page_by_path( 'news' );
			?>
			<a class="text-link" href="<?php echo esc_url( $encore_page ? get_permalink( $encore_page ) : $encore_news ); ?>">&larr; <?php esc_html_e( 'All news', 'encore' ); ?></a>
		</footer>
	</article>
	<?php
endwhile;

get_footer();
