<?php

echo '<div><strong>' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) ) ) . '</strong></div><span>';
// translators: %s placeholder is the username of the user.
echo ( empty( $post->post_author ) ? esc_html__( 'by a guest', 'my-listing' ) : sprintf( esc_html__( 'by %s', 'my-listing' ), '<a href="' . esc_url( add_query_arg( 'author', $post->post_author ) ) . '">' . esc_html( get_the_author() ) . '</a>' ) ) . '</span>';