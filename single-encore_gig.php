<?php
/**
 * One gig. MusicEvent JSON-LD is added in inc/schema.php.
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$encore_id     = get_the_ID();
	$encore_date   = encore_date_parts( (string) get_post_meta( $encore_id, 'gig_date', true ) );
	$encore_status = encore_gig_status( $encore_id );
	$encore_venue  = (string) get_post_meta( $encore_id, 'venue', true );
	$encore_city   = (string) get_post_meta( $encore_id, 'city', true );
	$encore_ctry   = (string) get_post_meta( $encore_id, 'country', true );
	$encore_tix    = (string) get_post_meta( $encore_id, 'ticket_url', true );
	$encore_doors  = (string) get_post_meta( $encore_id, 'doors', true );
	$encore_supp   = (string) get_post_meta( $encore_id, 'support', true );
	$encore_past   = $encore_date && $encore_date['iso'] < encore_today();
	?>
	<article class="gig-single status--<?php echo esc_attr( $encore_status['class'] ); ?><?php echo $encore_past ? ' is-past' : ''; ?>">
		<div class="wrap gig-single__grid">
			<?php if ( $encore_date ) : ?>
				<div class="gig-single__date" aria-hidden="true">
					<span class="gig-single__day"><?php echo esc_html( $encore_date['day'] ); ?></span>
					<span class="gig-single__month"><?php echo esc_html( $encore_date['month'] . ' ' . $encore_date['year'] ); ?></span>
				</div>
			<?php endif; ?>
			<div class="gig-single__body">
				<p class="eyebrow-text"><?php echo esc_html( encore_artist_name() ); ?></p>
				<h1 class="gig-single__title"><?php echo esc_html( $encore_venue ?: get_the_title() ); ?></h1>
				<p class="gig-single__where"><?php echo esc_html( implode( ', ', array_filter( [ $encore_city, $encore_ctry ] ) ) ); ?></p>
				<dl class="facts">
					<?php if ( $encore_date ) : ?>
						<dt><?php esc_html_e( 'Date', 'encore' ); ?></dt><dd><time datetime="<?php echo esc_attr( $encore_date['iso'] ); ?>"><?php echo esc_html( $encore_date['full'] ); ?></time></dd>
					<?php endif; ?>
					<?php if ( $encore_doors ) : ?>
						<dt><?php esc_html_e( 'Doors', 'encore' ); ?></dt><dd><?php echo esc_html( $encore_doors ); ?></dd>
					<?php endif; ?>
					<?php if ( $encore_supp ) : ?>
						<dt><?php esc_html_e( 'Support', 'encore' ); ?></dt><dd><?php echo esc_html( $encore_supp ); ?></dd>
					<?php endif; ?>
				</dl>
				<?php if ( $encore_past ) : ?>
					<p class="status-chip"><?php esc_html_e( 'This show has happened', 'encore' ); ?></p>
				<?php elseif ( $encore_tix && $encore_status['bookable'] ) : ?>
					<p class="btn-row"><a class="btn btn--accent btn--large" href="<?php echo esc_url( $encore_tix ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $encore_status['label'] ); ?></a></p>
				<?php else : ?>
					<p class="status-chip status-chip--<?php echo esc_attr( $encore_status['class'] ); ?>"><?php echo esc_html( 'on-sale' === $encore_status['class'] ? __( 'On sale', 'encore' ) : $encore_status['label'] ); ?></p>
				<?php endif; ?>
				<?php if ( get_the_content() ) : ?>
					<div class="prose"><?php the_content(); ?></div>
				<?php endif; ?>
				<?php
				$encore_live = get_page_by_path( 'live' );
				if ( $encore_live ) :
					?>
					<p><a class="text-link" href="<?php echo esc_url( get_permalink( $encore_live ) ); ?>">&larr; <?php esc_html_e( 'All dates', 'encore' ); ?></a></p>
				<?php endif; ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
