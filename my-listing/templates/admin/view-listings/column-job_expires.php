<?php

if ( $post->_job_expires ) {
	echo '<strong>' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $post->_job_expires ) ) ) . '</strong>';
} else {
	echo '&mdash;';
}