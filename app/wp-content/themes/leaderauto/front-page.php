<?php
/**
 * Front page — Головна (home / service landing). Content mirrors reference page 10
 * (see app/reference/NOTES.md); demo imagery and source typos are dropped.
 *
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

get_template_part( 'template-parts/home/hero' );
get_template_part( 'template-parts/home/services' );
get_template_part( 'template-parts/home/why' );
get_template_part( 'template-parts/home/loyalty' );
get_template_part( 'template-parts/home/autopilot' );
get_template_part( 'template-parts/home/faq' );
get_template_part( 'template-parts/home/cta' );

get_footer();
