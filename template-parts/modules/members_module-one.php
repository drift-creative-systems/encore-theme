<?php
/**
 * Members — who's in the band.
 *
 * @package Encore
 */
defined( 'ABSPATH' ) || exit;

$a       = encore_args( $args, [ 'section_title' => '' ] );
$members = encore_get_members();
if ( ! $members ) {
	return;
}

encore_module_open( $a, 'members' );
?>
	<div class="wrap">
		<?php encore_section_head( (string) $a['section_title'] ); ?>
		<ul class="member-grid" role="list">
			<?php foreach ( $members as $m ) :
				$role = (string) get_post_meta( $m->ID, 'role', true );
				$insta = (string) get_post_meta( $m->ID, 'instagram', true );
				?>
				<li class="member">
					<div class="member__photo">
						<?php echo has_post_thumbnail( $m ) ? get_the_post_thumbnail( $m, 'encore-portrait', [ 'alt' => '' ] ) : '<span class="art-placeholder" aria-hidden="true"></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<h3 class="member__name"><?php echo esc_html( $m->post_title ); ?></h3>
					<?php if ( $role ) : ?><p class="member__role"><?php echo esc_html( $role ); ?></p><?php endif; ?>
					<?php if ( trim( $m->post_content ) ) : ?><div class="member__bio prose"><?php echo wp_kses_post( $m->post_content ); ?></div><?php endif; ?>
					<?php if ( $insta ) : ?><p><a class="text-link" href="<?php echo esc_url( $insta ); ?>" target="_blank" rel="noopener">Instagram</a></p><?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
