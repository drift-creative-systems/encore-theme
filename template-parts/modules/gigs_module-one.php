<?php
/**
 * Gigs — the tour list. Upcoming first; past shows tucked into a
 * disclosure underneath when show_past is on.
 *
 * If the hub's Site Settings → Live Embed holds iframe code (a tour-dates
 * widget), that replaces the synced list, click-to-load.
 *
 * @package Surface_Theme
 */
defined( 'ABSPATH' ) || exit;

$a = surface_args( $args, [
	'section_title' => '',
	'limit'         => 0,
	'show_past'     => false,
	'empty_message' => '',
] );

$embed = surface_embed( (string) surface_setting( 'live_embed' ), __( 'Show tour dates', 'surface-theme' ) );

if ( '' === $embed ) {
	$limit    = (int) $a['limit'];
	$upcoming = surface_get_gigs( 'upcoming', $limit );
	$past     = $a['show_past'] ? surface_get_gigs( 'past', 20 ) : [];
	$live     = get_page_by_path( 'live' );
	$more     = $limit && $live && ! is_page( 'live' ) && count( surface_get_gigs( 'upcoming' ) ) > $limit;
}

surface_module_open( $a, 'gigs' );
?>
	<div class="wrap">
		<?php surface_section_head( (string) $a['section_title'] ); ?>

		<?php if ( '' !== $embed ) : ?>
			<?php echo $embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Filtered and escaped in surface_embed(). ?>
		<?php else : ?>

			<?php if ( $upcoming ) : ?>
				<ol class="gig-list" role="list">
					<?php
					foreach ( $upcoming as $gig ) {
						get_template_part( 'template-parts/snippets/gig-row', null, [ 'post' => $gig ] );
					}
					?>
				</ol>
			<?php else : ?>
				<p class="empty"><?php echo esc_html( $a['empty_message'] ?: __( 'No shows announced right now. Follow along to hear first.', 'surface-theme' ) ); ?></p>
				<?php echo surface_link_list( surface_social_links(), 'link-list--empty' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endif; ?>

			<?php if ( $more ) : ?>
				<p class="btn-row"><a class="btn btn--ghost" href="<?php echo esc_url( get_permalink( $live ) ); ?>"><?php esc_html_e( 'All dates', 'surface-theme' ); ?></a></p>
			<?php endif; ?>

			<?php if ( $past ) : ?>
				<details class="gig-past">
					<summary><?php
						/* translators: %d: number of past shows. */
						printf( esc_html( _n( '%d past show', '%d past shows', count( $past ), 'surface-theme' ) ), count( $past ) );
					?></summary>
					<ol class="gig-list gig-list--past" role="list">
						<?php
						foreach ( $past as $gig ) {
							get_template_part( 'template-parts/snippets/gig-row', null, [ 'post' => $gig, 'past' => true ] );
						}
						?>
					</ol>
				</details>
			<?php endif; ?>

		<?php endif; ?>
	</div>
</section>
