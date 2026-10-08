<?php
/**
 * One gig as a row. $args['post'] (WP_Post), $args['past'] (bool).
 *
 * @package Surface_Theme
 */
defined( 'ABSPATH' ) || exit;

$gig    = $args['post'];
$past   = ! empty( $args['past'] );
$date   = surface_date_parts( (string) get_post_meta( $gig->ID, 'gig_date', true ) );
$status = surface_gig_status( $gig->ID );
$venue  = (string) get_post_meta( $gig->ID, 'venue', true ) ?: $gig->post_title;
$city   = (string) get_post_meta( $gig->ID, 'city', true );
$ctry   = (string) get_post_meta( $gig->ID, 'country', true );
$tix    = (string) get_post_meta( $gig->ID, 'ticket_url', true );
$supp   = (string) get_post_meta( $gig->ID, 'support', true );
$fest   = (bool) get_post_meta( $gig->ID, 'is_festival', true );
?>
<li class="gig status--<?php echo esc_attr( $status['class'] ); ?><?php echo $past ? ' is-past' : ''; ?>">
	<?php if ( $date ) : ?>
		<time class="gig__date" datetime="<?php echo esc_attr( $date['iso'] ); ?>">
			<span class="gig__weekday"><?php echo esc_html( $date['weekday'] ); ?></span>
			<span class="gig__day"><?php echo esc_html( $date['day'] ); ?></span>
			<span class="gig__month"><?php echo esc_html( $date['month'] ); ?><?php echo $date['year'] !== wp_date( 'Y' ) ? ' ' . esc_html( $date['year'] ) : ''; ?></span>
		</time>
	<?php endif; ?>
	<div class="gig__where">
		<a class="gig__venue" href="<?php echo esc_url( get_permalink( $gig ) ); ?>"><?php echo esc_html( $venue ); ?></a>
		<span class="gig__city"><?php echo esc_html( implode( ', ', array_filter( [ $city, 'UK' === strtoupper( $ctry ) || 'United Kingdom' === $ctry ? '' : $ctry ] ) ) ); ?></span>
		<?php if ( $supp || $fest ) : ?>
			<span class="gig__extra"><?php echo esc_html( implode( ' · ', array_filter( [ $fest ? __( 'Festival', 'surface-theme' ) : '', $supp ? sprintf( /* translators: %s: support act. */ __( 'with %s', 'surface-theme' ), $supp ) : '' ] ) ) ); ?></span>
		<?php endif; ?>
	</div>
	<div class="gig__action">
		<?php if ( $past ) : ?>
			<span class="status-chip"><?php esc_html_e( 'Played', 'surface-theme' ); ?></span>
		<?php elseif ( $tix && $status['bookable'] ) : ?>
			<a class="btn btn--accent" href="<?php echo esc_url( $tix ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $status['label'] ); ?><span class="screen-reader-text"> — <?php echo esc_html( $venue . ( $date ? ', ' . $date['full'] : '' ) ); ?></span></a>
		<?php else : ?>
			<span class="status-chip status-chip--<?php echo esc_attr( $status['class'] ); ?>"><?php echo esc_html( 'on-sale' === $status['class'] ? __( 'On sale', 'surface-theme' ) : $status['label'] ); ?></span>
		<?php endif; ?>
	</div>
</li>
