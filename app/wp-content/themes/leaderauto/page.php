<?php
/**
 * Default page template (used by "Про нас" and any plain page).
 *
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'page-shell' ); ?>>
		<header class="page-shell__head">
			<div class="page-shell__inner">
				<h1 class="page-shell__title"><?php the_title(); ?></h1>
			</div>
		</header>
		<div class="page-shell__inner page-shell__body">
			<?php
			the_content();

			// When migrated content lives in a template-part matching the slug, pull it in.
			$slug = get_post_field( 'post_name', get_the_ID() );
			if ( $slug && locate_template( "template-parts/page-{$slug}.php" ) ) {
				get_template_part( 'template-parts/page', $slug );
			}
			?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
