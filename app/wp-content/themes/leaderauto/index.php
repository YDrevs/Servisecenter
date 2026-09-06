<?php
/**
 * Generic fallback template.
 *
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div class="page-shell">
	<div class="page-shell__inner">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class( 'entry' ); ?>>
					<h1 class="entry__title"><?php the_title(); ?></h1>
					<div class="entry__content"><?php the_content(); ?></div>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Нічого не знайдено.', 'leaderauto' ); ?></p>
		<?php endif; ?>
	</div>
</div>
<?php
get_footer();
