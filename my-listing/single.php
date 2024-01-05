<?php get_header();

while( have_posts() ) {
	the_post();

	if ( get_post_type() === 'job_listing' ) {
		get_template_part( 'templates/listing' );
	} elseif ( get_post_type() === 'elementor_library' ) {
		the_content();
	} else {
		get_template_part( 'templates/content' );
	}

}

get_footer();