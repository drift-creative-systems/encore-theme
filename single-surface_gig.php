<?php
/**
 * One gig. MusicEvent JSON-LD is added in inc/schema.php.
 *
 * @package Surface_Theme
 */
defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$surface_id     = get_the_ID();
	$surface_date   = surface_date_parts( (string) get_post_meta( $surface_id, 'gig_date', true ) );
	$surface_status = surface_gig_status( $surface_id );
	$surface_venue  = (string) get_post_meta( $surface_id, 'venue', true );
	$surface_city   = (string) get_post_meta( $surface_id, 'city', true );
	$surface_ctry   = (string) get_post_meta( $surface_id, 'country', true );
	$surface_tix    = (string) get_post_meta( $surface_id, 'ticket_url', true );
	$surface_doors  = (string) get_post_meta( $surface_id, 'doors', true );
	$surface_supp   = (string) get_post_meta( $surface_id, 'support', true );
	$surface_past   = $surface_date && $surface_date['iso'] < surface_today();
	?>
	<article class="gig-single status--<?php echo esc_attr( $surface_status['class'] ); ?><?php echo $surface_past ? ' is-past' : ''; ?>">
		<div class="wrap gig-single__grid">
			<?php if ( $surface_date ) : ?>
				<div class="gig-single__date" aria-hidden="true">
					<span class="gig-single__day"><?php echo esc_html( $surface_date['day'] ); ?></span>
					<span class="gig-single__month"><?php echo esc_html( $surface_date['month'] . ' ' . $surface_date['year'] ); ?></span>
				</div>
			<?php endif; ?>
			<div class="gig-single__body">
				<p class="eyebrow-text"><?php echo esc_html( surface_artist_name() ); ?></p>
				<h1 class="gig-single__title"><?php echo esc_html( $surface_venue ?: get_the_title() ); ?></h1>
				<p class="gig-single__where"><?php echo esc_html( implode( ', ', array_filter( [ $surface_city, $surface_ctry ] ) ) ); ?></p>
				<dl class="facts">
					<?php if ( $surface_date ) : ?>
						<dt><?php esc_html_e( 'Date', 'surface-theme' ); ?></dt><dd><time datetime="<?php echo esc_attr( $surface_date['iso'] ); ?>"><?php echo esc_html( $surface_date['full'] ); ?></time></dd>
					<?php endif; ?>
					<?php if ( $surface_doors ) : ?>
						<dt><?php esc_html_e( 'Doors', 'surface-theme' ); ?></dt><dd><?php echo esc_html( $surface_doors ); ?></dd>
					<?php endif; ?>
					<?php if ( $surface_supp ) : ?>
						<dt><?php esc_html_e( 'Support', 'surface-theme' ); ?></dt><dd><?php echo esc_html( $surface_supp ); ?></dd>
					<?php endif; ?>
				</dl>
				<?php if ( $surface_past ) : ?>
					<p class="status-chip"><?php esc_html_e( 'This show has happened', 'surface-theme' ); ?></p>
				<?php elseif ( $surface_tix && $surface_status['bookable'] ) : ?>
					<p class="btn-row"><a class="btn btn--accent btn--large" href="<?php echo esc_url( $surface_tix ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $surface_status['label'] ); ?></a></p>
				<?php else : ?>
					<p class="status-chip status-chip--<?php echo esc_attr( $surface_status['class'] ); ?>"><?php echo esc_html( 'on-sale' === $surface_status['class'] ? __( 'On sale', 'surface-theme' ) : $surface_status['label'] ); ?></p>
				<?php endif; ?>
				<?php if ( get_the_content() ) : ?>
					<div class="prose"><?php the_content(); ?></div>
				<?php endif; ?>
				<?php
				$surface_live = get_page_by_path( 'live' );
				if ( $surface_live ) :
					?>
					<p><a class="text-link" href="<?php echo esc_url( get_permalink( $surface_live ) ); ?>">&larr; <?php esc_html_e( 'All dates', 'surface-theme' ); ?></a></p>
				<?php endif; ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
