<?php

$field = $listing->get_field('location', true );
if ( $location = $listing->get_field( 'location' ) ) {
	printf(
		'<a class="google_map_link" href="%s" target="_blank">%s</a>',
		esc_url( 'http://maps.google.com/maps?q=' . rawurlencode( wp_strip_all_tags( $field->string_value('address') ) ) . '&zoom=14&size=512x512&maptype=roadmap&sensor=false' ),
		esc_html( wp_strip_all_tags( $field->string_value('address') ) )
	);
}